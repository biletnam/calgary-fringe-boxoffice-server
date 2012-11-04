<?php

function dbGetDailyTicketBreakdown( $day ) {
    $sql = '  SELECT Sum( NumTickets ) AS `Tickets Sold`, ' .
           '         Venues.Name AS `Ticket Sale Location`, ' .
           '         Shows.Name AS `Show Title` ' .
           '    FROM ((((CashSales JOIN ' .
           '          PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID) JOIN ' .
           '          Windows ON PerformanceWindows.WindowID = Windows.ID) JOIN ' .
           '          Venues ON Windows.VenueID = Venues.ID) JOIN ' .
           '          Performances ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '          Shows ON Performances.ShowID = Shows.ID ' .
           '   WHERE Windows.Day = ? ' .
           'GROUP BY `Ticket Sale Location`, `Show Title`';
    $params = array( $day );

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    
    return $res;
}

function dbGetArtistPayout( $perfid ) {
    $params = array( $perfid );

    $payout = array( 'tickets' => array(), 'comps' => array() );
    $total = 0;

    $sql = 'SELECT ToArtist ' .
           '  FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE Performances.ID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    $payout[ 'ToArtist' ] = $row[ 0 ][ 'ToArtist' ];

    $sql = '  SELECT Sum( NumTickets ) as Total, ToArtistOverride as Artist ' .
           '    FROM CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? AND IsHomeVenue = \'Y\' ' .
           'GROUP BY ToArtistOverride';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $pricepoint) {
            if (trim( $pricepoint[ 'Artist' ] ) == '0') {
                $payout[ 'tickets' ][ 'Default' ] = $pricepoint[ 'Total' ];
                $total += ($pricepoint[ 'Total' ] * $payout[ 'ToArtist' ]);
            } else {
                $payout[ 'tickets' ][ $pricepoint[ 'Artist' ] ] = $pricepoint[ 'Total' ];
                $total += ($pricepoint[ 'Total' ] * $pricepoint[ 'Artist' ]);
            }
        }
    }
    
    $sql = '  SELECT Sum( NumTickets ) AS Total, Reason ' .
           '    FROM Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? AND IsHomeVenue = \'Y\' ' .
           'GROUP BY Reason';
    
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $reason) {
            switch ($reason[ 'Reason' ]) {
                case 'Superpass':
                    $payout[ 'comps' ][ 'Superpass' ] = $reason[ 'Total' ];
                    $total += (1000 * $reason[ 'Total' ]);
                    break;
                case 'Artist':
                    $payout[ 'comps' ][ 'Artist' ] = $reason[ 'Total' ];
                    break;
                default:
                    $payout[ 'comps' ][ 'Free' ] += $reason[ 'Total' ];
                    break;
            }
        }
    }
    
    $media = '';
    $sql = 'SELECT DISTINCT Note ' .
           '  FROM Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? AND Reason = \'Media\'';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $medialine) {
            $media .= ', ' . $medialine[ 'Note' ];
        }
        $media = substr( $media, 2 );
    }
    $payout[ 'media' ] = $media;
    
    $payout[ 'total' ] = $total;
    
    return $payout;
}

