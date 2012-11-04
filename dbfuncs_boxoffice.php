<?php

require_once( 'dblib.php' );

$dbConn[ 'boxoffice' ] = NULL;
dbInitParams( 'boxoffice' );

$thisYear = 2012;


function dbHashPassword( $username, $password ) {
    $sql = 'SELECT Salt FROM Users WHERE Name = ?';
    $params = array( $username );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $salt = $row[ 0 ][ 'Salt' ];

    return sha1( $password . $salt );
}

function dbLogin( $username, $password ) {
    $sql = 'SELECT Hash FROM Users WHERE Name = ? AND Password = ?';
    $params = array( $username, dbHashPassword( $username, $password ) );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    if (! isset( $row[ 0 ][ 'Hash' ] )) {
        return false;
    }

    $userhash = $row[ 0 ][ 'Hash' ];
    sessLogin( $username, $userhash );

    return true;
}

function dbLogout() {
    sessLogout();
}

function dbIsLoggedIn() {
    $sql = 'SELECT Count( * ) AS NumUsers FROM Users WHERE Name = ? AND Hash = ?';
    $params = array( sessGetLogin(), sessGetHash() );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    return ($row[ 0 ][ 'NumUsers' ] == 1 ? true : false);
}

function dbGetFestivalSpan( $year = NULL ) {
    global $thisYear;
    
    if ($year == NULL) {
        $year = $thisYear;
    }

    $sql = 'SELECT StartDate, EndDate FROM Festivals WHERE Year = ?';
    $params = array( $year );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ];
}

function dbGetFestivalDates( $year = NULL ) {
    $span = dbGetFestivalSpan( $year );
    if ($span === false) {
        return false;
    }
    
    return dbGetPerformanceDates( $span[ 'StartDate' ], $span[ 'EndDate' ] );
}

function dbGetFestivalDay( $day = NULL ) {
    if ($day == NULL) {
        $span = dbGetFestivalSpan();
        list( $startyr, $startmo, $startdy ) = explode( '-', $span[ 'StartDate' ] );
        list( $endyr, $endmo, $enddy ) = explode( '-', $span[ 'EndDate' ] );
        if (time() < mktime( 0, 0, 0, $startmo, $startdy, $startyr )) {
            $day = $span[ 'StartDate' ];
        } else if (time() >= mktime( 0, 0, 0, $endmo, $enddy, $endyr )) {
            $day = $span[ 'EndDate' ];
        } else {
            $day = date( 'Y-m-d' );
        }
    }
    
    return $day;
}

