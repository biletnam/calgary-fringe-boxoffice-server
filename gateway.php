<?php

// REQUIRES APP VERSION >= 0.7.37
$REQUIRED_APP_VERSION = array( 0, 7, 37 );

// This needs to come before the first inclusion of dblib.php, which references the current date/time.
date_default_timezone_set( 'Canada/Mountain' ) ;

require_once( 'dblib.php' );
require_once( 'dbfuncs_boxoffice.php' );
require_once( 'dbfuncs_carbonpop.php' );


$newln = "\n";
dbConnect( 'boxoffice' );

if ($dbConns[ 'boxoffice' ] == NULL) {
    echo 'fail' . $newln;
} else {
    switch ($_POST[ 'action' ]) {
        case 'login':
            $cxnauth = tryLogin();

            echo 'success' . $newln . $cxnauth;
            break;
        case 'checkversion':
            echo 'success' . $newln;
            echo implode( '.', $REQUIRED_APP_VERSION ) . $newln;

            break;
        case 'venuelist':
            $venues = tryVenueList();

            echo 'success' . $newln;
            foreach ($venues as $v) {
                echo $v[ 'id' ] . '|' . $v[ 'windows' ] . '|' . $v[ 'name' ] . $newln;
            }
            break;
        case 'pwindowlist':
            $window = tryPerformanceWindowList();

            echo 'success' . $newln;
            echo $window[ 'ID' ] . '|' . $window[ 'HasConcession' ] . '|' .
                 $window[ 'StartingFloat' ] . '|' . $window[ 'Donations' ] . '|' .
                 $window[ 'ConcessionSales' ] . '|' . $window[ 'StartingButtons' ] . '|' .
                 $window[ 'ButtonSales' ] . '|' . $window[ 'CashAdjustments' ] . '|' .
                 $window[ 'ButtonAdjustments' ] . $newln;

            foreach ($window[ 'pwindows' ] as $pw) {
                echo $pw[ 'ID' ] . '|' . $pw[ 'PerformanceID' ] . '|' .
                     $pw[ 'ShowName' ] . '|' . $pw[ 'ArtistName' ] . '|' .
                     $pw[ 'VenueName' ] . '|' . $pw[ 'StartTime' ] . '|' .
                     $pw[ 'WindowClosesAt' ] . '|' . $pw[ 'TicketPrice' ] . '|' .
                     $pw[ 'StartingTickets' ] . '|' . $pw[ 'TicketSales' ] . '|' .
                     $pw[ 'Superpasses' ] . '|' . $pw[ 'ArtistComps' ] . '|' .
                     $pw[ 'OtherComps' ] . '|' . $pw[ 'ToArtist' ] . '|' .
                     $pw[ 'SpecialQty' ] . '|' . $pw[ 'SpecialAmt' ] . '|' .
                     $pw[ 'Remaining' ] . '|' . $pw[ 'Overflow' ] . '|' .
                     $pw[ 'PayoutMade' ];
                foreach ($pw[ 'Presales' ] as $ps) {
                    echo '|' . $ps[ 'Name' ] . '|' . $ps[ 'OrderNum' ] .
                         '|' . $ps[ 'NumTickets' ] . '|' . $ps[ 'PickedUp' ];
                }
                echo $newln;
            }
            break;
        case 'concessionlist':
            $items = tryConcessionList();

            echo 'success' . $newln;
            foreach ($items as $item) {
                echo $item[ 'ID' ] . '|' . $item[ 'Name' ] . '|' .
                     $item[ 'ItemPrice' ] . '|' . $item[ 'ShowInApplet' ] . $newln;
            }
            break;
        case 'cashsale':
            tryCashSale();
            echo 'success' . $newln;

            break;
        case 'buttonsale':
            tryButtonSale();
            echo 'success' . $newln;

            break;
        case 'issuecomp':
            tryIssueComp();
            echo 'success' . $newln;

            break;
        case 'redeempresale':
            tryRedeemPresale();
            echo 'success' . $newln;

            break;
        case 'concessionsale':
            tryConcessionSale();
            echo 'success' . $newln;

            break;
        case 'donation':
            tryDonation();
            echo 'success' . $newln;

            break;
        case 'showreport':
            $rept = getReportData();

            $filename = str_replace( ' ', '_', $rept[ 'name' ] ) . '.csv';
            header( 'Content-type: text/csv' );
            header( 'Content-Disposition: attachment; filename=' . $filename );
            header( 'Pragma: no-cache' );
            header( 'Expires: 0' );

            echo makeReportCSV( $rept );

            break;
        case 'addpresale':
            tryAddPresale();
            echo 'success' . $newln;

            break;
        case 'cashadjustment':
            tryCashAdjustment();
            echo 'success' . $newln;

            break;
        case 'buttonadjustment':
            tryButtonAdjustment();
            echo 'success' . $newln;

            break;
        case 'remainingcapacity':
            $cap = tryRemainingCapacity();

            echo 'success' . $newln;
            echo $cap[ 'Remaining' ] . '|' . $cap[ 'Overflow' ];
            foreach ($cap[ 'Presales' ] as $ps) {
                echo '|' . $ps[ 'Name' ] . '|' . $ps[ 'OrderNum' ] .
                     '|' . $ps[ 'NumTickets' ] . '|' . $ps[ 'PickedUp' ];
            }
            echo $newln;

            break;
        case 'infotentsales':
            $sales = tryInfoTentSales();

            echo 'success' . $newln;
            echo $sales[ 'Sales' ] . $newln;

            break;
        case 'medianames':
            $names = tryMediaNames();
            
            echo 'success' . $newln;
            foreach ($names as $name) {
                echo $name . $newln;
            }

            break;
        case 'initwindow':
            tryInitWindow();
            echo 'success' . $newln;

            break;
        default:
            fail( 'Unrecognized action command' );
    }

    dbDisconnect( 'boxoffice' );
}

?>
