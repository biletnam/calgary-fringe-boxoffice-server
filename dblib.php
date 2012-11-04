<?php

$dbConns = array();
$dbParams = array();
$defaultCxn = '';

$dbError = NULL;
$dbLastInsert = NULL;

$dbConns[ $defaultCxn ] = NULL;
$dbParams[ $defaultCxn ] = array(
    'host' => NULL,
    'name' => NULL,
    'user' => NULL,
    'pass' => NULL
);

$curtime = date( 'H:i:s' );
$curdate = date( 'Y-m-d' );


function dbInitParams( $cxn = NULL ) {
    global $dbParams;
    
    if ($cxn == NULL) {
        return false;
    }
    
    // The credentials file contains definitions for $dbhost, $dbname, $dbuser and $dbpass
    
    $filename = 'credentials.' . $cxn . '.php';
    include( $filename );
    
    $dbParams[ $cxn ] = array(
        'host' => $dbhost,
        'name' => $dbname,
        'user' => $dbuser,
        'pass' => $dbpass
    );
    
    return true;
}

function dbConnect( $cxn = NULL ) {
    global $dbConns;
    global $dbParams;
    global $defaultCxn;
    global $dbError;

    if ($cxn == NULL) {
        $cxn = $defaultCxn;
    }
    
    $dbhost = $dbParams[ $cxn ][ 'host' ];
    $dbname = $dbParams[ $cxn ][ 'name' ];
    $dbuser = $dbParams[ $cxn ][ 'user' ];
    $dbpass = $dbParams[ $cxn ][ 'pass' ];

    $connstr = 'mysql:host=' . $dbhost . ';dbname=' . $dbname . ';';
    $connopt = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION
    );

    try {
        $dbConns[ $cxn ] = new PDO( $connstr, $dbuser, $dbpass, $connopt );
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
        $dbConns[ $cxn ] = NULL;
        return false;
    }

    return true;
}

function dbDisconnect( $cxn = NULL ) {
    global $dbConns;
    global $defaultCxn;

    if ($cxn == NULL) {
        $cxn = $defaultCxn;
    }

    $dbConns[ $cxn ] = NULL;
}

function dbSafeUserPass( $str ) {
    return preg_replace( '/\W/', '', $str );
}

function dbSafeInt( $id ) {
    return preg_replace( '/[^0-9\-]/', '', $id );
}

function dbSafeBool( $b ) {
    $b = strtolower( $b );
    if (($b == 1) || ($b == -1) ||
        ($b == 'y') || ($b == 'yes') ||
        ($b == 't') || ($b == 'true')) {

        return '\'Y\'';
    }

    return '\'N\'';
}

function dbSafeEnum( $needle, $haystack = array(), $default = NULL ) {
    if (in_array( $needle, $haystack, true )) {
        return $needle;
    } else {
        foreach ($haystack as $hay) {
            if (strcasecmp( $needle, $hay ) == 0) {
                return $hay;
            }
        }
    }

    return $default;
}

function dbTruncateText( $txt, $maxlen ) {
    if ($txt == NULL) {
        return NULL;
    }
    
    return (strlen( $txt ) > $maxlen ? substr( $txt, 0, $maxlen ) : $txt);
}

function dbGetResult( $sql, $params = NULL, $cxn = NULL ) {
    global $dbConns;
    global $defaultCxn;
    global $dbError;

    if ($cxn == NULL) {
        $cxn = $defaultCxn;
    }

    if ($dbConns[ $cxn ] == NULL) {
        if (! dbConnect( $cxn )) {
            return false;
        }
    }

    try {
        $stmt = $dbConns[ $cxn ]->prepare( $sql );
        if (! $stmt->execute( $params )) {
            $stmt->closeCursor();
            return false;
        }
        $rst = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
        return false;
    }

    if (count( $rst ) == 0) {
        $rows = false;
    } else {
        $rows = $rst;
    }

    return $rows;
}

function dbUpdate( $sql, $params = NULL, $cxn = NULL ) {
    return (dbUpdateMulti( $sql, array( $params ), $cxn ) == 0 ? false : true);
}

function dbUpdateMulti( $sql, $paramslist = array( NULL ), $cxn = NULL ) {
    global $dbConns;
    global $defaultCxn;
    global $dbError;
    global $dbLastInsert;

    if ($cxn == NULL) {
        $cxn = $defaultCxn;
    }

    if ($dbConns[ $cxn ] == NULL) {
        if (! dbConnect( $cxn )) {
            return 0;
        }
    }

    $n = 0;
    $dbLastInsert = NULL;
    try {
        $stmt = $dbConns[ $cxn ]->prepare( $sql );
        foreach ($paramslist as $params) {
            if (! $stmt->execute( $params )) {
                $stmt->closeCursor();
                return 0;
            }

            try {
                $dbLastInsert = $dbConns[ $cxn ]->lastInsertId();
            } catch (PDOException $e) {
                $dbError = $e->getMessage();
            }

            $n += $stmt->rowCount();
        }
        $stmt->closeCursor();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
        return 0;
    }

    return $n;
}

function dbGetNextAutoInc( $tablename, $params = NULL, $cxn = NULL ) {
    // This is ugly, but PDO seems determined not to let us do it a proper way.
    
    global $dbLastInsert;
    
    if ($params == NULL) {
        $paramlist = '';
    } else {
        $paramlist = str_repeat( ', ?', count( $params ) );
    }
    
    $sql = 'INSERT INTO ' . $tablename . ' VALUES( NULL' . $paramlist . ' )';
    if (!dbUpdate( $sql, $params, $cxn )) {
        return -1;
    }
    
    $lastauto = $dbLastInsert;
    
    $sql = 'DELETE FROM ' . $tablename . ' wHERE ID = ?';
    $params = array( $lastauto );
    dbUpdate( $sql, $params, $cxn );
    
    return ($lastauto + 1);
}