function dbGetPerformanceDates( $startdate = NULL, $enddate = NULL ) {
    if (($startdate == NULL) || ($enddate == NULL)) {
        $span = dbGetFestivalSpan();
        
        if ($startdate == NULL) {
            $startdate = $span[ 'StartDate' ];
        }
        if ($enddate == NULL) {
            $enddate = $span[ 'EndDate' ];
        }        
    }
    
    $sql = 'SELECT DISTINCT Day FROM Windows ' .
           ' WHERE Day BETWEEN ? AND ? ORDER BY Day';
    $params = array( $startdate, $enddate );

    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetVenueList() {
    $sql = 'SELECT ID, Name FROM Venues ORDER BY Name';

    return dbGetResult( $sql, NULL, 'boxoffice' );
}

function dbGetVenueParamList() {
    $venues = dbgetVenueList();

    $plist = array();
    foreach ($venues as $v) {
        $plist[ $v[ 'ID' ] ] = $v[ 'Name' ];
    }

    return $plist;
}

function dbGetConcessionItemParamList() {
    $sql = 'SELECT ID, Concat( Name, " @ $", Format( ItemPrice / 100, 2 ) ) AS ItemName ' .
           '  FROM ConcessionItems ORDER BY Name';
    $items = dbGetResult( $sql, NULL, 'boxoffice' );

    $itemlist = array();
    foreach ($items as $item) {
        $itemlist[ $item[ 'ID' ] ] = $item[ 'ItemName' ];
    }

    return $itemlist;
}

function dbGetCompReasonsParamList() {
    $reasons = array(
        'All-Access',
        'Artist',
        'Host',
        'Media',
        'Nova Chem',
        'Superpass',
        'Volunteer',
        '(Other)'
    );
    
    return $reasons;
}

function dbGetPerformancesParamList() {
    $sql = '  SELECT Performances.ID, Concat( Name, " (", PerformanceDate, " @ ", PerformanceTime, ")" ) AS PDefn ' .
           '    FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           'ORDER BY Name, PerformanceDate, PerformanceTime';
    $performances = dbGetResult( $sql, NULL, 'boxoffice' );

    $pdefns = array();
    foreach ($performances as $p) {
        $pdefns[ $p[ 'ID' ] ] = $p[ 'PDefn' ];
    }

    return $pdefns;
}

function dbGetPerformanceDefn( $pid ) {
    $sql = 'SELECT Concat( Name, " (", PerformanceDate, " @ ", PerformanceTime, ")" ) AS PDefn ' .
           '  FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE Performances.ID = ?';
    $params = array( $pid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    return $row[ 0 ][ 'PDefn' ];
}

function dbGetVenueDetails( $venueid ) {
    $sql = 'SELECT Name, Capacity, Overflow, StartingFloat, StartingButtons ' .
           '  FROM Venues JOIN Windows ON Venues.ID = Windows.VenueID ' .
           ' WHERE Venues.ID = ? ' .
           ' LIMIT 1';
    $params = array( $venueid );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $venue = array(
        'Name' => $row[ 0 ][ 'Name' ],
        'Details' => array(
            'Capacity' => array( 'Value' => $row[ 0 ][ 'Capacity' ], 'Type' => 'int' ),
            'Overflow' => array( 'Value' => $row[ 0 ][ 'Overflow' ], 'Type' => 'int' ),
            'StartingFloat' => array( 'Value' => $row[ 0 ][ 'StartingFloat' ], 'Type' => 'currency' ),
            'StartingButtons' => array( 'Value' => $row[ 0 ][ 'StartingButtons' ], 'Type' => 'int' )
        )
    );

    return $venue;
}

function dbGetWindowID( $venueid, $day ) {
    $sql = 'SELECT ID FROM Windows WHERE VenueID = ? AND Day = ?';
    $params = array( $venueid, $day );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ][ 'ID' ];
}

function dbGetVenueName( $venueid ) {
    $sql = 'SELECT Name FROM Venues WHERE ID = ?';
    $params = array( $venueid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ][ 'Name' ];
}

function dbGetPerformanceVenueName( $pid ) {
    $sql = 'SELECT Venues.Name ' .
           '  FROM Performances JOIN Venues ON Performances.VenueID = Venues.ID ' .
           ' WHERE Performances.ID = ?';
    $params = array( $pid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ][ 'Name' ];
}

function dbGetWindowTxns( $windowid ) {
    $params = array( $windowid );
    
    $sql = 'SELECT ID, SaleTime, NumButtons, Note FROM ButtonSales WHERE WindowID = ? ORDER BY SaleTime';
    $buttontxns = dbGetResult( $sql, $params, 'boxoffice' );
    if ($buttontxns === false) {
        $buttontxns = array();
    }
    
    $sql = '  SELECT ConcessionSales.ID, SaleTime, ' .
           '         Concat( ConcessionItems.Name, " @ $", Format( ItemPrice / 100, 2 ) ) AS ItemName, ' .
           '         NumItems, Concat( "$", Format( (NumItems * ItemPrice) / 100, 2 ) ) AS Amount ' .
           '    FROM ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID ' .
           '   WHERE WindowID = ? ' .
           'ORDER BY SaleTime';
    $concessiontxns = dbGetResult( $sql, $params, 'boxoffice' );
    if ($concessiontxns === false) {
        $concessiontxns = array();
    }
    
    $sql = '  SELECT ID, DonationTime, Concat( "$", Format( Amount / 100, 2 ) ) AS Amount, Note ' .
           '    FROM Donations ' .
           '   WHERE WindowID = ? ' .
           'ORDER BY DonationTime';
    $donationtxns = dbGetResult( $sql, $params, 'boxoffice' );
    if ($donationtxns === false) {
        $donationtxns = array();
    }
    
    return array(
        'ButtonSales' => $buttontxns,
        'ConcessionSales' => $concessiontxns,
        'Donations' => $donationtxns
    );
}

function dbGetShowList() {
    $span = dbGetFestivalSpan();
    
    $sql = '  SELECT DISTINCT Shows.ID, Concat( Shows.Name, " (", Artists.Name, ")" ) AS Name ' .
           '    FROM (Shows JOIN Artists ON Shows.ArtistID = Artists.ID) JOIN Performances ON Performances.ShowID = Shows.ID ' .
           '   WHERE PerformanceDate BETWEEN ? AND ? ' .
           'ORDER BY Shows.Name';
    $params = array( $span[ 'StartDate' ], $span[ 'EndDate' ] );

    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetShowDetails( $showid ) {
    $sql = '  SELECT Shows.Name, TicketPrice, ToArtist, ' .
           '         Artists.Name AS Artist, GSTNumber, WithholdingTax ' .
           '    FROM Shows JOIN Artists ON Shows.ArtistID = Artists.ID ' .
           '   WHERE Shows.ID = ? ' .
           'ORDER BY Shows.Name';
    $params = array( $showid );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $show = array(
        'Name' => $row[ 0 ][ 'Name' ],
        'Details' => array(
            'Artist' => array( 'Value' => $row[ 0 ][ 'Artist' ], 'Type' => 'readonly' ),
            'TicketPrice' => array( 'Value' => $row[ 0 ][ 'TicketPrice' ], 'Type' => 'currency' ),
            'ToArtist' => array( 'Value' => $row[ 0 ][ 'ToArtist' ], 'Type' => 'currency' ),
            'GSTNumber' => array( 'Value' => $row[ 0 ][ 'GSTNumber' ], 'Type' => 'string', 'Params' => 15 ),
            'WithholdingTax' => array( 'Value' => $row[ 0 ][ 'WithholdingTax' ], 'Type' => 'bool' )
        )
    );

    return $show;
}

function dbGetPerformanceList( $day = NULL ) {
    if ($day == NULL) {
        $span = dbGetFestivalSpan();

        $datespec = 'BETWEEN ? AND ? ';
        $params = array( $span[ 'StartDate' ], $span[ 'EndDate' ] );
    } else {
        $datespec = '= ? ';
        $params = array( $day );
    }
    
    $sql = '  SELECT Performances.ID, PerformanceDate, Venues.Name AS VenueName, ' .
           '         Concat( Shows.Name, " @ ", Date_Format( PerformanceTime, "%h:%i %p" ) ) AS ShowNameTime ' .
           '    FROM (Performances JOIN Shows ON Performances.ShowID = Shows.ID) JOIN Venues ' .
           '      ON Performances.VenueID = Venues.ID ' .
           '   WHERE PerformanceDate ' . $datespec .
           'ORDER BY PerformanceDate, Venues.Name, PerformanceTime';
    $params = array( $day );

    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetPerformanceDetails( $perfid ) {
    $sql = '  SELECT Venues.Name AS VenueName, PerformanceDate, PerformanceTime, ' .
           '         IsHomeVenue, StartingTickets, SalesStopAt, Shows.Name AS ShowName ' .
           '    FROM ((Performances JOIN Venues ON Performances.VenueID = Venues.ID) JOIN ' .
           '          PerformanceWindows ON Performances.ID = PerformanceWindows.PerformanceID) JOIN ' .
           '          Shows ON Performances.ShowID = Shows.ID ' .
           '   WHERE Performances.ID = ? ' .
           'ORDER BY IsHomeVenue';
    $params = array( $perfid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $venue = ($row[ 0 ][ 'IsHomeVenue' ] == 'Y' ? 0 : 1);
    $infotent = 1 - $venue;
    $performance = array(
        'Name' => $row[ 0 ][ 'ShowName' ] . ' on ' . $row[ 0 ][ 'PerformanceDate' ],
        'Details' => array(
            'Venue' => array( 'Value' => $row[ 0 ][ 'VenueName' ], 'Type' => 'enum', 'Params' => dbGetVenueParamList() ),
            'Time' => array( 'Value' => $row[ 0 ][ 'PerformanceTime' ], 'Type' => 'time' ),
            'InfoTentStartingTickets' => array( 'Value' => $row[ $infotent ][ 'StartingTickets' ], 'Type' => 'int' ),
            'InfoTentSalesStopAt' => array( 'Value' => $row[ $infotent ][ 'SalesStopAt' ], 'Type' => 'time' ),
            'VenueStartingTickets' => array( 'Value' => $row[ $venue ][ 'StartingTickets' ], 'Type' => 'int' ),
            'VenueSalesStopAt' => array( 'Value' => $row[ $venue ][ 'SalesStopAt' ], 'Type' => 'time' )
        )
    );
    
    return $performance;
}

function dbGetPresales( $performanceid ) {    
    $sql = '  SELECT Name, OrderNum, NumTickets, Redeemed AS PickedUp, PassType ' .
           '    FROM Presales ' .
           '   WHERE PerformanceID = ? ' .
           'ORDER BY Name';
    $params = array( $performanceid );
    
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        $res = array();
    }

    return $res;
}


function dbGetMaxFieldFromTable( $fieldname, $tablename, $spec = NULL, $default = 0 ) {
    // WARNING: THIS IS NOT THREAD-SAFE AT ALL!!! USE AT YOUR OWN RISK!!!

    if ($spec == NULL) {
        $where = '';
    } else {
        if (strtoupper( substr( trim( $spec ), 0, 5 ) ) == 'WHERE') {
            $where = ' ' . $spec;
        } else {
            $where = ' WHERE ' . $spec;
        }
    }
    
    $sql = 'SELECT Coalesce( Max( ' . $fieldname . ' ), ' . $default . ' ) As MaxField FROM ' . $tablename . $where;
    $row = dbGetResult( $sql, NULL, 'boxoffice' );
    if ($row === false) {
        return $default;
    }
    
    return $row[ 0 ][ 'MaxField' ];
}

function dbGetNextFreePresaleOrderNum() {
    // WARNING: THIS IS NOT THREAD-SAFE AT ALL!!! USE AT YOUR OWN RISK!!!
    
    return dbGetMaxFieldFromTable( 'OrderNum', 'Presales', 'PassType=\'Manual\'' ) + 1;
}

function dbGetWindowIDByVenueAndDay( $venueid, $day ) {
    $sql = 'SELECT ID FROM Windows WHERE VenueID = ? AND Day = ?';
    $params = array( $venueid, $day );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ][ 'ID' ];
}

function dbGetPerformanceTxns( $performanceid, $fromvenue = NULL ) {
    $params = array( $performanceid );

    $sql = 'SELECT ID, IsHomeVenue FROM PerformanceWindows WHERE PerformanceID = ?';
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    if ($fromvenue === NULL) {
        $params = array( $row[ 0 ][ 'ID' ], $row[ 1 ][ 'ID' ] );
        $where = 'PerformanceWindowID = ? OR PerformanceWindowID = ?';
    } else {
        $venue = ($row[ 0 ][ 'IsHomeVenue' ] == 'Y' ? 0 : 1);
        $infotent = 1 - $venue;
        if ($fromvenue) {
            $params = array( $row[ $venue ][ 'ID' ] );
        } else {
            $params = array( $row[ $infotent ][ 'ID' ] );
        }
        $where = 'PerformanceWindowID = ?';
    }
    
    $sql = '  SELECT ID, SaleTime, NumTickets, ' .
           '         If( PriceOverride = 0, NULL, PriceOverride ) AS PriceOverride, ' .
           '         If( ToArtistOverride = 0, NULL, ToArtistOverride ) AS ToArtistOverride, ' .
           '         Note ' .
           '    FROM CashSales ' .
           '   WHERE ' . $where . ' ' .
           'ORDER BY SaleTime';
    $cashtxns = dbGetResult( $sql, $params, 'boxoffice' );
    if ($cashtxns == false) {
        $cashtxns = array();
    }
    
    $sql = '  SELECT ID, SaleTime, NumTickets, Reason, Note ' .
           '    FROM Comps ' .
           '   WHERE ' . $where . ' ' .
           'ORDER BY SaleTime';
    $comptxns = dbGetResult( $sql, $params, 'boxoffice' );
    if ($comptxns == false) {
        $comptxns = array();
    }
    
    return array( 'CashSales' => $cashtxns, 'Comps' => $comptxns );
}

function dbGetVenueSuperReconciliations( $day ) {
    $sql = '  SELECT Venues.Name, AdjustmentTime AS Time, ' .
           '         Concat( "$", Format( Amount / 100, 2 ) ) AS Amount, Reason, Note ' .
           '    FROM (CashAdjustments JOIN Windows ON CashAdjustments.WindowID = Windows.ID) ' .
           '    JOIN Venues ON Windows.VenueID = Venues.ID ' .
           '   WHERE Day = ? ' .
           'ORDER BY Venues.Name, AdjustmentTime';
    $params = array( $day );
    
    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetEndOfDaySummary( $day ) {
    $params = array( $day );

    $sql = 'SELECT Coalesce( Sum( NumTickets * PriceOverride ), 0 ) AS Total ' .
           '  FROM (CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID) ' .
           '  JOIN Performances ON PerformanceWindows.PerformanceID = Performances.ID ' .
           ' WHERE PerformanceDate = ? AND PriceOverride > 0';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $specialsales = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumTickets * TicketPrice ), 0 ) AS Total ' .
           '  FROM ((CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID) ' .
           '        JOIN Performances ON PerformanceWindows.PerformanceID = Performances.ID) ' .
           '        JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE PerformanceDate = ? AND PriceOverride = 0';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $normalsales = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumButtons * 500 ), 0 ) AS Total ' .
           '  FROM ButtonSales JOIN Windows ON ButtonSales.WindowID = Windows.ID ' .
           ' WHERE Day = ?';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $buttonsales = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( Amount ), 0 ) AS Total ' .
           '  FROM Donations JOIN Windows ON Donations.WindowID = Windows.ID ' .
           ' WHERE Day = ?';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $donations = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumItems * ItemPrice ), 0 ) AS Total ' .
           '  FROM (ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID) ' .
           '        JOIN Windows ON ConcessionSales.WindowID = Windows.ID ' .
           ' WHERE Day = ? AND Name NOT LIKE \'superpass%\' AND Name NOT LIKE \'merchandise%\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $concessions = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumItems * ItemPrice ), 0 ) AS Total ' .
           '  FROM (ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID) ' .
           '        JOIN Windows ON ConcessionSales.WindowID = Windows.ID ' .
           ' WHERE Day = ? AND Name LIKE \'merchandise%\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $merchandise = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumItems * ItemPrice ), 0 ) AS Total ' .
           '  FROM (ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID) ' .
           '        JOIN Windows ON ConcessionSales.WindowID = Windows.ID ' .
           ' WHERE Day = ? AND Name LIKE \'superpass%\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $superpasses = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( Amount ), 0 ) AS Total ' .
           '  FROM CashAdjustments JOIN Windows ON CashAdjustments.WindowID = Windows.ID ' .
           ' WHERE Day = ? AND Reason = \'Payout\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );

    $payouts = $row[ 0 ][ 'Total' ];

    $cashin = $specialsales + $normalsales + $buttonsales + $donations + $concessions + $merchandise + $superpasses;

    $report = array(
        'special sales' => '$' . number_format( $specialsales / 100, 2 ),
        'normal sales' => '$' . number_format( $normalsales / 100, 2 ),
        'button sales' => '$' . number_format( $buttonsales / 100, 2 ),
        'donations' => '$' . number_format( $donations / 100, 2 ),
        'concessions' => '$' . number_format( $concessions / 100, 2 ),
        'merchandise' => '$' . number_format( $merchandise / 100, 2 ),
        'superpasses' => '$' . number_format( $superpasses / 100, 2 ),
        'TOTAL CASH RECEIVED' => '$' . number_format( $cashin / 100, 2 ),
        'ARTIST PAYOUTS' => '$' . number_format( $payouts / 100, 2 ),
        'REMAINING CASH' => '$' . number_format( ($cashin - $payouts) / 100, 2 )
    );

    return $report;
}