function dbGetEndOfDayCash( $windowid ) {
    $params = array( $windowid );
    
    $sql = 'SELECT Sum( NumTickets * TicketPrice ) AS Total ' .
           '  FROM ((CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID) JOIN ' .
           '        Performances ON PerformanceWindows.PerformanceID = Performances.ID) JOIN ' .
           '        Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE WindowID = ? AND PriceOverride = 0';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row !== false) {
        $normaltix = $row[ 0 ][ 'Total' ];
    }
    
    $sql = 'SELECT Sum( NumTickets * PriceOverride ) AS Total ' .
           '  FROM CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           ' WHERE WindowID = ? AND PriceOverride > 0';
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row !== false) {
        $specialtix = $row[ 0 ][ 'Total' ];
    }
    
    $sql = 'SELECT (Sum( NumButtons ) * 500) AS Total ' .
           '  FROM ButtonSales ' .
           ' WHERE WindowID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row !== false) {
        $buttons  = $row[ 0 ][ 'Total' ];
    }
    
    $sql = 'SELECT Sum( NumItems * ItemPrice ) AS Total ' .
           '  FROM ConcessionSales JOIN ConcessionItems ON ConcessionSales.ItemID = ConcessionItems.ID ' .
           ' WHERE WindowID = ? AND ConcessionItems.Name LIKE \'superpass%\'';
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row !== false) {
        $superpasses = $row[ 0 ][ 'Total' ];
    }
    
    $payouts = 0;
    $surplus = 0;

    $sql = '  SELECT Sum( Amount ) AS Total, Reason ' .
           '    FROM CashAdjustments ' .
           '   WHERE WindowID = ? AND Reason <> \'Other\' ' .
           'GROUP BY Reason';
    
    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $row) {
             if ($row[ 'Reason' ] == 'Surplus') {
                 $surplus = $row[ 'Total' ];
             } else if ($row[ 'Reason' ] == 'Payout') {
                 $payouts = $row[ 'Total' ];
             }
        }
    }
    
    $total = $normaltix + $specialtix + $buttons + $superpasses - ($payouts + $surplus);
    $eod = array(
        'Normal' => $normaltix,
        'Special' => $specialtix,
        'Buttons' => $buttons,
        'Superpass' => $superpasses,
        'Payouts' => $payouts,
        'Surplus' => $surplus,
        'Summary' => $total
    );

    return $eod;
}

function dbGetWindowListByDay( $day ) {
    $sql = '  SELECT Windows.ID, Venues.Name ' .
           '    FROM Windows JOIN Venues ON Windows.VenueID = Venues.ID ' .
           '   WHERE Day = ? ' .
           'ORDER BY Venues.Name';
    $params = array( $day );
    
    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetDayAndVenue( $windowid ) {
    $sql = 'SELECT Day, Venues.Name AS Venue ' .
           '  FROM Windows JOIN Venues ON Windows.VenueID = Venues.ID ' .
           ' WHERE Windows.ID = ?';
    $params = array( $windowid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ];
}

function dbGetPerformanceTicketSummary( $perfid ) {
    $presales = 0;
    $infosales = 0;
    $infosupers = 0;
    $infocomps = 0;
    $venuesales = 0;
    $venuesupers = 0;
    $venuecomps = 0;

    $title = dbGetPerformanceName( $perfid );

    $params = array( $perfid );

    $sql = '  SELECT Sum( NumTickets ) AS Total, IsHomeVenue ' .
           '    FROM CashSales JOIN PerformanceWindows ON CashSales.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? ' .
           'GROUP BY IsHomeVenue';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $row) {
            if ($row[ 'IsHomeVenue' ] == 'Y') {
                $venuesales = $row[ 'Total' ];
            } else {
                $infosales = $row[ 'Total' ];
            }
        }
    }
    
    $sql = '  SELECT Sum( NumTickets ) AS Total, IsHomeVenue ' .
           '    FROM Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? AND Reason <> \'Superpass\' ' .
           'GROUP BY IsHomeVenue';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $row) {
            if ($row[ 'IsHomeVenue' ] == 'Y') {
                $venuecomps = $row[ 'Total' ];
            } else {
                $infocomps = $row[ 'Total' ];
            }
        }
    }
    
    $sql = '  SELECT Sum( NumTickets ) AS Total, IsHomeVenue ' .
           '    FROM Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID ' .
           '   WHERE PerformanceID = ? AND Reason = \'Superpass\' ' .
           'GROUP BY IsHomeVenue';

    $res = dbGetResult( $sql, $params, 'boxoffice' );
    if ($res !== false) {
        foreach ($res as $row) {
            if ($row[ 'IsHomeVenue' ] == 'Y') {
                $venuesupers = $row[ 'Total' ];
            } else {
                $infosupers = $row[ 'Total' ];
            }
        }
    }
    
    $sql = 'SELECT Sum( NumTickets ) AS Total FROM Presales WHERE PerformanceID = ?';

    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row !== false) {
        $presales = (int) ($row[ 0 ][ 'Total' ]);
    }

    $total = $presales + $venuesales + $venuecomps + $infosales + $infocomps + $venuesupers + $infosupers;

    $sales = array(
        'Show Name' => $title,
        'Presales' => $presales,
        'Info Cash Sales' => $infosales,
        'Info Supers' => $infosupers,
        'Info Comps' => $infocomps,
        'Venue Cash Sales' => $venuesales,
        'Venue Supers' => $venuesupers,
        'Venue Comps' => $venuecomps,
        'Total' => '<B>' . $total . '</B>'
    );

    return $sales;
}

