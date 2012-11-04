<?php

require_once( 'dblib.php' );
require_once( 'dbfuncs_boxoffice.php' );
require_once( 'dbfuncs_carbonpop.php' );


function showTable( $tbl, $name = NULL ) {
    $newln = "\n";
    
    if ($tbl == NULL) {
        return $dbError;
    }

    $cols = array_keys( $tbl[ 0 ] );

    $output = ($name == NULL ? '' : '<STRONG>Table Name: ' . $name . '</STRONG><BR /><BR />' . $newln);
    $output .= '<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1">' . $newln .
               '<TR>' . $newln;
    foreach ($cols as $col) {
        $output .= '<TD><EM>' . $col . '</EM></TD>';
    }
    $output .= '</TR>';
    foreach ($tbl as $row) {
        $output .= '<TR>' . $newln;
        foreach ($cols as $col) {
            $output .= '<TD>' . $row[ $col ] . '</TD>';
        }
        $output .= '</TR>' . $newln;
    }
    $output .= '</TABLE>' . $newln;

    return $output;
}

$newln = "\n";
echo '<html><body>' . $newln;

$defaultCxn = 'boxoffice';

dbConnect();
$sql = '  SELECT Shows.Name AS ShowName, CarbonpopShowingID ' .
       '    FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
       '   WHERE PerformanceDate = ? AND CarbonpopShowingID IS NOT NULL ' .
       'ORDER BY PerformanceTime';
$params = array( '2011-07-24' );

$performances = dbGetResult( $sql, $params );
dbDisconnect();

$defaultCxn = 'carbonpop';

dbConnect();
$n = count( $performances );
for ($i = 0; $i < $n; $i++) {    
    $performances[ $i ][ 'Presales' ] = getPresalesFromCPop( $performances[ $i ][ 'CarbonpopShowingID' ] );
}
//print_r( $sql, $performances );
dbDisconnect();
 
foreach ($performances as $p) {
    echo '<STRONG>' . $p[ 'ShowName' ] . ':</STRONG><BR />' . $newln;
    if (count( $p[ 'Presales' ] ) > 0) {
        echo '<UL>' . showTable( $p[ 'Presales' ] ) . '</UL>' . $newln;
    } else {
        echo '<BR />' . $newln;
    }
}

echo '</body></html>' . $newln;

?>