function checkAuth( $user, $ip, $sessauth ) {
    global $curdate;

    $sql = 'SELECT Count(*) AS NumSessions ' .
           '  FROM Connections LEFT JOIN Users ' .
           '    ON Connections.UserID = Users.ID ' .
           ' WHERE CxnDate = ? AND Name = ? AND ' .
           '       IP = ? AND CxnAuth = ?';
    $params = array( $curdate, $user, $ip, $sessauth );

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        return false;
    }

    if ($res[ 0 ][ 'NumSessions' ] <= 0) {
        badauth();
    }

    // Just some cleanup. I know there are far, far better ways of doing this sort
    //  of thing. With any luck I will have time to implement one of them someday...
    $sql = 'DELETE Connections.* ' .
           '  FROM Connections LEFT JOIN Users ' .
           '    ON Connections.UserID = Users.ID ' .
           ' WHERE Name = ? AND ' .
           '       IP = ? AND ' .
           '       CxnDate <> ?';
    $params = array( $user, $ip, $curdate );
    
    dbUpdate( $sql, $params, 'boxoffice' );

    return true;
}

function checkPerformanceWindowOpen( $pwindowid ) {
    global $curdate;
    global $curtime;

    $sql = 'SELECT Count(*) AS NumRecords ' .
           '  FROM PerformanceWindows LEFT JOIN Windows ' .
           '    ON PerformanceWindows.WindowID = Windows.ID ' .
           ' WHERE Day = ? AND ' .
           '       OpensAt <= ? AND ' .
           '       SalesStopAt > ? AND ' .
           '       PerformanceWindows.ID = ?';
    $params = array( $curdate, $curtime, $curtime, $pwindowid );

    return recordExists( $sql, $params, 'Performance window not open' );
}

function checkWindowOpen( $windowid ) {
    global $curdate;
    global $curtime;

    $sql = 'SELECT Count(*) AS NumRecords ' .
           '  FROM Windows ' .
           ' WHERE Day = ? AND ' .
           '       OpensAt <= ? AND ' .
           '       ClosesAt > ? AND ' .
           '       Windows.ID = ?';
    $params = array( $curdate, $curtime, $curtime, $windowid );

    return recordExists( $sql, $params, 'Window not open' );
}

function recordExists( $sql, $params = array(), $msg = 'Record not found' ) {
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'A1' );
    }

    if ($res[ 0 ][ 'NumRecords' ] <= 0) {
        fail( $msg );
    }
    
    return true;
}

function getCompReason( $reasonid ) {
    switch ($reasonid) {
        case 1:
            $reason = 'Volunteer';
            break;
        case 2:
            $reason = 'Media';
            break;
        case 3:
            $reason = 'Artist';
            break;
        case 4:
            $reason = 'Host';
            break;
        case 5:
            $reason = 'Superpass';
            break;
        case 6:
            $reason = 'All-Access';
            break;
        case 7:
            $reason = '(Other)';
            break;
        default:
            fail( 'B1' );
    }
    
    return $reason;
}

function fail( $msg = NULL ) {
    echo "fail\n";

    if ($msg != NULL) {
        if (strlen( $msg ) <= 3) {
            $msg = "/ code = " . $msg;
        } else {
            $msg = "/ reason = " . $msg;
        }
        echo $msg . "\n";
    }

    dbDisconnect( 'boxoffice' );
    exit( 1 );
}

function badauth() {
    echo "badauth\n";
    dbDisconnect();
    exit( 2 );
}

function tryAddPresale() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $pass = dbSafeUserPass( $_POST[ 'password' ] );
    $showid = dbSafeInt( $_POST[ 'showid' ] );
    $guest = $_POST[ 'guestname' ];
    $ordernum = dbSafeInt( $_POST[ 'ordernum' ] );
    $numtickets = dbSafeInt( $_POST[ 'numtickets' ] );
    $superpass = dbSafeEnum( $_POST[ 'superpass' ], array( 'None', 'Binger', 'Buddy', 'Frequent', 'Manual' ), 'None' );

    if (($user != 'snichols') || ($pass != 'kirkpatrick')) {
        fail( 'Invalid username / password' );
    }

    $sql = 'INSERT INTO Presales VALUES( ?, ?, ?, ?, \'N\', ? )';
    $params = array( $guest, $ordernum, $showid, $numtickets, $superpass );
    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'C1' );
    }

    return true;
}

function tryInitWindow() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $pass = dbSafeUserPass( $_POST[ 'password' ] );
    $pwindowid = dbSafeInt( $_POST[ 'windowid' ] );
    $tickets = dbSafeInt( $_POST[ 'tickets' ] );
    $buttons = dbSafeInt( $_POST[ 'buttons' ] );
    $float = (dbSafeInt( $_POST[ 'floatd' ] ) * 100) + dbSafeInt( $_POST[ 'floatc' ] );

    if (($user != 'snichols') || ($pass != 'kirkpatrick')) {
        fail( 'Invalid username / password' );
    }

    $sql = 'SELECT WindowID FROM PerformanceWindows WHERE ID = ?';
    $params = array( $pwindowid );
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'D1' );
    }
    $windowid = $res[ 0 ][ 'WindowID' ];

    $sql = 'UPDATE PerformanceWindows ' .
           '   SET StartingTickets = ? ' .
           ' WHERE ID = ?';
    $params = array( $tickets, $pwindowid );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'D2' );
    }

    $sql = 'UPDATE Windows ' .
           '   SET StartingButtons = ?, ' .
           '       StartingFloat = ? ' .
           ' WHERE ID = ?';
    $params = array( $buttons, $float, $windowid );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'D3' );
    }

    return true;
}

function dbGetPayoutMade( $perfid, $amountonly = true ) {
    $sql = 'SELECT Count(*) AS Showing ' .
           '  FROM Performances ' .
           ' WHERE PerformanceDate IN (SELECT PerformanceDate FROM Performances WHERE ID = ?) AND ' .
           '       ShowID IN (SELECT ShowID FROM Performances WHERE ID = ?) AND ' .
           '       PerformanceTime <= (SELECT PerformanceTime FROM Performances WHERE ID = ?)';
    $params = array( $perfid, $perfid, $perfid );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $whichOfDaysShowings = $row[ 0 ][ 'Showing' ];

    $sql = 'SELECT CashAdjustments.ID AS TxnID, CashAdjustments.Amount ' .
           '  FROM ((Performances JOIN Shows ON Performances.ShowID = Shows.ID) JOIN ' .
           '        PerformanceWindows ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '        CashAdjustments ON PerformanceWindows.WindowID = CashAdjustments.WindowID ' .
           ' WHERE CashAdjustments.Reason = \'Payout\' AND ' .
           '       CashAdjustments.Note = Shows.Name AND ' .
           '       Performances.ID = ?';
    $params = array( $perfid );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    if (count( $row ) < $whichOfDaysShowings) {
        return false;
    }
    
    $txnrow = $row[ ($whichOfDaysShowings - 1) ];
    $txnid = $txnrow[ 'TxnID' ];
    $amt = $txnrow[ 'Amount' ];

    if ($amountonly) {
        return $amt;
    } else {
        return array( $txnid, $amt );
    }
}

