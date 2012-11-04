<?php

require_once( 'dblib.php' );
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


$defaultCxn = 'carbonpop';

$queries = array(
/*
    array( 'sql' => 'select * from customers where id = 3194' ),
    array( 'sql' => 'select * from orders where id = 296689' ),
    array( 'sql' => 'select shows_at as PerformanceTime, orders.id as OrderNum, ' .
                           'coalesce( promo_codes.name, customers.name, concat( card_first_name, " ", card_last_name ) ) as GuestName, ' .
                           'orders.refunded, orders.deleted, (select count( * ) from line_items where order_id = orders.id) as NumTickets ' .
                      'from ((showings left join orders on showings.id = orders.showing_id) ' .
                           'left join customers on orders.customer_id = customers.id) ' .
                           'left join promo_codes on orders.promo_code_id = promo_codes.id ' .
                     'where event_id = 342 and completed = 1 and promo_codes.deleted_at is null order by shows_at' ),
    array( 'sql' => 'select showings.id, venues.name, events.name, showings.shows_at, tickets.kind, count(*) ' .
                      'from ((showings join events on showings.event_id = events.id) ' .
                           'left join venues on showings.venue_id = venues.id) ' .
                           'left join tickets on showings.id = tickets.showing_id ' .
                     'where showings.shows_at >= \'2011-07-01\' ' .
                  'group by venues.name, events.name, showings.shows_at, tickets.kind' ),
    array( 'sql' => 'select id from orders where showing_id = 1261' ),
    array( 'sql' => 'select id, shows_at from showings where event_id=315 order by shows_at' ),
    array( 'sql' => 'select id from events where name like \'%interruptions%\'' ),
    array( 'sql' => 'show fields from showings' ),
    array( 'sql' => 'select events.name as EventName, shows_at as PerformanceTime, orders.id as OrderNum, ' .
                           'customers.name as GuestName, passes.name as PassType, orders.refunded as Refunded, ' .
                           '(select sum( quantity_ordered ) from line_items where order_id = orders.id) as NumTickets ' .
                      'from ((((events left join showings on events.id = showings.event_id) ' .
                           'left join orders on showings.id = orders.showing_id) ' .
                           'left join customers on orders.customer_id = customers.id) ' .
                           'left join promo_codes on orders.promo_code_id = promo_codes.id) ' .
                           'left join passes on passes.id = coalesce( orders.pass_id, promo_codes.pass_id ) ' .
                     'where completed = 1 and customers.name like \'%doncaster%\' and ' .
                           'shows_at >= \'2011-07-01\' ' .
                  'order by events.name, shows_at' )
    array( 'sql' => 'show fields from orders' ),
    array( 'sql' => 'show fields from showings' ),
*/
    array( 'sql' => '  select sum( orders.total ), date( showings.shows_at ) ' .
                    '    from (orders join showings on orders.showing_id = showings.id) ' .
                    '   where orders.completed = 1 and orders.refunded = 0 and orders.voided = 0 and showings.deleted_at is null and ' .
                    '         date( showings.shows_at ) between \'2011-07-29\' and \'2011-08-02\' ' .
                    'group by date( showings.shows_at )' ),
    array( 'sql' => '  select sum( orders.total ), orders.showing_id, events.name ' .
                    '    from (orders join showings on orders.showing_id = showings.id) join events on showings.event_id = events.id ' .
                    '   where orders.completed = 1 and orders.refunded = 0 and orders.voided = 0 and showings.deleted_at is null and ' .
                    '         date( showings.shows_at ) = \'2011-07-29\' ' .
                    'group by orders.showing_id' ),
    array( 'sql' => 'select convert( 100 * sum( total ), unsigned ) from orders where showing_id = 1392' ),
    array( 'sql' => 'select * from orders where showing_id = 1392' ),
    array( 'sql' => 'show fields from line_items' ),
    array( 'sql' => 'select * from line_items where order_id = 296971' ),
    array( 'sql' => 'SELECT orders.id AS OrderNum, ' .
                    '       Coalesce( customers.name, Concat( card_first_name, " ", card_last_name ), promo_codes.name ) AS Name, ' .
                    '       (SELECT Sum( quantity_ordered ) FROM line_items WHERE order_id = orders.id) AS NumTickets, ' .
                    '       promo_codes.pass_id, Coalesce( promo_codes.kind, \'None\' ) AS PassType ' .
                    '  FROM ((orders LEFT JOIN customers ON orders.customer_id = customers.id) LEFT JOIN ' .
                    '        promo_codes ON orders.promo_code_id = promo_codes.id) LEFT JOIN ' .
                    '        passes ON Coalesce( orders.pass_id, promo_codes.pass_id ) = passes.id ' .
                    ' WHERE orders.completed = 1 AND ' .
                     '       orders.refunded = 0 AND ' .
                    '       orders.voided = 0 AND ' .
                    '       promo_codes.deleted_at IS NULL AND ' .
                    '       orders.showing_id = 1392' ),
    array( 'sql' => '  SELECT Coalesce( promo_codes.kind, \'None\') AS PassType, ' .
                    '         Sum( line_items.quantity_ordered ) AS NumTickets, ' .
                    '         If( (line_items.unit_price = 0) OR (orders.total = 0), \'Y\', \'N\' ) AS UsedPass, ' .
                    '         orders.customer_id AS Customer, promo_codes.pass_id AS PassID ' .
                    '    FROM (orders LEFT JOIN promo_codes ON orders.promo_code_id = promo_codes.id) LEFT JOIN ' .
                    '          line_items ON orders.id = line_items.order_id ' .
                    '   WHERE orders.completed = 1 AND orders.refunded = 0 AND orders.voided = 0 AND ' .
                    '         promo_codes.deleted_at IS NULL AND orders.showing_id = 1392 ' .
                    'GROUP BY line_items.unit_price, orders.total, promo_codes.kind, ' .
                    '         promo_codes.pass_id, orders.customer_id' ),
    array( 'sql' => 'SELECT id, name FROM customers WHERE name LIKE \'%neal%\' OR name LIKE \'%doncaster%\'' )
);

$newln = "\n";
echo '<html><body>' . $newln;

dbConnect();

if ($dbConns[ 'carbonpop' ] == NULL) {
    echo 'Connection error: ' . $dbError;
} else {
    if ($queries == NULL) {
        $tbls = dbGetResult( 'SHOW TABLES;', NULL );

        echo showTable( $tbls, "List of Tables" );

        foreach ($tbls as $tbl) {
            $tname = $tbl[ 'Tables_in_' . $defaultCxn ];
            $tstruct = dbGetResult( 'SHOW FIELDS FROM `' . $tname . '`;', NULL );
            echo showTable( $tstruct, $tname );
        }
    } else {
        foreach ($queries as $query) {
            $sql = $query[ 'sql' ];
            $title = $sql;
            if (isset( $query[ 'title' ] )) {
                $title = $query[ 'title' ];
            }

            echo showTable( dbGetResult( $sql, NULL ), $title );
        }
    }
}

dbDisconnect( 'carbonpop' );
echo '</body></html>' . $newln;

?>
