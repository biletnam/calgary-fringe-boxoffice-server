<?php

// This needs to come before the first inclusion of dblib.php, which references the current date/time.
date_default_timezone_set( 'Canada/Mountain' ) ;

require_once( 'dblib.php' );
require_once( 'dbfuncs_boxoffice.php' );


$demotimes = array(
    array( 255, 13, '18:30', '18:50', '17:50' ),
    array( 260, 4,  '19:45', '20:05', '19:05' ),
    array( 270, 4,  '17:45', '18:05', '17:05' ),
    array( 273, 5,  '21:00', '21:20', '20:20' ),
    array( 257, 2,  '23:30', '23:50', '22:50' ),
    array( 252, 4,  '15:45', '16:05', '15:05' ),
    array( 243, 2,  '17:00', '17:20', '16:20' ),
    array( 244, 13, '16:30', '16:50', '15:50' ),
    array( 244, 13, '20:30', '20:50', '19:50' ),
    array( 245, 2,  '21:00', '21:20', '20:20' )
);

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

function insertWindows( $pdate ) {
    global $dbLastInsert;

    $wids = array();

    $sql = 'INSERT INTO Windows( VenueID, Day, OpensAt, ClosesAt, StartingButtons, StartingFloat ) ' .
           'VALUES( ?, ?, ?, ?, ?, ? )';
    $params = array( 1, $pdate, '06:00:00', '23:59:59', 100, 30000 );
    
    dbUpdate( $sql, $params );
    $wid = $dbLastInsert;
    if ($wid === NULL) {
        return false;
    }
    $wids[ 1 ] = $wid;

    foreach (array( 2, 4, 5, 13 ) as $venue) {
        $params = array( $venue, $pdate, '06:00:00', '23:59:59', 40, 10000 );
        dbUpdate( $sql, $params );
        $wid = $dbLastInsert;
        if ($wid === NULL) {
            return false;
        }
        $wids[ $venue ] = $wid;
    }

    return $wids;
}

function insertPerformance( $pdate, $wids, $showid, $venueid, $ptime, $vsaletime, $isaletime ) {
    global $dbLastInsert;
    global $dbError;
    
    switch ($venueid) {
        case 2:
            $itix = 12; $vtix = 85;
            break;
        case 4:
            $itix = 20; $vtix = 133;
            break;
        case 5:
            $itix = 12; $vtix = 79;
            break;
        case 6:
            $itix = 15; $vtix = 102;
            break;
        default:
            $itix = 10; $vtix = 70;
            break;
    }

    $sql = 'INSERT INTO Performances( ShowID, VenueID, PerformanceDate, PerformanceTime, CarbonpopShowingID ) ' .
           'VALUES( ?, ?, ?, ?, ? )';
    $params = array( $showid, $venueid, $pdate, $ptime, 0 );
    if (dbUpdate( $sql, $params ) === false) {
        return false;
    }

    $pid = $dbLastInsert;
    if ($pid === NULL) {
        return false;
    }

    $sql = 'INSERT INTO PerformanceWindows( PerformanceID, WindowID, StartingTickets, SalesStopAt, IsHomeVenue ) ' .
           'VALUES( ?, ?, ?, ?, ? )';

    $params = array( $pid, $wids[ 1 ], $itix, $isaletime, 'N' );
    if (dbUpdate( $sql, $params ) === false) {
        return false;
    }

    $params = array( $pid, $wids[ $venueid ], $vtix, $vsaletime, 'Y' );
    if (dbUpdate( $sql, $params ) === false) {
        return false;
    }

    return true;
}

?>
<HTML>
    <HEAD>
        <TITLE>Calgary Fringe Festival - Box Office (Demo Version)</TITLE>
    </HEAD>
    <BODY>
        <H1><IMG SRC="fringe_logo_100x100_trans.png" ALIGN="absmiddle" /> Calgary Fringe Festival - Box Office (Demo Version)</H1>
        <DIV STYLE="border: 1px solid black; width: 1200px; height: 80%;">
<?php

$newln = "\n";

$defaultCxn = 'boxoffice';
$setupOk = false;
$errmsg = 'No error message here. Oops, Sean screwed up somewhere. (403) 397-7669.';

if (! dbConnect()) {
    $errmsg = 'Database connection error: ' . $dbError;
} else {
    $pdate = date( 'Y-m-d' );
    
    $sql = 'SELECT Count(*) AS NumWindows FROM Windows WHERE Day = ?';
    $params = array( $pdate );
    $res = dbGetResult( $sql, $params );
    if ($res === false) {
        $errmsg = 'Database error: ' .$dbError;
    } else { 
        $setupOk = true;

        $n = $res[ 0 ][ 'NumWindows' ];
        if ($n <= 0) {
            $setupOk = true;

            $wids = insertWindows( $pdate );
            if ($wids === false) {
                $errmsg = 'Error setting up today\'s sale windows: ' . $dbError;
                $setupOk = false;
            } else {
                foreach ($demotimes as $detail) {
                    list( $showid, $venueid, $ptime, $vsaletime, $isaletime ) = $detail;
                    if (! insertPerformance( $pdate, $wids, $showid, $venueid, $ptime . ':00',
                                             $vsaletime . ':00', $isaletime . ':00' )) {

                        $errmsg = 'Error adding performance ' . $showid . ': ' . $dbError;
                        $setupOk = false;
                        break;
                    }
                }
            }
        }
    }
//} else {
//    $setupOk = true;
}
dbDisconnect();

if ($setupOk) {

?>
        <APPLET WIDTH="100%" HEIGHT="100%" CODE="BoxOffice.class" ARCHIVE="BoxOffice.jar">
            <PARAM NAME="cache_option" VALUE="no">
            <PARAM NAME="version_major" VALUE="0">
            <PARAM NAME="version_minor" VALUE="7">
            <PARAM NAME="version_revision" VALUE="27">
        </APPLET>
<?php

} else {
    echo '        <P><FONT COLOR="red">The following problem occurred...<BR /><BR />' . $errmsg . '</FONT></P>' . $newln;
}

?>
        </DIV>
    </BODY>
</HTML>