function dbMakeArtistPayout( $perfid, $amount = NULL ) {
    global $curtime;

    $params = array( $perfid );

    $sql = 'SELECT Shows.Name AS Title ' .
           '  FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE Performances.ID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $title = $row[ 0 ][ 'Title' ];

    $sql = 'SELECT WindowID ' .
           '  FROM PerformanceWindows ' .
           ' WHERE PerformanceID = ? AND IsHomeVenue = \'Y\'';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $wid = $row[ 0 ][ 'WindowID' ];

    if ($amount == NULL) {
        $payout = dbGetArtistPayout( $perfid );
        if ($payout === false) {
            return false;
        }
        $amount = $payout[ 'total' ];
    }

    $sql = 'INSERT INTO CashAdjustments VALUES( NULL, ?, ?, ?, ?, ? )';
    $params = array( $wid, $curtime, $amount, 'Payout', $title );

    dbUpdate( $sql, $params, 'boxoffice' );
    
    return true;
}

function dbUpdateArtistPayout( $txnid, $amount ) {
    global $curtime;
    
    $sql = 'UPDATE CashAdjustments ' .
           '   SET AdjustmentTime = ?, Amount = ? ' .
           ' WHERE ID = ?';
    $params = array( $curtime, $amount, $txnid );

    dbUpdate( $sql, $params, 'boxoffice' );
    
    return true;
}

function dbAnalyzeCPopOrderKind( $order ) {
    $kind = 'Promo';

    if ($order[ 'CustomerID' ] == 901) {
        // Neal Doncaster
        $kind = 'Super';
    } else if ($order[ 'UsedPass' ] == 'N') {
        $kind = 'Indiv';
    } else {
        if ($order[ 'PassType' ] == 'superpass') {
            $kind = 'Super';
        } else if ($order[ 'PassType' ] == 'corporate') {
            if (isset( $order[ 'PassID' ] )) {
                $kind = 'Super';
            } else {
                $kind = 'Promo';
            }
        } else {
            $kind = 'Promo';
        }
    }
    
    return ('Online' . $kind);
}

function dbGetReportPerformance( $perfid, $title ) {
    $params = array( $perfid );
    $sql = 'SELECT PerformanceDate, CarbonpopShowingID ' .
           '  FROM Performances WHERE ID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    $performance = array(
        'ID' => $perfid,
        'PerformanceDate' => $row[ 0 ][ 'PerformanceDate' ],
        'OnlineSuper' => 0,
        'OnlineIndiv' => 0,
        'OnlinePromo' => 0,
        'InfoNormal' => 0,
        'Info1000' => 0,
        'InfoSuper' => 0,
        'InfoArtist' => 0,
        'InfoPromo' => 0,
        'VenueNormal' => 0,
        'Venue1000' => 0,
        'VenueSuper' => 0,
        'VenueArtist' => 0,
        'VenuePromo' => 0,
        'Payout' => 0
    );

    $showingid = $row[ 0 ][ 'CarbonpopShowingID' ];
    if ($showingid != NULL) {
        $presales = getCPopOrderBreakdown( $showingid );
        if ($presales !== false) {
            foreach ($presales as $order) {
                $n = $order[ 'NumTickets' ];
                $orderkind = dbAnalyzeCPopOrderKind( $order );

                $performance[ $orderkind ] += $n;
            }
        }
    }

    $sql = '  SELECT Sum( NumTickets ) AS Total, PriceOverride, IsHomeVenue ' .
           '    FROM CashSales LEFT JOIN PerformanceWindows ' .
           '      ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? ' .
           'GROUP BY IsHomeVenue, PriceOverride';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $ticketsale) {
            $loc = ($ticketsale[ 'IsHomeVenue' ] == 'Y' ? 'Venue' : 'Info');
            if ($ticketsale[ 'PriceOverride' ] == 0) {
                $performance[ $loc . 'Normal' ] = $ticketsale[ 'Total' ];
            } else {
                $performance[ $loc . $ticketsale[ 'PriceOverride' ] ] = $ticketsale[ 'Total' ];
            }
        }
    }

    $sql = '  SELECT Sum( NumTickets ) AS Total, Reason, IsHomeVenue ' .
           '    FROM Comps LEFT JOIN PerformanceWindows ' .
           '      ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? ' .
           'GROUP BY IsHomeVenue, Reason';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $ticketcomp) {
            $loc = ($ticketcomp[ 'IsHomeVenue' ] == 'Y' ? 'Venue' : 'Info');
            switch ($ticketcomp[ 'Reason' ]) {
                case 'Superpass':
                    $performance[ $loc . 'Super' ] = $ticketcomp[ 'Total' ];
                    break;
                case 'Artist':
                    $performance[ $loc . 'Artist' ] = $ticketcomp[ 'Total' ];
                    break;
                case 'Nova Chem':
                    $performance[ 'OnlineSuper' ] += $ticketcomp[ 'Total' ];
                    break;
                default:
                    $performance[ $loc . 'Promo' ] += $ticketcomp[ 'Total' ];
                    break;
            }
        }
    }

    $payout = dbGetPayoutMade( $perfid );
    if ($payout !== false) {
        $performance[ 'Payout' ] = $payout;
    }

    return $performance;
}

function dbGetReportShow( $showid ) {
    $span = dbGetFestivalSpan();

    $sql = '  SELECT Shows.Name AS Title, TicketPrice, ToArtist, Venues.Name AS VenueName, ' .
           '         Artists.Name AS ArtistName, GSTNumber, WithholdingTax, Performances.ID AS PerfID ' .
           '    FROM ((Shows JOIN Performances ON Shows.ID = Performances.ShowID) JOIN ' .
           '          Venues ON Performances.VenueID = Venues.ID) JOIN ' .
           '          Artists ON Shows.ArtistID = Artists.ID ' .
           '   WHERE Shows.ID = ? AND ' .
           '         PerformanceDate BETWEEN ? AND ? ' .
           'ORDER BY PerformanceDate, PerformanceTime';
    $params = array( $showid, $span[ 'StartDate' ], $span[ 'EndDate' ] );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $show = $row[ 0 ];
    $show[ 'ID' ] = $showid;
    unset( $show[ 'PerfID' ] );
    $show[ 'Performances' ] = array();

    $title = $show[ 'Title' ];

    foreach ($row as $performances) {
        $id = $performances[ 'PerfID' ];
        $show[ 'Performances' ][] = dbGetReportPerformance( $id, $title );
    }
    
    return $show;
}

function dbGetReportShows() {
    global $dbConns;

    dbConnect( 'carbonpop' );
    if ($dbConns[ 'carbonpop' ] == NULL) {
        return false;
    }

    $sql = 'SELECT ID FROM Shows ORDER BY Name';
    $list = dbGetResult( $sql, NULL, 'boxoffice' );
    $shows = array();
    foreach ($list as $showid) {
        $show = dbGetReportShow( $showid[ 'ID' ] );
        if ($show !== false) {
            $shows[ $showid[ 'ID' ] ] = $show;
        }
    }

    dbDisconnect( 'carbonpop' );

    return $shows;
}

function dbGetReportSummary() {
    $summary = getCPopSuperpassPresales();
//    $summary = array(
//        'PresoldFringers' => 30800,
//        'PresoldBuddys' => 191100,
//        'PresoldBingers' => 125000
//    );
    
    $days = dbGetFestivalDates();
    foreach ($days as $day) {
        $summary[ 'Daily' ][ $day[ 'Day' ] ] = array(
            'Buttons' => 0,
            'Fringers' => 0,
            'Buddys' => 0,
            'Bingers' => 0,
        );
    }

    $sql = '  SELECT Day, Sum( NumButtons * 500 ) AS Total ' .
           '    FROM ButtonSales LEFT JOIN Windows ON ButtonSales.WindowID = Windows.ID ' .
           'GROUP BY Day ' .
           'ORDER BY Day';
    $row = dbGetResult( $sql, NULL, 'boxoffice' );

    if ($row !== false) {
        foreach ($row as $lineitem) {
            $day = $lineitem[ 'Day' ];
            if (isset( $summary[ 'Daily' ][ $day ] )) {
                $summary[ 'Daily' ][ $day ][ 'Buttons' ] = $lineitem[ 'Total' ];
            }
        }
    }
    
    $sql = '  SELECT Day, ItemPrice, Coalesce( Sum( NumItems * ItemPrice ), 0 ) AS Total ' .
           '    FROM (ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID) JOIN ' .
           '          Windows ON ConcessionSales.WindowID = Windows.ID ' .
           '   WHERE ConcessionItems.Name LIKE \'%superpass%\' ' .
           'GROUP BY Day, ItemPrice';
    $row = dbGetResult( $sql, NULL, 'boxoffice' );
    
    if ($row !== false) {
        foreach ($row as $sale) {
            $day = $sale[ 'Day' ];
            if (isset( $summary[ 'Daily' ][ $day ] )) {
                switch ($sale[ 'ItemPrice' ]) {
                    case 5500:
                        $ptype = 'Fringers';
                        break;
                    case 10500:
                        $ptype = 'Buddys';
                        break;
                    case 20000:
                        $ptype = 'Bingers';
                        break;
                }
                $summary[ 'Daily' ][ $day ][ $ptype ] = $sale[ 'Total' ];
            }
        }
    }

    return $summary;
}

function dbGetFinalReportData() {
    $report = array(
        'Summary' => dbGetReportSummary(),
        'Shows' => dbGetReportShows(),
        'Dates' => dbGetFestivalDates()
    );

    return $report;
}

