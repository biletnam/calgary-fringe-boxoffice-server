<?php

// This needs to come first, to ensure cookies / headers get set as appropriate before any output
require_once( 'ssnfuncs.php' );

// This needs to come before the first inclusion of dblib.php, which references the current date/time.
date_default_timezone_set( 'Canada/Mountain' ) ;

require_once( 'dblib.php' );
require_once( 'dbfuncs_admin.php' );
require_once( 'dbfuncs_boxoffice.php' );
require_once( 'dbfuncs_carbonpop.php' );
require_once( 'uifuncs.php' );
require_once( 'rptfuncs.php' );

$uiPageInit = true;
$newln = "\n";

if (! dbConnect( 'boxoffice' )) {
    echo 'FAIL: Cannot Connect to Database';
}

$action = uiGetHTTPParam( 'action' );
if ($action == NULL) {
    dbLogout();
    $action = 'login';
}

$badcred = false;
$username = NULL;
if ($action == 'trylogin') {
    $tryuser = $_POST[ 'userlogin' ];
    $trypass = $_POST[ 'passlogin' ];

    if (! dbLogin( $tryuser, $trypass )) {
        $badcred = true;
        $action = 'login';
    } else {
        $username = sessGetLogin();
        $action = 'menu';
    }
} elseif ($action != 'login' && $action != 'logout') {
    if (! dbIsLoggedIn()) {
        $badcred = true;
        $action = 'login';
    } else {
        $username = sessGetLogin();
    }
}

if (substr( $action, 0, 4 ) == 'form') {
    list( $action, $errormsg ) = uiTakeAction( $action, $username );
} else {
    $errormsg = array();
}

switch ($action) {
    case 'login':
        $html = uiMkLoginPage( false, $badcred );
        break;
    case 'logout':
        dbLogout();
        $html = uiMkLoginPage( true );
        break;
    case 'menu':
        $html = uiMkMenuPage( $username, $errormsg );
        break;
    case 'editbuttons':
        $html = uiMkEditButtonsPage( $username, $errormsg );
        break;
    case 'editconcessions':
        $html = uiMkEditConcessionsPage( $username, $errormsg );
        break;
    case 'editdonations':
        $html = uiMkEditDonationsPage( $username, $errormsg );
        break;
    case 'editpresales':
        $html = uiMkEditPresalesPage( $username, $errormsg );
        break;
    case 'editinfosales':
    case 'editvenuesales':
        $html = uiMkEditTicketSalesPage( $username, $errormsg );
        break;
    case 'editinfocomps':
    case 'editvenuecomps':
        $html = uiMkEditTicketCompsPage( $username, $errormsg );
        break;
    case 'addbuttons':
        $html = uiMkAddButtonsPage( $username, $errormsg );
        break;
    case 'addconcessions':
        $html = uiMkAddConcessionsPage( $username, $errormsg );
        break;
    case 'adddonations':
        $html = uiMkAddDonationsPage( $username, $errormsg );
        break;
    case 'addpresales':
        $html = uiMkAddPresalesPage( $username, $errormsg );
        break;
    case 'addinfosales':
    case 'addvenuesales':
        $html = uiMkAddTicketSalesPage( $username, $errormsg );
        break;
    case 'addinfocomps':
    case 'addvenuecomps':
        $html = uiMkAddTicketCompsPage( $username, $errormsg );
        break;
    case 'venues':
        $html = uiMkVenueListPage( $username, $errormsg );
        break;
    case 'venuedetails':
        $html = uiMkVenueDetailPage( $username, $errormsg );
        break;
    case 'venuesales':
        $html = uiMkVenueSalesPage( $username, $errormsg );
        break;
    case 'shows':
        $html = uiMkShowListPage( $username, $errormsg );
        break;
    case 'showdetails':
        $html = uiMkShowDetailPage( $username, $errormsg );
        break;
    case 'performances':
        $html = uiMkPerformanceListPage( $username, $errormsg );
        break;
    case 'performancedetails':
        $html = uiMkPerformanceDetailPage( $username, $errormsg );
        break;
    case 'endofday':
        $html = uiMkEndOfDayListPage( $username, $errormsg );
        break;
    case 'dailyendofday':
        $html = uiMkEndOfDaySummaryPage( $username, $errormsg );
        break;
    case 'windowcash':
        $html = uiMkWindowCashSummaryPage( $username, $errormsg );
        break;
    case 'venuesuper':
        $html = uiMkVenueSuperListPage( $username, $errormsg );
        break;
    case 'dailyvenuesuper':
        $html = uiMkVenueSuperReconciliationsPage( $username, $errormsg );
        break;
    case 'tickets':
        $html = uiMkTicketBreakdownListPage( $username, $errormsg );
        break;
    case 'dailytickets':
        $html = uiMkTicketBreakdownPage( $username, $errormsg );
        break;
    case 'finalboxoffice':
        set_time_limit( 90 );
        $reportdata = dbGetFinalReportData();
        $html = rptMkReportPage( $reportdata, $username, $errormsg );
        break;
    case 'media':
        $html = uiMkMediaAttSummaryPage( $username, $errormsg );
        break;
    case 'cpoptix':
        $html = uiMkCarbonPopTicketCheckPage( $username, $errormsg );
        break;
    case 'float':
        $html = uiMkFloatCalculatorPage( $username, $errormsg );
        break;
    case 'createfestival':
        $html = uiMkCreateFestivalPage( $username, $errormsg );
        break;
    case 'festivalcreated':
        $html = uiMkFestivalCreatedPage( $username, $errormsg );
        break;
    default:
        $html = uiMkLoginPage( false );
}

dbDisconnect( 'boxoffice' );

echo $html;

?>