function dbGetPerformanceIDsByDay( $day ) {
    $sql = '  SELECT Performances.ID ' .
           '    FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           '   WHERE PerformanceDate = ? ' .
           'ORDER BY Shows.Name';
    $params = array( $day );
    
    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetPerformanceName( $pid ) {
    $sql = 'SELECT Shows.Name ' .
           '  FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE Performances.ID = ?';
    $params = array( $pid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return false;
    }
    
    return $row[ 0 ][ 'Name' ];
}

function dbGetDailyTotalTicketSummary( $day ) {
    $perfids = dbGetPerformanceIDsByDay( $day );
    
    $sumpresales = 0;
    $suminfosales = 0;
    $suminfocomps = 0;
    $suminfosupers = 0;
    $sumvenuesales = 0;
    $sumvenuesupers = 0;
    $sumvenuecomps = 0;

    $summary = array();
    foreach ($perfids as $perf) {
        $pid = $perf[ 'ID' ];
        $numbers = dbGetPerformanceTicketSummary( $pid );
        $summary[] = $numbers;

        $sumpresales += $numbers[ 'Presales' ];
        $suminfosales += $numbers[ 'Info Cash Sales' ];
        $suminfosupers += $numbers[ 'Info Supers' ];
        $suminfocomps += $numbers[ 'Info Comps' ];
        $sumvenuesales += $numbers[ 'Venue Cash Sales' ];
        $sumvenuesupers += $numbers[ 'Venue Supers' ];
        $sumvenuecomps += $numbers[ 'Venue Comps' ];
    }

    $sumtotal = $sumpresales + $sumvenuesales + $sumvenuecomps + $suminfosales + $suminfocomps;

    $totals = array(
        'Show Name' => '<B>TOTAL</B>',
        'Presales' => '<B>' . $sumpresales . '</B>',
        'Info Cash Sales' => '<B>' . $suminfosales . '</B>',
        'Info Supers' => '<B>' . $suminfosupers . '</B>',
        'Info Comps' => '<B>' . $suminfocomps . '</B>',
        'Venue Cash Sales' => '<B>' . $sumvenuesales . '</B>',
        'Venue Supers' => '<B>' . $sumvenuesupers . '</B>',
        'Venue Comps' => '<B>' . $sumvenuecomps . '</B>',
        'Total' => '<B>' . $sumtotal . '</B>'
    );
    $summary[] = $totals;
    
    return $summary;
}

function dbGetPresalesByName( $guestname ) {
    global $dbConns;
    
    dbConnect( 'carbonpop' );
    if ($dbConns[ 'carbonpop' ] == NULL) {
        return false;
    }
    $sales = checkCPopPresalesByName( $guestname, dbGetFestivalSpan() );
    dbDisconnect( 'carbonpop' );
    
    $n = count( $sales );
    if ($n > 0) {
        for ($i = 0; $i < $n; $i++) {
            $perf = dbGetPerformanceDetailsByShowingID( $sales[ $i ][ 'Performance' ] );
            $sales[ $i ][ 'Performance' ] = $perf;
        }
    }
    
    return $sales;
}

function dbGetPerformanceDetailsByShowingID( $showingid ) {
    $sql = 'SELECT Shows.Name, PerformanceDate, PerformanceTime ' .
           '  FROM Performances JOIN Shows ON Performances.ShowID = Shows.ID ' .
           ' WHERE CarbonpopShowingID = ?';
    $params = array( $showingid );
    
    $row = dbGetResult( $sql, $params, 'boxoffice' );
    if ($row === false) {
        return '';
    }
    
    $str = '<EM>' . $row[ 0 ][ 'Name' ] . '</EM> on ' . $row[ 0 ][ 'PerformanceDate' ] . ' @ ' . $row[ 0 ][ 'PerformanceTime' ];
    return $str;
}

function dbGetNumButtonsSold( $windowid ) {
    $sql = 'SELECT Sum( NumButtons ) AS Total FROM ButtonSales WHERE WindowID = ?';
    $params = array( $windowid );
    
    return dbGetResult( $sql, $params, 'boxoffice' );
}

function dbGetMediaAttSummary() {
    $span = dbGetFestivalSpan();
    
    $sql = '  SELECT Coalesce( Note, "(None Entered)" ) AS Name, Count(*) AS "# Attendances" ' .
           '    FROM (Comps JOIN PerformanceWindows ON Comps.PerformanceWindowID = PerformanceWindows.ID) JOIN ' .
           '          Windows ON PerformanceWindows.WindowID = Windows.ID ' .
           '   WHERE Reason = "Media" AND ' .
           '         Day BETWEEN ? AND ? ' .
           'GROUP BY Note ' .
           'ORDER BY Note';
    $params = array( $span[ 'StartDate' ], $span[ 'EndDate' ] );
    
    return dbGetResult( $sql, $params, 'boxoffice' );
}

function addTime( $timestr, $h = 0, $m = 0, $s = 0 ) {
    $hrs = substr( $timestr, 0, 2 ) + $h;
    $min = substr( $timestr, 3, 2 ) + $m;
    $sec = substr( $timestr, 6, 2 ) + $s;
    
    $t = ($hrs * 3600) + ($min * 60) + $sec;
    
    $newh = (int) ($t / 3600);
    $t = $t - ($newh * 3600);
    $newm = (int) ($t / 60);
    $t = $t - ($newm * 60);
    $news = $t;

    return sprintf( '%02d:%02d:%02d', $newh, $newm, $news );
}

function dbFormCreateNewFestival( $cpopdata, $startdate, $enddate ) {
    // WARNING: THIS IS NOT THREAD-SAFE AT ALL!!! USE AT YOUR OWN RISK!!!

    $firstartist = dbGetNextAutoInc( 'Artists', array( '', NULL, 'N' ), 'boxoffice' );
    $firstshow = dbGetNextAutoInc( 'Shows', array( '', 0, 0, 0 ), 'boxoffice' );
    $firstperformance = dbGetNextAutoInc( 'Performances', array( 0, 0, '2000-00-00', '00:00:00', 0 ), 'boxoffice' );
    $firstpwindow = dbGetNextAutoInc( 'PerformanceWindows', array( 0, 0, 0, '00:00:00', 0 ), 'boxoffice' );
    $firstwindow = dbGetNextAutoInc( 'Windows', array( 0, '2000-00-00', '00:00:00', '00:00:00', 0, 0 ), 'boxoffice' );

    $INFO_TENT = 1; // That's its venue id. God help us if we ever get more than one of them.

    $days = array();
    $firstday = DateTime::createFromFormat( 'Y-m-d', $startdate );
    $lastday = DateTime::createFromFormat( 'Y-m-d', $enddate );
    $int = new DateInterval( 'P1D' );
    while ($firstday <= $lastday) {
        $days[] = $firstday->format( 'Y-m-d' );
        $firstday->add( $int );
    }
    
    $sql = 'SELECT CarbonPopID, BoxOfficeID FROM IDMap WHERE MapType = \'Venue\'';
    $row = dbGetResult( $sql, NULL, 'boxoffice' );
    if ($row === false) {
       return false;
    }
    $venuemaps = tableToAssoc( $row, 'CarbonPopID' );

    $venues = array();
    foreach ($cpopdata[ 'Showings' ] as $showing) {
        $venueid = $venuemaps[ $showing[ 'venue_id' ] ][ 'BoxOfficeID' ];
        if (!in_array( $venueid, $venues )) {
            $venues[] = $venueid;
        }
    }
    
    $ticketprices = tableToAssoc( $cpopdata[ 'TicketPrices' ], 'id' );
    
    $freebies = array();
    foreach ($cpopdata[ 'Showings' ] as $showing) {
        $freebies[ $showing[ 'event_id' ] ] = $showing[ 'free_show' ];
    }
    
    // -----------------------------------------------------
    // CPOP::Performers -> BOFFICE::Artists
    $artists = array();
    $artistmaps = array();
    $artistmaps2 = array();
    
    $performers = $cpopdata[ 'Performers' ];
    $n = count( $performers );
    if ($n > 0) {
        for ($i = 0; $i < $n; $i++) {
            $id = $performers[ $i ][ 'id' ];
            
            $artists[] = array( $performers[ $i ][ 'name' ] );
            $artistmaps[] = array( $id, $firstartist + $i );
            $artistmaps2[ $id ] = $firstartist + $i;
        }
    }
    
    $sql = 'INSERT INTO Artists VALUES( NULL, ?, NULL, \'N\' )';
    dbUpdateMulti( $sql, $artists, 'boxoffice' );
    $sql = 'INSERT INTO IDMap VALUES( ?, ?, \'Artist\' )';
    dbUpdateMulti( $sql, $artistmaps, 'boxoffice' );


    // -----------------------------------------------------
    // CPOP::Events -> BOFFICE::Shows
    $shows = array();
    $showmaps = array();
    $showmaps2 = array();
    
    $events = $cpopdata[ 'Events' ];
    $n = count( $events );
    if ($n > 0) {
        for ($i = 0; $i < $n; $i++) {
            $id = $events[ $i ][ 'id' ];
            
            $artistid = $artistmaps2[ $events[ $i ][ 'performer_id' ] ];


            // We shouldn't have machine precision errors at 2 decimal places, but I've seen stranger.
            $price = (int) (($ticketprices[ $id ][ 'price' ] * 100) + 0.1); 
            if ($events[ $i ][ 'category_id' ] == 32) {
                // Category 32 is Youth Theatre - they get the entire proceeds.
                $toartist = $price;
            } else if ($freebies[ $id ] == 1) {
                $toartist = 0;
            } else {
                $toartist = $price - 150;
            }
            
            $shows[] = array( $events[ $i ][ 'name' ], $artistid, $price, $toartist );
            $showmaps[] = array( $id, $firstshow + $i );
            $showmaps2[ $id ] = $firstshow + $i;
        }
    }

    $sql = 'INSERT INTO Shows VALUES( NULL, ?, ?, ?, ? )';
    dbUpdateMulti( $sql, $shows, 'boxoffice' );
    $sql = 'INSERT INTO IDMap VALUES( ?, ?, \'Show\' )';
    dbUpdateMulti( $sql, $showmaps, 'boxoffice' );

    
    // -----------------------------------------------------
    // BOFFICE::Windows
    $windows = array();
    $windowmaps = array();
    
    $wid = $firstwindow;
    $windowmaps[ $INFO_TENT ] = array();
    foreach ($days as $day) {
        $windows[] = array( $INFO_TENT, $day, '07:00:00', 100, 30000 );
        $windowmaps[ $INFO_TENT ][ $day ] = $wid;
        $wid++;
    }
    foreach ($venues as $venueid) {
        $windowmaps[ $venueid ] = array();
        foreach ($days as $day) {
            $windows[] = array( $venueid, $day, '09:00:00', 40, 10000 );
            $windowmaps[ $venueid ][ $day ] = $wid;
            $wid++;
        }
    }
    
    $sql = 'INSERT INTO Windows VALUES( NULL, ?, ?, ?, \'23:59:59\', ?, ? )';
    dbUpdateMulti( $sql, $windows, 'boxoffice' );
    

    // -----------------------------------------------------
    // CPOP::Showings -> BOFFICE::Performances
    //                -> BOFFICE::PerformanceWindows
    $perfs = array();
    $perfmaps = array();
    
    $pwindows = array();
    $ticketnums = $cpopdata[ 'TicketNums' ];

    $showings = $cpopdata[ 'Showings' ];
    $n = count( $showings );
    if ($n > 0) {
        for ($i = 0; $i < $n; $i++) {
            $id = $showings[ $i ][ 'id' ];
            $pid = $firstperformance + $i;
            
            $showid = $showmaps2[ $showings[ $i ][ 'event_id' ] ];
            $venueid = $venuemaps[ $showings[ $i ][ 'venue_id' ] ][ 'BoxOfficeID' ];
            $showsat = trim( $showings[ $i ][ 'shows_at' ] );
            $pdate = substr( $showsat, 0, 10 );
            $ptime = substr( $showsat, -8 );
            
            $perfs[] = array( $showid, $venueid, $pdate, $ptime, $id );
            $perfmaps[] = array( $id, $pid );
            
            $stopsales_info = addTime( $ptime, 0, -40 );
            $stopsales_venue = addTime( $ptime, 0, 20 );
            $numtix = ($ticketnums[ $id ][ 'numtickets' ] / 2);

            // 999 for number of tickets to be sold at venue: in theory this number
            //  is actually ignored, and the system only uses the venue capacity.
            //  Vestigial trace of an older version of the software.
            $pwindows[] = array( $pid, $windowmaps[ $INFO_TENT ][ $pdate ], $numtix, $stopsales_info,  'N' );
            $pwindows[] = array( $pid, $windowmaps[ $venueid   ][ $pdate ], 999,     $stopsales_venue, 'Y' );
        }
    }
    
    $sql = 'INSERT INTO Performances VALUES( NULL, ?, ?, ?, ?, ? )';
    dbUpdateMulti( $sql, $perfs, 'boxoffice' );
    $sql = 'INSERT INTO IDMap VALUES( ?, ?, \'Performance\' )';
    dbUpdateMulti( $sql, $perfmaps, 'boxoffice' );
    
    $sql = 'INSERT INTO PerformanceWindows VALUES( NULL, ?, ?, ?, ?, ? )';
    dbUpdateMulti( $sql, $pwindows, 'boxoffice' );


    // -----------------------------------------------------
    // BOFFICE::Festivals
    $sql = 'DELETE FROM Festivals WHERE Year = ?';
    $params = array( $firstday->format( 'Y' ) );
    dbUpdate( $sql, $params, 'boxoffice' );
    $sql = 'INSERT INTO Festivals VALUES( ?, ?, ? )';
    $params = array( $firstday->format( 'Y' ), $startdate, $enddate );
    dbUpdate( $sql, $params, 'boxoffice' );


    // Yeah this is ugly. The engine needs to be rewritten so I can properly pass arbitrary
    //  user-data back to the UI. Later.    
    $_POST[ 'stats_artists' ]      = count( $artists );
    $_POST[ 'stats_shows'   ]      = count( $shows );
    $_POST[ 'stats_performances' ] = count( $perfs );
    $_POST[ 'stats_windows' ]      = count( $windows );
    $_POST[ 'stats_pwindows' ]     = count( $pwindows );
    
    return true;
}

?>