function tryLogin() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $pass = dbSafeUserPass( $_POST[ 'password' ] );

    $sql = 'SELECT Count(*) AS NumUsers ' .
           '  FROM Users ' .
           ' WHERE Name = ? AND ' .
           '       Password = SHA1( ? )';
    $params = array( $user, $pass );
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'E1' );
    }

    if ($res[ 0 ][ 'NumUsers' ] <= 0) {
        fail( 'Invalid username / password supplied' );
    }

    $sql = 'SELECT ID FROM Users WHERE Name = ?';
    $params = array( $user );
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'E2' );
    }

    $userid = $res[ 0 ][ 'ID' ];

    $salt = 'dsgfsdbfqwpoaawid389dhn3weuodfh';
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    $cxnauth = md5( $salt . time() . $ip . $user );
    $sql = 'INSERT INTO Connections ' .
           '    VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $userid, $ip, date( 'Y-m-d' ), $cxnauth );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'E3' );
    }

    return $cxnauth;
}

function tryPerformanceWindowList() {
    global $curdate;
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $venueid = dbSafeInt( $_POST[ 'venueid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'F1' );
    }
    
    return getWindowData( $venueid, $curdate, $curtime );
}

function getWindowData( $venueid, $day, $time ) {
    $sql = 'SELECT Windows.ID, StartingButtons, StartingFloat, HasConcession ' .
           '  FROM Windows LEFT JOIN Venues ' .
           '    ON Windows.VenueID = Venues.ID ' .
           ' WHERE Day = ? AND ' .
           '       OpensAt <= ? AND ' .
           '       ClosesAt > ? AND ' .
           '       Windows.VenueID = ?';
    $params = array( $day, $time, $time, $venueid );

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F2' );
    }
    $window = $res[ 0 ];
    
    $params = array( $window[ 'ID' ] );

    $sql = 'SELECT Coalesce( Sum( NumButtons ), 0 ) AS ButtonSales ' .
           '  FROM ButtonSales ' .
           ' WHERE WindowID = ?';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F3' );
    }
    $window[ 'ButtonSales' ] = (int) $res[ 0 ][ 'ButtonSales' ];

    $sql = 'SELECT Coalesce( Sum( Amount ), 0 ) AS Total ' .
           '  FROM Donations ' .
           ' WHERE WindowID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F4' );
    }
    $window[ 'Donations' ] = (int) $res[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( Amount ), 0 ) AS Total ' .
           '  FROM CashAdjustments ' .
           ' WHERE WindowID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F5' );
    }
    $window[ 'CashAdjustments' ] = (int) $res[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( Amount ), 0 ) AS Total ' .
           '  FROM ButtonAdjustments ' .
           ' WHERE WindowID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F6' );
    }
    $window[ 'ButtonAdjustments' ] = (int) $res[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumItems * ItemPrice ), 0 ) AS Total ' .
           '  FROM ConcessionSales LEFT JOIN ConcessionItems ' .
           '    ON ItemID = ConcessionItems.ID ' .
           ' WHERE WindowID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F7' );
    }
    $window[ 'ConcessionSales' ] = (int) $res[ 0 ][ 'Total' ];

    $sql = '  SELECT PerformanceWindows.ID, StartingTickets, PerformanceID, ' .
           '         Shows.Name AS ShowName, DATE_FORMAT( PerformanceTime, \'%l:%i %p\' ) AS StartTime, ' .
           '         SUBTIME( SalesStopAt, \'00:05\' ) AS WindowClosesAt, TicketPrice, ToArtist, ' .
           '         Venues.Name AS VenueName, Artists.Name AS ArtistName ' .
           '    FROM PerformanceWindows JOIN ' .
           '         (((Performances JOIN Shows ON Performances.ShowID = Shows.ID) JOIN ' .
           '          Venues ON Performances.VenueID = Venues.ID) JOIN ' .
           '           Artists ON Shows.ArtistID = Artists.ID) ' .
           '      ON PerformanceWindows.PerformanceID = Performances.ID ' .
           '   WHERE PerformanceWindows.WindowID = ? ' .
           'ORDER BY ShowName';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'F8' );
    }

    $window[ 'pwindows' ] = $res;

    $n = count( $window[ 'pwindows' ] );
    if ($n > 0) {
        for ($i = 0; $i < $n; $i++) {
            $pwindowid = $window[ 'pwindows' ][ $i ][ 'ID' ];
            $perfid = $window[ 'pwindows' ][ $i ][ 'PerformanceID' ];

            $params = array( $pwindowid );
            $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Totals ' .
                   '  FROM CashSales ' .
                   ' WHERE PerformanceWindowID = ? AND ' .
                   '       PriceOverride = 0';
            $res = dbGetResult( $sql, $params, 'boxoffice' );
            if ($res === false) {
                fail( 'F9' );
            }
            $window[ 'pwindows' ][ $i ][ 'TicketSales' ] = (int) $res[ 0 ][ 'Totals' ];

            $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Totals ' .
                   '  FROM Comps ' .
                   ' WHERE PerformanceWindowID = ? AND ' .
                   '       Reason = \'Superpass\'';
            $res = dbGetResult( $sql, $params, 'boxoffice' );
            if ($res === false) {
                fail( 'F10' );
            }
            $window[ 'pwindows' ][ $i ][ 'Superpasses' ] = (int) $res[ 0 ][ 'Totals' ];

            $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Totals ' .
                   '  FROM Comps ' .
                   ' WHERE PerformanceWindowID = ? AND ' .
                   '       Reason = \'Artist\'';
            $res = dbGetResult( $sql, $params, 'boxoffice' );
            if ($res === false) {
                fail( 'F11' );
            }
            $window[ 'pwindows' ][ $i ][ 'ArtistComps' ] = (int) $res[ 0 ][ 'Totals' ];

            $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Totals ' .
                   '  FROM Comps ' .
                   ' WHERE PerformanceWindowID = ?';
            $res = dbGetResult( $sql, $params, 'boxoffice' );
            if ($res === false) {
                fail( 'F12' );
            }
            $window[ 'pwindows' ][ $i ][ 'OtherComps' ] = (int) $res[ 0 ][ 'Totals' ];

            $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Qty, ' .
                   '       Coalesce( Sum( NumTickets * PriceOverride ), 0 ) AS Amt ' .
                   '  FROM CashSales ' .
                   ' WHERE PerformanceWindowID = ? AND ' .
                   '       PriceOverride > 0 ';

            $res = dbGetResult( $sql, $params, 'boxoffice' );
            if ($res === false) {
                fail( 'F13' );
            }

            $window[ 'pwindows' ][ $i ][ 'SpecialQty' ] = (int) $res[ 0 ][ 'Qty' ];
            $window[ 'pwindows' ][ $i ][ 'SpecialAmt' ] = (int) $res[ 0 ][ 'Amt' ];

            $payout = dbGetPayoutMade( $perfid );
            $window[ 'pwindows' ][ $i ][ 'PayoutMade' ] = ($payout === false ? 'N' : 'Y');

            $cap = getRemainingCapacity( $pwindowid );
            $window[ 'pwindows' ][ $i ][ 'Remaining' ] = $cap[ 'Remaining' ];
            $window[ 'pwindows' ][ $i ][ 'Overflow' ] = $cap[ 'Overflow' ];
            $window[ 'pwindows' ][ $i ][ 'Presales' ] = $cap[ 'Presales' ];
        }
    }

    return $window;
}

function updatePresaleList( $performanceid ) {
    global $dbConns;
    
    $errormsg = 'Unable to read list of online presales from Carbonpop';
    
    $sql = 'SELECT CarbonpopShowingID FROM Performances WHERE ID = ?';
    $params = array( $performanceid );
    
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( $errormsg + ' (Code: A)' );
    }
    
    $cpopid = $res[ 0 ][ 'CarbonpopShowingID' ];
    
    dbConnect( 'carbonpop' );
    if ($dbConns[ 'carbonpop' ] == NULL) {
        fail( $errormsg + ' (Code: B)' );
    }
    $sales = getPresalesFromCPop( $cpopid );
    dbDisconnect( 'carbonpop' );

    if (count( $sales ) > 0) {
        $sql = 'INSERT IGNORE INTO Presales VALUES( ?, ?, ?, ?, ?, ? )';
        foreach ($sales as $sale) {
            $params = array( $sale[ 'Name' ], $sale[ 'OrderNum' ], $performanceid,
                             $sale[ 'NumTickets' ], 'N', $sale[ 'PassType' ] );
            dbUpdate( $sql, $params, 'boxoffice' );
        }
    }
}

function getPresaleList( $performanceid ) {
    updatePresaleList( $performanceid );

    return dbGetPresales( $performanceid );
}

function tryVenueList() {
    global $curdate;
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'G1' );
    }

    $sql = '  SELECT Windows.VenueID AS Venue, ' .
           '         Count(*) AS NumWindows ' .
           '    FROM PerformanceWindows LEFT JOIN Windows ' .
           '      ON PerformanceWindows.WindowID = Windows.ID ' .
           '   WHERE Day = ? AND ' .
           '         OpensAt <= ? AND ' .
           '         SalesStopAt >= ? ' .
           'GROUP BY Windows.VenueID';
    $params = array( $curdate, $curtime, $curtime );

    $res = dbGetResult( $sql, $params, 'boxoffice' );

    $active = array();
    if (($res !== false) && (count( $res ) > 0)) {
        foreach ($res as $row) {
            $active[ $row[ 'Venue' ] ] = $row[ 'NumWindows' ];
        }
    }

    $sql = 'SELECT Name, ID FROM Venues ORDER BY Name';

    $res = dbGetResult( $sql, NULL, 'boxoffice' );
    if ($res === false) {
        fail( 'G2' );
    }

    $venues = array();
    foreach ($res as $row) {
        $venues[] = array(
            'name'    => $row[ 'Name' ],
            'id'      => $row[ 'ID' ],
            'windows' => (int) $active[ $row[ 'ID' ] ]
        );
    }

    return $venues;
}

