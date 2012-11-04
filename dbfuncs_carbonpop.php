<?php

require_once( 'dblib.php' );

$dbConn[ 'carbonpop' ] = NULL;
dbInitParams( 'carbonpop' );


function translatePassType( &$pass ) {
    $newtype = trim( $pass[ 'PassType' ] );

    if (trim( $pass[ 'Name' ] ) == 'Neal Doncaster') {
        $newtype = 'Super';
    } else if ($newtype == 'None') {
        // NOP
    } else if ($newtype == 'corporate') {
        if (isset( $pass[ 'pass_id' ] )) {
            $newtype = 'Super';
        } else {
            $newtype = 'Promo';
        }
    } else if ($newtype == 'superpass') {
        $newtype = 'Super';
    } else {
        $newtype = 'Promo';
    }
    
    $pass[ 'PassType' ] = $newtype;
}

function getPresalesFromCPop( $showingid ) {
    $sql = 'SELECT orders.id AS OrderNum, ' .
           '       Coalesce( customers.name, Concat( card_first_name, " ", card_last_name ), promo_codes.name ) AS Name, ' .
           '       (SELECT Sum( quantity_ordered ) FROM line_items WHERE order_id = orders.id) AS NumTickets, ' .
           '       promo_codes.pass_id, Coalesce( promo_codes.kind, \'None\' ) AS PassType ' .
           '  FROM ((orders LEFT JOIN customers ON orders.customer_id = customers.id) LEFT JOIN ' .
           '        promo_codes ON orders.promo_code_id = promo_codes.id) LEFT JOIN ' .
           '        passes ON Coalesce( orders.pass_id, promo_codes.pass_id ) = passes.id ' .
           ' WHERE orders.completed = 1 AND ' .
           '       orders.refunded = 0 AND ' .
           '       orders.voided = 0 AND ' .
           '       orders.showing_id = ?';
    $params = array( $showingid );

    $sales = dbGetResult( $sql, $params, 'carbonpop' );
    if ($sales === false) {
        $sales = array();
    } else {
        $n = count( $sales );
        for ($i = 0; $i < $n; $i++) {
            translatePassType( $sales[ $i ] );
        }
    }
   
    return $sales;
}

function checkCPopPresalesByName( $guestname, $datespan ) {
    global $dbError;
    
    if (trim( $guestname ) != '') {
        $guestname = '%' . $guestname . '%';
    }
    
    $sql = '  SELECT orders.id AS OrderNum, ' .
           '         Coalesce( customers.name, Concat( card_first_name, " ", card_last_name ), promo_codes.name ) AS Name, ' .
           '         (SELECT Sum( quantity_ordered ) FROM line_items WHERE order_id = orders.id) AS NumTickets, ' .
           '         orders.created_at AS WhenOrderPlaced, Coalesce( promo_codes.kind, \'None\' ) AS PassType, ' .
           '         showings.id AS Performance ' .
           '    FROM (((orders LEFT JOIN customers ON orders.customer_id = customers.id) LEFT JOIN ' .
           '          promo_codes ON orders.promo_code_id = promo_codes.id) JOIN ' .
           '          showings ON orders.showing_id = showings.id) LEFT JOIN ' .
           '          passes ON Coalesce( orders.pass_id, promo_codes.pass_id ) = passes.id ' .
           '   WHERE orders.completed = 1 AND ' .
           '         orders.refunded = 0 AND ' .
           '         orders.voided = 0 AND ' .
           '         customers.name LIKE ? AND ' .
           '         showings.shows_at BETWEEN ? AND  ? ' .
           'ORDER BY customers.name, orders.created_at';
    $params = array( $guestname, $datespan[ 'StartDate' ] . ' 00:00:00', $datespan[ 'EndDate' ] . ' 23:59:59' );

    $sales = dbGetResult( $sql, $params, 'carbonpop' );
    if ($sales === false) {
        $sales = array();
    } else {
        $n = count( $sales );
        for ($i = 0; $i < $n; $i++) {
            translatePassType( $sales[ $i ] );
        }
    }
   
    return $sales;
}

function getCPopOrderBreakdown( $showingid ) {
    $sql = '  SELECT Coalesce( promo_codes.kind, \'None\') AS PassType, ' .
           '         Sum( line_items.quantity_ordered ) AS NumTickets, ' .
           '         If( (line_items.unit_price = 0) OR (orders.total = 0), \'Y\', \'N\' ) AS UsedPass, ' .
           '         orders.customer_id AS CustomerID ' .
           '    FROM (orders LEFT JOIN promo_codes ON orders.promo_code_id = promo_codes.id) LEFT JOIN ' .
           '          line_items ON orders.id = line_items.order_id ' .
           '   WHERE orders.completed = 1 AND ' .
           '         orders.refunded = 0 AND ' .
           '         orders.voided = 0 AND ' .
           '         orders.showing_id = ? ' .
           'GROUP BY line_items.unit_price, orders.total, promo_codes.kind, orders.customer_id';
    $params = array( $showingid );
    
    return dbGetResult( $sql, $params, 'carbonpop' );
}