function tryConcessionList() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );

    $sql = 'SELECT * FROM ConcessionItems';

    $res = dbGetResult( $sql, NULL, 'boxoffice' );
    if ($res === false) {
        fail( 'H1' );
    }
    
    return $res;
}

function tryCashSale() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $pwindowid = dbSafeInt( $_POST[ 'pwindowid' ] );
    $numtickets = dbSafeInt( $_POST[ 'numtickets' ] );
    $ticketprice = dbSafeInt( $_POST[ 'ticketprice' ] );
    $artistprice = dbSafeInt( $_POST[ 'artistprice' ] );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'I1' );
    }

    checkPerformanceWindowOpen( $pwindowid );
    
    $sql = 'INSERT INTO CashSales VALUES( NULL, ?, ?, ?, ?, ?, ? )';
    $params = array( $pwindowid, $curtime, $numtickets, $note, $ticketprice, $artistprice );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'I2' );
    }

    return true;
}

function tryButtonSale() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $windowid = dbSafeInt( $_POST[ 'windowid' ] );
    $numbuttons = dbSafeInt( $_POST[ 'numbuttons' ] );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'J1' );
    }

    checkWindowOpen( $windowid );

    $sql = 'INSERT INTO ButtonSales VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $curtime, $numbuttons, $note );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'J2' );
    }

    return true;
}

function tryIssueComp() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $pwindowid = dbSafeInt( $_POST[ 'pwindowid' ] );
    $numtickets = dbSafeInt( $_POST[ 'numtickets' ] );
    $reason = getCompReason( $_POST[ 'reason' ] );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'K1' );
    }

    checkPerformanceWindowOpen( $pwindowid );

    $sql = 'INSERT INTO Comps VALUES( NULL, ?, ?, ?, ?, ? )';
    $params = array( $pwindowid, $curtime, $numtickets, $reason, $note );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'K2' );
    }

    return true;
}

function tryRedeemPresale() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $pwindowid = dbSafeInt( $_POST[ 'pwindowid' ] );
    $ordernum = dbSafeInt( $_POST[ 'ordernum' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'L1' );
    }

    checkPerformanceWindowOpen( $pwindowid );

    $sql = 'SELECT Count(*) AS NumRecords ' .
           '  FROM PerformanceWindows JOIN Presales ' .
           '    ON PerformanceWindows.PerformanceID = Presales.PerformanceID ' .
           ' WHERE OrderNum = ? AND ' .
           '       Redeemed = \'N\' AND ' .
           '       PerformanceWindows.ID = ?';
    $params = array( $ordernum, $pwindowid );

    recordExists( $sql, $params, "Order number not associated with this performance window" );

    $sql = 'UPDATE Presales ' .
           '   SET Redeemed = \'Y\' ' .
           ' WHERE OrderNum = ?';
    $params = array( $ordernum );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'L2' );
    }

    return true;
}

function tryConcessionSale() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $windowid = dbSafeInt( $_POST[ 'windowid' ] );
    $numitems = dbSafeInt( $_POST[ 'numitems' ] );
    $itemid = dbSafeInt( $_POST[ 'itemid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'M1' );
    }

    checkWindowOpen( $windowid );

    $sql = 'INSERT INTO ConcessionSales VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $itemid, $curtime, $numitems );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'M2' );
    }

    return true;
}

function tryDonation() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $windowid = dbSafeInt( $_POST[ 'windowid' ] );
    $amount = dbSafeInt( $_POST[ 'amount' ] );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'N1' );
    }

    checkWindowOpen( $windowid );

    $sql = 'INSERT INTO Donations VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $curtime, $amount, $note );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'N2' );
    }

    return true;
}

function tryCashAdjustment() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $windowid = dbSafeInt( $_POST[ 'windowid' ] );
    $amount = dbSafeInt( $_POST[ 'amount' ] );
    $reason = dbSafeEnum( $_POST[ 'reason' ], array( 'Payout', 'Surplus', 'Other' ), 'Other' );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'O1' );
    }

    checkWindowOpen( $windowid );

    $sql = 'INSERT INTO CashAdjustments VALUES( NULL, ?, ?, ?, ?, ? )';
    $params = array( $windowid, $curtime, $amount, $reason, $note );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'O2' );
    }

    return true;        
}

function tryButtonAdjustment() {
    global $curtime;

    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $windowid = dbSafeInt( $_POST[ 'windowid' ] );
    $amount = dbSafeInt( $_POST[ 'amount' ] );
    $note = dbTruncateText( $_POST[ 'note' ], 255 );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'P1' );
    }

    checkWindowOpen( $windowid );

    $sql = 'INSERT INTO ButtonAdjustments VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $curtime, $amount, $note );

    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        fail( 'P2' );
    }

    return true;
}

function tryRemainingCapacity() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $pwindowid = dbSafeInt( $_POST[ 'pwindowid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'Q1' );
    }

    return getRemainingCapacity( $pwindowid );
}

function getRemainingCapacity( $pwindowid ) {
    $sql = 'SELECT IsHomeVenue, PerformanceID, Capacity AS Remaining, Overflow ' .
           '  FROM (PerformanceWindows JOIN ' .
           '        Performances ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '       Venues ON Performances.VenueID = Venues.ID ' .
           ' WHERE PerformanceWindows.ID = ?';
    $params = array( $pwindowid );

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'R1' );
    }

    $cap = $res[ 0 ];
    if ($cap[ 'IsHomeVenue' ] == 'N') {
        $cap[ 'Overflow' ] = 0;
    }

    $perfid = $cap[ 'PerformanceID' ];
    $params = array( $perfid );

    $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Total ' .
           '  FROM CashSales JOIN PerformanceWindows ' .
           '    ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE PerformanceID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'R2' );
    }

    $cap[ 'Remaining' ] -= (int) $res[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Total ' .
           '  FROM Comps JOIN PerformanceWindows ' .
           '    ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE PerformanceID = ?';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'R3' );
    }

    $cap[ 'Remaining' ] -= (int) $res[ 0 ][ 'Total' ];

    $sql = 'SELECT Coalesce( Sum( NumTickets ), 0 ) AS Total ' .
           '  FROM Presales ' .
           ' WHERE PerformanceID = ? AND ' .
           '       Redeemed = \'Y\'';
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'R4' );
    }

    $cap[ 'Remaining' ] -= (int) $res[ 0 ][ 'Total' ];

    $cap[ 'Presales' ] = getPresaleList( $perfid );

    return $cap;
}

function tryInfoTentSales() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $perfid = dbSafeInt( $_POST[ 'performanceid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'S1' );
    }

    $params = array( $perfid );

    $sql = 'SELECT Sum( NumTickets ) AS Total ' .
           '  FROM CashSales JOIN PerformanceWindows ' .
           '    ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           ' wHERE PerformanceWindows.PerformanceID = ? AND ' .
           '       IsHomeVenue = \'N\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        fail( 'S2' );
    }
    $cashsales = $row[ 0 ][ 'Total' ];

    $sql = 'SELECT Sum( NumTickets ) AS Total ' .
           '  FROM Comps JOIN PerformanceWindows ' .
           '    ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           ' wHERE PerformanceWindows.PerformanceID = ? AND ' .
           '       IsHomeVenue = \'N\'';
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        fail( 'S3' );
    }
    $comps = $row[ 0 ][ 'Total' ];

    $totaltix = array( 'Sales' => ($cashsales + $comps) );
    return $totaltix;
}

function tryMediaNames() {
    $user = dbSafeUserPass( $_POST[ 'username' ] );
    $sessauth = dbSafeUserPass( $_POST[ 'sessionid' ] );
    $perfid = dbSafeInt( $_POST[ 'performanceid' ] );
    $ip = $_SERVER[ 'REMOTE_ADDR' ];

    if (!checkAuth( $user, $ip, $sessauth )) {
        fail( 'T1' );
    }

    $params = array( $perfid );
    $sql = 'SELECT DISTINCT Note AS MediaName ' .
           '  FROM Comps JOIN PerformanceWindows ' .
           '    ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           ' wHERE PerformanceWindows.PerformanceID = ? AND ' .
           '       Comps.Reason = \'Media\'';
    
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res === false) {
        fail( 'T2' );
    }

    $names = array();
    foreach ($res as $row) {
        $names[] = $row[ 'MediaName' ];
    }
    
    return $names;
}

function dbFormDeleteCashSale( $id ) {
    $params = array( $id );
    $sql = 'SELECT PerformanceID ' .
           '  FROM CashSales JOIN PerformanceWindows ' .
           '    ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE CashSales.ID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    $pid = $row[ 0 ][ 'PerformanceID' ];

    $sql = 'DELETE FROM CashSales WHERE ID = ?';
    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        $pid = false;
    }
    
    return $pid;    
}

function dbFormDeleteComp( $id ) {
    $params = array( $id );
    $sql = 'SELECT PerformanceID ' .
           '  FROM Comps JOIN PerformanceWindows ' .
           '    ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE Comps.ID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    $pid = $row[ 0 ][ 'PerformanceID' ];
    
    $sql = 'DELETE FROM Comps WHERE ID = ?';
    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        $pid = false;
    }
    
    return $pid;    
}

function dbFormDeleteWindowSale( $id, $tblname ) {
    $params = array( $id );
    $sql = 'SELECT VenueID, Day ' .
           '  FROM ' . $tblname . ' JOIN Windows ' .
           '    ON ' . $tblname . '.WindowID = Windows.ID ' .
           ' WHERE ' . $tblname . '.ID = ?';
           
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return $false;
    }
    
    $wspec = array( $row[ 0 ][ 'VenueID' ], $row[ 0 ][ 'Day' ] );

    $sql = 'DELETE FROM ' . $tblname . ' WHERE ID = ?';
    if (!dbUpdate( $sql, $params, 'boxoffice' )) {
        $wspec = false;
    }
    
    return $wspec;
}

function dbFormEditVenueDetails( $id, $capacity, $overflow, $float, $buttons ) {
    $sql = 'UPDATE Venues SET Capacity = ?, Overflow = ? WHERE ID = ?';
    $params = array( $capacity, $overflow, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'UPDATE Windows SET StartingFloat = ?, StartingButtons = ? WHERE VenueID = ?';
    $params = array( $float, $buttons, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    return true;
}

function dbFormEditShowDetails( $id, $ticketprice, $toartist, $gstnumber, $wholdingtax ) {
    $sql = 'UPDATE Shows SET TicketPrice = ?, ToArtist = ? WHERE ID = ?';
    $params = array( $ticketprice, $toartist, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'SELECT ArtistID FROM Shows WHERE ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $sql = 'UPDATE Artists SET GSTNumber = ?, WithholdingTax = ? WHERE ID = ?';
    $params = array( $gstnumber, $wholdingtax, $row[ 0 ][ 'ArtistID' ] );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    return true;
}

function dbFormEditPerformanceDetails( $id, $venueid, $time, $infotix, $infostop, $venuetix, $venuestop ) {
    $sql = 'UPDATE Performances SET VenueID = ?, PerformanceTime = ? WHERE ID = ?';
    $params = array( $venueid, $time, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'UPDATE PerformanceWindows SET StartingTickets = ?, SalesStopAt = ? WHERE PerformanceID = ? AND IsHomeVenue = \'N\'';
    $params = array( $infotix, $infostop, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'UPDATE PerformanceWindows SET StartingTickets = ?, SalesStopAt = ? WHERE PerformanceID = ? AND IsHomeVenue = \'Y\'';
    $params = array( $venuetix, $venuestop, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    return true;
}

function dbFormMakeFloats( $amount, $day, $venues ) {
    $sql = 'UPDATE Windows SET StartingFloat = ? WHERE VenueID = ? AND Day = ?';

    if (count( $venues ) > 0) {
        foreach ($venues as $venue) {
            $params = array( $amount, $venue, $day );
            dbUpdate( $sql, $params, 'boxoffice' );
        }
    }
    
    return true;
}

function dbGetButtonSaleLineItem( $id ) {
    $sql = 'SELECT Day, SaleTime, NumButtons, Note, Venues.Name AS Venue ' .
           '  FROM (ButtonSales JOIN Windows ON ButtonSales.WindowID = Windows.ID) JOIN Venues ' .
           '    ON Windows.VenueID = Venues.ID ' .
           ' WHERE ButtonSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    $lineitem = array(
        'Name' => 'Button Sale',
        'Data' => array(
            'ID' => array( 'Value' => $id, 'Type' => 'readonly' ),
            'Venue' => array( 'Value' => $row[ 0 ][ 'Venue' ], 'Type' => 'readonly' ),
            'Day' => array( 'Value' => $row[ 0 ][ 'Day' ], 'Type' => 'readonly' ),
            'SaleTime' => array( 'Value' => $row[ 0 ][ 'SaleTime' ], 'Type' => 'time' ),
            'NumButtons' => array( 'Value' => $row[ 0 ][ 'NumButtons' ], 'Type' => 'int' ),
            'Note' => array( 'Value' => $row[ 0 ][ 'Note' ], 'Type' => 'string', 'Params' => 50 )
        )
    );
    
    return $lineitem;
}

function dbGetConcessionSaleLineItem( $id ) {
    $sql = 'SELECT Day, SaleTime, NumItems, Venues.Name AS Venue, ' .
           '       Concat( ConcessionItems.Name, " @ $", Format( ItemPrice / 100, 2 ) ) AS ItemName ' .
           '  FROM ((ConcessionSales JOIN Windows ON ConcessionSales.WindowID = Windows.ID) JOIN ' .
           '        ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID) JOIN ' .
           '        Venues ON Windows.VenueID = Venues.ID ' .
           ' WHERE ConcessionSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $itemlist = dbGetConcessionItemParamList();

    $lineitem = array(
        'Name' => 'Concession Sale',
        'Data' => array(
            'ID' => array( 'Value' => $id, 'Type' => 'readonly' ),
            'Venue' => array( 'Value' => $row[ 0 ][ 'Venue' ], 'Type' => 'readonly' ),
            'Day' => array( 'Value' => $row[ 0 ][ 'Day' ], 'Type' => 'readonly' ),
            'SaleTime' => array( 'Value' => $row[ 0 ][ 'SaleTime' ], 'Type' => 'time' ),
            'ItemName' => array( 'Value' => $row[ 0 ][ 'ItemName' ], 'Type' => 'enum', 'Params' => $itemlist ),
            'NumItems' => array( 'Value' => $row[ 0 ][ 'NumItems' ], 'Type' => 'int' )
        )
    );

    return $lineitem;
}

function dbGetDonationLineItem( $id ) {
    $sql = 'SELECT Day, DonationTime, Amount, Note, Venues.Name AS Venue ' .
           '  FROM (Donations JOIN Windows ON Donations.WindowID = Windows.ID) JOIN Venues ' .
           '    ON Windows.VenueID = Venues.ID ' .
           ' WHERE Donations.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    $lineitem = array(
        'Name' => 'Donation',
        'Data' => array(
            'ID' => array( 'Value' => $id, 'Type' => 'readonly' ),
            'Venue' => array( 'Value' => $row[ 0 ][ 'Venue' ], 'Type' => 'readonly' ),
            'Day' => array( 'Value' => $row[ 0 ][ 'Day' ], 'Type' => 'readonly' ),
            'DonationTime' => array( 'Value' => $row[ 0 ][ 'DonationTime' ], 'Type' => 'time' ),
            'Amount' => array( 'Value' => $row[ 0 ][ 'Amount' ], 'Type' => 'currency' ),
            'Note' => array( 'Value' => $row[ 0 ][ 'Note' ], 'Type' => 'string', 'Params' => 50 )
        )
    );
    
    return $lineitem;
}

function dbGetPresaleLineItem( $ordernum ) {
    $sql = 'SELECT Concat( Shows.Name, " (", PerformanceDate, " @ ", PerformanceTime, ")" ) AS Performance, ' .
           '       Presales.Name AS GuestName, NumTickets, Redeemed, PassType ' .
           '  FROM (Presales JOIN Performances ON Presales.PerformanceID = Performances.ID) JOIN ' .
           '        Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE OrderNum = ?';
    $params = array( $ordernum );


    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    $manual = ($row[ 0 ][ 'PassType' ] == 'Manual' ? true : false);
    if ($manual) {
        $pdefns = dbGetPerformancesParamList();
    }

    $lineitem = array(
        'Name' => 'Presale',
        'Data' => array(
            'OrderNum' => array( 'Value' => $ordernum, 'Type' => 'readonly' ),
            'GuestName' => array( 'Value' => $row[ 0 ][ 'GuestName' ], 'Type' => ($manual ? 'string' : 'readonly'), 'Params' => 40 ),
            'Performance' => array( 'Value' => $row[ 0 ][ 'Performance' ], 'Type' => ($manual ? 'enum' : 'readonly'), 'Params' => $pdefns ),
            'NumTickets' => array( 'Value' => $row[ 0 ][ 'NumTickets' ], 'Type' => ($manual ? 'int' : 'readonly') ),
            'PickedUp' => array( 'Value' => $row[ 0 ][ 'Redeemed' ], 'Type' => 'bool' ),
            'PassType' => array( 'Value' => $row[ 0 ][ 'PassType' ], 'Type' => 'readonly' )
        )
    );
    
    return $lineitem;
}

function dbGetTicketSaleLineItem( $id ) {
    $sql = 'SELECT SaleTime, NumTickets, Note, PriceOverride, ToArtistOverride, Venues.Name AS Venue, ' .
           '       Concat( Shows.Name, " (", PerformanceDate, " @ ", PerformanceTime, ")" ) AS Performance ' .
           '  FROM (((CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID) JOIN ' .
           '        Performances ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '        Venues ON Performances.VenueID = Venues.ID) JOIN ' .
           '        Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE CashSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $lineitem = array(
        'Name' => 'Ticket Sale',
        'Data' => array(
            'ID' => array( 'Value' => $id, 'Type' => 'readonly' ),
            'Venue' => array( 'Value' => $row[ 0 ][ 'Venue' ], 'Type' => 'readonly' ),
            'Performance' => array( 'Value' => $row[ 0 ][ 'Performance' ], 'Type' => 'readonly' ),
            'SaleTime' => array( 'Value' => $row[ 0 ][ 'SaleTime' ], 'Type' => 'time' ),
            'NumTickets' => array( 'Value' => $row[ 0 ][ 'NumTickets' ], 'Type' => 'int' ),
            'Note' => array( 'Value' => $row[ 0 ][ 'Note' ], 'Type' => 'string', 'Params' => 50 ),
            'PriceOverride' => array( 'Value' => $row[ 0 ][ 'PriceOverride' ], 'Type' => 'currency' ),
            'ToArtistOverride' => array( 'Value' => $row[ 0 ][ 'ToArtistOverride' ], 'Type' => 'currency' )
        )
    );

    return $lineitem;
}

function dbGetTicketCompLineItem( $id ) {
    $sql = 'SELECT SaleTime, NumTickets, Reason, Note, Venues.Name AS Venue, ' .
           '       Concat( Shows.Name, " (", PerformanceDate, " @ ", PerformanceTime, ")" ) AS Performance ' .
           '  FROM (((Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID) JOIN ' .
           '        Performances ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '        Venues ON Performances.VenueID = Venues.ID) JOIN ' .
           '        Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE Comps.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    $reasons = dbGetCompReasonsParamList();

    $lineitem = array(
        'Name' => 'Ticket Comp',
        'Data' => array(
            'ID' => array( 'Value' => $id, 'Type' => 'readonly' ),
            'Venue' => array( 'Value' => $row[ 0 ][ 'Venue' ], 'Type' => 'readonly' ),
            'Performance' => array( 'Value' => $row[ 0 ][ 'Performance' ], 'Type' => 'readonly' ),
            'SaleTime' => array( 'Value' => $row[ 0 ][ 'SaleTime' ], 'Type' => 'time' ),
            'NumTickets' => array( 'Value' => $row[ 0 ][ 'NumTickets' ], 'Type' => 'int' ),
            'Reason' => array( 'Value' => $row[ 0 ][ 'Reason' ], 'Type' => 'enum', 'Params' => $reasons ),
            'Note' => array( 'Value' => $row[ 0 ][ 'Note' ], 'Type' => 'string', 'Params' => 50 )
        )
    );

    return $lineitem;
}

function dbFormNewButtonSale( $venueid, $day, $saletime, $numbuttons, $note ) {
    $windowid = dbGetWindowIDByVenueAndDay( $venueid, $day );
    if ($windowid === false) {
        return false;
    }

    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'INSERT INTO ButtonSales VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $saletime, $numbuttons, $noteparam );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormNewConcessionSale( $venueid, $day, $saletime, $itemid, $numitems ) {
    $windowid = dbGetWindowIDByVenueAndDay( $venueid, $day );
    if ($windowid === false) {
        return false;
    }

    $sql = 'INSERT INTO ConcessionSales VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $itemid, $saletime, $numitems );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormNewDonation( $venueid, $day, $donationtime, $amount, $note ) {
    $windowid = dbGetWindowIDByVenueAndDay( $venueid, $day );
    if ($windowid === false) {
        return false;
    }

    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'INSERT INTO Donations VALUES( NULL, ?, ?, ?, ? )';
    $params = array( $windowid, $donationtime, $amount, $noteparam );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormNewPresale( $pid, $ordernum, $guestname, $numtickets, $redeemed ) {
    $sql = 'INSERT INTO Presales VALUES( ?, ?, ?, ?, ?, \'Manual\' )';
    $params = array( $guestname, $ordernum, $pid, $numtickets, $redeemed );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormNewTicketSale( $pid, $info, $saletime, $numtickets, $priceoverride, $toartistoverride, $note ) {
    $homevenue = ($info == 'Y' ? 'N' : 'Y');

    $sql = 'SELECT ID FROM PerformanceWindows WHERE PerformanceID = ? AND IsHomeVenue = ?';
    $params = array( $pid, $homevenue );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $pwid = $row[ 0 ][ 'ID' ];

    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'INSERT INTO CashSales VALUES( NULL, ?, ?, ?, ?, ?, ? )';
    $params = array( $pwid, $saletime, $numtickets, $noteparam, $priceoverride, $toartistoverride );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormNewTicketComp( $pid, $info, $saletime, $numtickets, $reason, $note ) {
    $homevenue = ($info == 'Y' ? 'N' : 'Y');

    $sql = 'SELECT ID FROM PerformanceWindows WHERE PerformanceID = ? AND IsHomeVenue = ?';
    $params = array( $pid, $homevenue );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $pwid = $row[ 0 ][ 'ID' ];
    
    $reasons = dbGetCompReasonsParamList();

    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'INSERT INTO Comps VALUES( NULL, ?, ?, ?, ?, ? )';
    $params = array( $pwid, $saletime, $numtickets, $reasons[ $reason ], $noteparam );

    return dbUpdate( $sql, $params, 'boxoffice' );
}

function dbFormEditButtonSale( $id, $saletime, $numbuttons, $note ) {
    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'UPDATE ButtonSales SET SaleTime = ?, NumButtons = ?, Note = ? WHERE ID = ?';
    $params = array( $saletime, $numbuttons, $noteparam, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'SELECT VenueID, Day ' .
           '  FROM ButtonSales JOIN Windows ON ButtonSales.WindowID = Windows.ID ' .
           ' WHERE ButtonSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    return array( $row[ 0 ][ 'VenueID' ], $row[ 0 ][ 'Day' ] );
}

function dbFormEditConcessionSale( $id, $saletime, $itemid, $numitems ) {
    $sql = 'UPDATE ConcessionSales SET ItemID = ?, SaleTime = ?, NumItems = ? WHERE ID = ?';
    $params = array( $itemid, $saletime, $numitems, $id );
    dbUpdate( $sql, $params, 'boxoffice' );

    $sql = 'SELECT VenueID, Day ' .
           '  FROM ConcessionSales JOIN Windows ON ConcessionSales.WindowID = Windows.ID ' .
           ' WHERE ConcessionSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    return array( $row[ 0 ][ 'VenueID' ], $row[ 0 ][ 'Day' ] );
}

function dbFormEditDonation( $id, $donationtime, $amount, $note ) {
    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'UPDATE Donations SET DonationTime = ?, Amount = ?, Note = ? WHERE ID = ?';
    $params = array( $donationtime, $amount, $noteparam, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'SELECT VenueID, Day ' .
           '  FROM Donations JOIN Windows ON Donations.WindowID = Windows.ID ' .
           ' WHERE Donations.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }

    return array( $row[ 0 ][ 'VenueID' ], $row[ 0 ][ 'Day' ] );
}

function dbFormEditPresale( $pid, $ordernum, $guestname, $numtickets, $redeemed ) {
    if ($pid == 0) {
        $sql = 'SELECT PerformanceID FROM Presales WHERE OrderNum = ?';
        $params = array( $ordernum );
        $row = dbGetResult( $sql, $params, 'boxoffice' );
        if ($row === false) {
            return false;
        }
        $pid = $row[ 0 ][ 'PerformanceID' ];
        
        $sql = 'UPDATE Presales SET Redeemed = ? WHERE OrderNum = ?';
        $params = array( $redeemed, $ordernum );
    } else {
        $sql = 'UPDATE Presales SET Name = ?, PerformanceID = ?, NumTickets = ?, Redeemed = ? WHERE OrderNum = ?';
        $params = array( $guestname, $pid, $numtickets, $redeemed, $ordernum );
    }

    dbUpdate( $sql, $params, 'boxoffice' );

    return $pid;
}

function dbFormEditTicketSale( $id, $saletime, $numtickets, $priceoverride, $toartistoverride, $note ) {
    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }

    $sql = 'UPDATE CashSales SET SaleTime = ?, NumTickets = ?, Note = ?, PriceOverride = ?, ToArtistOverride = ? WHERE ID = ?';
    $params = array( $saletime, $numtickets, $noteparam, $priceoverride, $toartistoverride, $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'SELECT PerformanceID ' .
           '  FROM CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE CashSales.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $pid = $row[ 0 ][ 'PerformanceID' ];

    return $pid;
}

function dbFormEditTicketComp( $id, $saletime, $numtickets, $reason, $note ) {
    if (trim( $note ) != '') {
        $noteparam = trim( $note );
    }
    
    $reasons = dbGetCompReasonsParamList();

    $sql = 'UPDATE Comps SET SaleTime = ?, NumTickets = ?, Note = ?, Reason = ? WHERE ID = ?';
    $params = array( $saletime, $numtickets, $noteparam, $reasons[ $reason ], $id );
    dbUpdate( $sql, $params, 'boxoffice' );
    
    $sql = 'SELECT PerformanceID ' .
           '  FROM Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE Comps.ID = ?';
    $params = array( $id );

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $pid = $row[ 0 ][ 'PerformanceID' ];

    return $pid;
}

function tableToAssoc( $table, $field = 'ID' ) {
    $array = array();
    
    foreach ($table as $row) {
        $array[ $row[ $field ] ] = $row;
    }
    
    return $array;
}

?>