function getCPopFestivalData( $startdate, $enddate ) {
    $params = array( $startdate . ' 00:00:00', $enddate . ' 23:59:59' );
    $okcategories = '(7, 22, 30, 32)';

    $sql = '  SELECT DISTINCT showings.id, event_id, performer_id, venue_id, shows_at, free_show ' .
           '    FROM events JOIN showings ON events.id = showings.event_id ' .
           '   WHERE showings.deleted_at IS NULL AND ' .
           '         events.deleted_at IS NULL AND ' .
           '         category_id IN ' . $okcategories . ' AND ' .
           '         shows_at BETWEEN ? AND ? ' .
           'ORDER BY showings.id';
    
    $showings = dbGetResult( $sql, $params, 'carbonpop' );
    if ($showings === false) {
        $showings = array();
    }
    
    $sql = '  SELECT DISTINCT events.id, name, performer_id, category_id ' .
           '    FROM events JOIN showings ON events.id = showings.event_id ' .
           '   WHERE showings.deleted_at IS NULL AND ' .
           '         events.deleted_at IS NULL AND ' .
           '         category_id IN ' . $okcategories . ' AND ' .
           '         shows_at BETWEEN ? AND ? ' .
           'ORDER BY events.id';
           
    $events = dbGetResult( $sql, $params, 'carbonpop' );
    if ($events === false) {
        $events = array();
    }

    $sql = '  SELECT DISTINCT performers.id, performers.name ' .
           '    FROM (performers JOIN events ON performers.id = events.performer_id) JOIN ' .
           '          showings ON events.id = showings.event_id ' .
           '   WHERE showings.deleted_at IS NULL AND ' .
           '         events.deleted_at IS NULL AND ' .
           '         category_id IN ' . $okcategories . ' AND ' .
           '         shows_at BETWEEN ? AND ? ' .
           'ORDER BY performers.id';
    
    $performers = dbGetResult( $sql, $params, 'carbonpop' );
    if ($performers === false) {
        $performers = array();
    }
    
    $sql = '  SELECT DISTINCT events.id, price ' .
           '    FROM (showings JOIN tickets ON showings.id = tickets.showing_id) JOIN ' .
           '          events ON showings.event_id = events.id ' .
           '   WHERE kind = \'door\' AND ' .
           '         showings.deleted_at IS NULL AND ' .
           '         events.deleted_at IS NULL AND ' .
           '         category_id IN ' . $okcategories . ' AND ' .
           '         shows_at BETWEEN ? AND ? ' .
           'ORDER BY event_id';
    
    $ticketprices = dbGetResult( $sql, $params, 'carbonpop' );
    if ($ticketprices === false) {
        $ticketprices = array();
    }
    
    $sql = '  SELECT showings.id, count(*) as numtickets ' .
           '    FROM showings JOIN tickets ON showings.id = tickets.showing_id ' .
           '   WHERE kind = \'door\' AND ' .
           '         showings.deleted_at IS NULL AND ' .
           '         shows_at BETWEEN ? AND ? ' .
           'GROUP BY showings.id';
    
    $ticketnums = dbGetResult( $sql, $params, 'carbonpop' );
    if ($ticketnums === false) {
        $ticketnums = array();
    }
    
    return array(
        'Showings'     => $showings,
        'Events'       => $events,
        'Performers'   => $performers,
        'TicketPrices' => $ticketprices,
        'TicketNums'   => tableToAssoc( $ticketnums, 'id' )
    );
}

function getCPopOnlineSoldOut( $startdate, $enddate, $cutoff = 0 ) {
    $sql = '  SELECT * ' .
           '    FROM (SELECT showings.shows_at, events.name AS title, ' .
           '                 (SELECT Count(*) ' .
           '                    FROM tickets ' .
           '                   WHERE kind = \'advanced\' AND ' .
           '                         purchased_at IS NULL AND ' .
           '                         showing_id = showings.id) AS remaining ' .
           '            FROM showings JOIN events ON showings.event_id = events.id ' .
           '           WHERE events.category_id in (7, 30) AND ' .
           '                 venue_id <> 40 AND ' .
           '                 shows_at BETWEEN ? AND ?) AS remainingtickets ' .
           '   WHERE remaining <= ? ' .
           'ORDER BY shows_at';
    $params = array( $startdate, $enddate, $cutoff );
    
    return dbGetResult( $sql, $params, 'carbonpop' );
}

function getCPopSuperpassPresales( $startdate = NULL, $enddate = NULL ) {
    global $thisYear;
    
    if ($startdate == NULL) {
        $startdate = $thisYear . '-07-01';
    }
    if ($enddate == NULL) {
        $enddate = $thisYear . '-08-01';
    }
    
    $presales = array(
        'PresoldFringers' => 0,
        'PresoldBuddys' => 0,
        'PresoldBingers' => 0
    );
    
    $sql = '  SELECT ticket_minimum, sum( total ) * 100 AS amt ' .
           '    FROM orders JOIN passes ON orders.pass_id = passes.id ' .
           '   WHERE completed = 1 AND ' .
           '         refunded = 0 AND ' .
           '         voided = 0 AND ' .
           '         order_type = \'PassOrder\' AND ' .
           '         orders.created_at BETWEEN ? AND ? ' .
           'GROUP BY pass_id';
    $params = array( $startdate, $enddate );
    
    $rows = dbGetResult( $sql, $params, 'carbonpop' );
    if ($rows === false) {
        return $presales;
    }
    
    foreach ($rows as $row) {
        switch ($row[ 'ticket_minimum' ]) {
            case 5:
                $presales[ 'PresoldFringers' ] = $row[ 'amt' ];
                break;
            case 10:
                $presales[ 'PresoldBuddys' ] = $row[ 'amt' ];
                break;
            case 20:
                $presales[ 'PresoldBingers' ] = $row[ 'amt' ];
                break;
        }
    }
    
    return $presales;
}

?>
