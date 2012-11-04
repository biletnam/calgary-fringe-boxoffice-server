<?php

require_once( 'uifuncs.php' );

function rptMakeSummaryAttLine( $head, $field, $perfs, $prefix = NULL ) {
    global $newln;

    if ($prefix == NULL) {
        $prefixcell = '    <TD STYLE="border-left: 2px solid black; background-color: #606060;">&nbsp;</TD>' . $newln;
        $total = 0;
    } else {
        $prefixcell = '    <TD STYLE="border-left: 2px solid black; text-align: right;">' . $prefix . '</TD>' . $newln;
        $total = $prefix;
    }

    $html = '<TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-left: 2px solid black;">' . $head . '</TD>' . $newln .
            $prefixcell;
    foreach ($perfs as $perf) {
        if (is_array( $field )) {
            $n = 0;
            foreach ($field as $f) {
                $n += (int) $perf[ $f ];
            }
        } else {
            $n = (int) $perf[ $field ];
        }
        $total += $n;
        $html .= '    <TD STYLE="text-align: right; border: 1px solid black;">' . $n . '</TD>' . $newln;
    }
    $html .= '    <TD STYLE="background-color: #c0c0c0; border-right: 2px solid black; text-align: right;">' . $total . '</TD>' . $newln .
             '</TR>';
    
    return $html;
}

function rptMakeSummaryRevLine( $head, $field, $perfs, $prefix = NULL ) {
    global $newln;

    if ($prefix == NULL) {
        $prefixcell = '    <TD STYLE="border-left: 2px solid black; background-color: #606060;">&nbsp;</TD>' . $newln;
        $total = 0;
    } else {
        $prefixcell = '    <TD STYLE="border-left: 2px solid black; text-align: right;">$' . number_format( $prefix / 100, 2 ) . '</TD>' . $newln;
        $total = $prefix;
    }

    $html = '<TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-left: 2px solid black;">' . $head . '</TD>' . $newln .
            $prefixcell;
    foreach ($perfs as $perf) {
        if (is_array( $field )) {
            $n = 0;
            foreach ($field as $f) {
                $n += (int) $perf[ $f ];
            }
        } else {
            $n = (int) $perf[ $field ];
        }
        $total += $n;
        $html .= '    <TD STYLE="text-align: right; border: 1px solid black;">$' . number_format( $n / 100, 2 ) . '</TD>' . $newln;
    }
    $html .= '    <TD STYLE="background-color: #c0c0c0; border-right: 2px solid black; text-align: right;">$' . number_format( $total / 100, 2 ) . '</TD>' . $newln .
             '</TR>';
    
    return $html;
}

function rptMkReportSummaryPage( $summarydata ) {
    global $newln;
    global $thisYear;
    
    $othernumbers = $summarydata[ 'Daily' ];
    $attnumbers = $summarydata[ 'Attendance' ];
    $revnumbers = $summarydata[ 'Revenues' ];
    
    $allpresoldpasses = $summarydata[ 'PresoldFringers' ] + $summarydata[ 'PresoldBuddys' ] + $summarydata[ 'PresoldBingers' ];

    $title = '<TABLE BORDER="0" WIDTH="850" CELLPADDING="0" CELLSPACING="0"><TR>' . $newln .
             '<TD STYLE="text-align: left;"><IMG SRC="fringe_logo_' . $thisYear . '_for_forms_med.png" /></TD>' . $newln .
             '<TD STYLE="text-align: right; font-size: 14pt; font-weight: bold;">' . $thisYear . ' Calgary Fringe Box Office Stats</TD>' . $newln .
             '</TR></TABLE><BR /><BR />' . $newln;
    
    $tbl = '<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" STYLE="font-weight: bold; font-size: 10pt; border-collapse: collapse;"> ' . $newln .
           '<TR STYLE="font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
           '    <TD STYLE="font-size: 12pt; border-right: 2px solid black;">Attendance Numbers</TD>' . $newln .
           '    <TD STYLE="border-right: 1px solid black; text-align: center;">Pre-Sold</TD>' . $newln;
    foreach (array_keys( $attnumbers ) as $day) {
        $datestr = date( 'F j, Y', strtotime( $day ) );
        $tbl .= '    <TD STYLE="border-right: 1px solid black; text-align: center;">' . $datestr . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: center;">Total Attendance</TD>' . $newln .
            '</TR>' . $newln .
            rptMakeSummaryAttLine( 'Online Superpasses', 'OnlineSuper', $attnumbers ) .
            rptMakeSummaryAttLine( 'Online Individual Tickets', 'OnlineIndiv', $attnumbers ) .
            rptMakeSummaryAttLine( 'Info Booth Superpasses', 'InfoSuper', $attnumbers ) .
            rptMakeSummaryAttLine( 'Info Booth Individual Tickets', 'InfoIndiv', $attnumbers ) .
            rptMakeSummaryAttLine( 'Senior Tuesday Tickets', 'Senior', $attnumbers ) .
            rptMakeSummaryAttLine( 'Artist Special-Price Tickets', 'Special', $attnumbers ) .
            rptMakeSummaryAttLine( 'At the Door Cash Sales', 'VenueIndiv', $attnumbers ) .
            rptMakeSummaryAttLine( 'At the Door Superpasses', 'VenueSuper', $attnumbers ) .
            rptMakeSummaryAttLine( 'Total Free Tickets (all-access, host, media, etc.)', 'Free', $attnumbers ) .
            rptMakeSummaryAttLine( 'Artist&apos;s Comps', 'Artist', $attnumbers ) .
            '<TR STYLE="font-size: 12pt; font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD>Total Attendance</TD>' . $newln .
            '    <TD STYLE="border-left: 2px solid black; background-color: #606060;">&nbsp;</TD>' . $newln;
    $totalatt = 0;
    foreach ($attnumbers as $day) {
        $sum = 0;
        foreach ($day as $kind) {
            $sum += $kind;
        }
        $totalatt += $sum;
        $tbl .= '    <TD STYLE="border: 1px solid black; text-align: right;">' . $sum . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: right;">' . $totalatt . '</TD>' . $newln .
            '</TR>' . $newln .
            '<TR><TD>&nbsp;</TD></TR>' . $newln .
            '<TR STYLE="font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD STYLE="font-size: 12pt; border-right: 1px solid black;">Daily Revenue by Category</TD>' . $newln .
            '    <TD STYLE="border-right: 1px solid black; text-align: center;">Pre-Sold</TD>' . $newln;
    foreach (array_keys( $revnumbers ) as $day) {
        $datestr = date( 'F j, Y', strtotime( $day ) );
        $tbl .= '    <TD STYLE="border-right: 1px solid black; text-align: center;">' . $datestr . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: center;">Total Revenue</TD>' . $newln .
            '</TR>' . $newln .
            rptMakeSummaryRevLine( 'Online Individual Tickets', 'OnlineIndiv', $revnumbers ) .
            rptMakeSummaryRevLine( 'Info Booth Individual Tickets', 'InfoIndiv', $revnumbers ) .
            rptMakeSummaryRevLine( 'At the Door Individual Tickets', 'VenueIndiv', $revnumbers ) .
            rptMakeSummaryRevLine( 'Senior Tuesday Tickets', 'Senior', $revnumbers ) .
            rptMakeSummaryRevLine( 'Artist Special-Price Tickets', 'Special', $revnumbers ) .
            rptMakeSummaryRevLine( 'Ticketing Surcharges', 'Surcharge', $revnumbers ) .
            rptMakeSummaryRevLine( 'Fringe Buttons', 'Buttons', $othernumbers ) .
            rptMakeSummaryRevLine( 'Frequent Fringer Passes (5)', 'Fringers', $othernumbers, $summarydata[ 'PresoldFringers' ] ) .
            rptMakeSummaryRevLine( 'Buddy Passes (10)', 'Buddys', $othernumbers, $summarydata[ 'PresoldBuddys' ] ) .
            rptMakeSummaryRevLine( 'Fringe Binger Passes (20)', 'Bingers', $othernumbers, $summarydata[ 'PresoldBingers' ] ) .
            '<TR STYLE="font-size: 12pt; font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD>Total Revenue</TD>' . $newln .
            '    <TD STYLE="border-left: 2px solid black;">$' . number_format( $allpresoldpasses / 100, 2 ) . '</TD>' . $newln;
    $totalrev = $allpresoldpasses;
    foreach (array_keys( $revnumbers ) as $day) {
        $sum = $revnumbers[ $day ][ 'OnlineIndiv' ] +
               $revnumbers[ $day ][ 'InfoIndiv' ] +
               $revnumbers[ $day ][ 'VenueIndiv' ] +
               $revnumbers[ $day ][ 'Senior' ] +
               $revnumbers[ $day ][ 'Special' ] +
               $revnumbers[ $day ][ 'Surcharge' ];
        foreach ($othernumbers[ $day ] as $kind) {
            $sum += $kind;
        }
        $totalrev += $sum;
        $tbl .= '    <TD STYLE="border: 1px solid black; text-align: right;">$' . number_format( $sum / 100, 2 ) . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: right;">$' . number_format( $totalrev / 100, 2 ) . '</TD>' . $newln .
            '</TR>' . $newln .
            '</TABLE><BR /><BR />' . $newln;

    $totalbuttons = 0;
    $totalfringers = $summarydata[ 'PresoldFringers' ];
    $totalbuddys = $summarydata[ 'PresoldBuddys' ];
    $totalbingers = $summarydata[ 'PresoldBingers' ];
    foreach ($othernumbers as $day) {
        $totalbuttons += $day[ 'Buttons' ];
        $totalfringers += $day[ 'Fringers' ];
        $totalbuddys += $day[ 'Buddys' ];
        $totalbingers += $day[ 'Bingers' ];
    }
    $superpasssurchg = (int) (($totalfringers / 11) + ($totalbuddys / 21));

    $totalsurchg = 0;
    $totalindiv = 0;
    $totaloverride = 0;
    foreach ($revnumbers as $day) {
        $totalsurchg +=  $day[ 'Surcharge' ];
        $totalindiv += ($day[ 'OnlineIndiv' ] + $day[ 'InfoIndiv' ] + $day[ 'VenueIndiv' ]);
        $totaloverride += ($day[ 'Senior' ] + $day[ 'Special' ]);
    }

    $redeemedpasses = 0;
    foreach ($attnumbers as $day) {
        $redeemedpasses += ($day[ 'OnlineSuper' ] + $day[ 'InfoSuper' ] + $day[ 'VenueSuper' ]);
    }
    
    $totalartistrevenue = $totalindiv + $totaloverride + ($redeemedpasses * 1000);

    $tbl2 = '<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="font-size: 10pt; border: 2px solid black; border-collapse: collapse;"> ' . $newln .
            '<TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Fringe Buttons Sold</TD>' . $newln .
            '    <TD STYLE="text-align: center;" WIDTH="100">' . ((int) ($totalbuttons / 500)) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Frequent Fringer Passes (5) (# of coupons sold)</TD>' . $newln .
            '    <TD STYLE="text-align: center;">' . ((int) ($totalfringers / 1100)) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Buddy Passes (10) (# of coupons sold)</TD>' . $newln .
            '    <TD STYLE="text-align: center;">' . ((int) ($totalbuddys / 1050)) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Fringe Binger Passes (20) (# of coupons sold)</TD>' . $newln .
            '    <TD STYLE="text-align: center;">' . ((int) ($totalbingers / 1000)) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Ticket Surcharges</TD>' . $newln .
            '    <TD STYLE="text-align: center;">$' . number_format( $totalsurchg / 100, 2 ) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Superpass Book Surcharges</TD>' . $newln .
            '    <TD STYLE="text-align: center;">$' . number_format( $superpasssurchg / 100, 2 ) . '</TD>' . $newln .
            '</TR><TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-right: 2px solid black;">Total Revenue to Artists (incl. GST)</TD>' . $newln .
            '    <TD STYLE="text-align: center;">$' . number_format( $totalartistrevenue / 100, 2 ) . '</TD>' . $newln .
            '</TR>' . $newln .
            '</TABLE><BR /><BR />' . $newln;

    $html = $title .
            $tbl .
            $tbl2;

    return $html;
}

function rptMakeAttLine( $head, $field, $perfs ) {
    global $newln;

    $total = 0;
    $html = '<TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-left: 2px solid black;">' . $head . '</TD>' . $newln;
    foreach ($perfs as $perf) {
        if (is_array( $field )) {
            $n = 0;
            foreach ($field as $f) {
                $n += (int) $perf[ $f ];
            }
        } else {
            $n = (int) $perf[ $field ];
        }
        $total += $n;
        $html .= '    <TD STYLE="text-align: right; border: 1px solid black;">' . $n . '</TD>' . $newln;
    }
    $html .= '    <TD STYLE="background-color: #c0c0c0; border-right: 2px solid black; text-align: right;">' . $total . '</TD>' . $newln .
             '</TR>';
    
    return $html;
}

function rptMakeRevLine( $head, $field, $perfs ) {
    global $newln;

    $total = 0;
    $html = '<TR STYLE="border: 1px solid black;">' . $newln .
            '    <TD STYLE="font-weight: bold; border-left: 2px solid black;">' . $head . '</TD>' . $newln;
    foreach ($perfs as $perf) {
        if (is_array( $field )) {
            $n = 0;
            foreach ($field as $f) {
                $n += (int) $perf[ $f ];
            }
        } else {
            $n = (int) $perf[ $field ];
        }
        $total += $n;
        $html .= '    <TD STYLE="text-align: right; border: 1px solid black;">$' . number_format( $n / 100, 2 ) . '</TD>' . $newln;
    }
    $html .= '    <TD STYLE="background-color: #c0c0c0; border-right: 2px solid black; text-align: right;">$' . number_format( $total / 100, 2 ) . '</TD>' . $newln .
             '</TR>';
    
    return $html;
}

function rptMkReportShowPage( $showdata ) {
    global $newln;

    $title = '<SPAN STYLE="font-family: \'Arial black\'; font-weight: bold; font-size: 16pt;">' .
             'ADVANCE/AT THE DOOR Ticket Sales - FINAL RECONCILIATION REPORT' .
             '</SPAN><BR /><BR />' . $newln;

    $surcharge = $showdata[ 'TicketPrice' ] - $showdata[ 'ToArtist' ];
    $performances = $showdata[ 'Performances' ];
    $revenues = $showdata[ 'Revenues' ];

    $showdetails = '<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="font-weight: bold; font-size: 10pt;"> ' . $newln .
                   '<TR>' . $newln .
                   '    <TD STYLE="text-align: right;">Performer/Group:&nbsp;</TD>' . $newln .
                   '    <TD>' . $showdata[ 'ArtistName' ] . '</TD>' . $newln .
                   '</TR><TR>' . $newln .
                   '    <TD STYLE="text-align: right;">Show Title:&nbsp;</TD>' . $newln .
                   '    <TD>' . $showdata[ 'Title' ] . '</TD>' . $newln .
                   '</TR><TR>' . $newln .
                   '    <TD STYLE="text-align: right;">Venue:&nbsp;</TD>' . $newln .
                   '    <TD STYLE="font-style: italic;">' . $showdata[ 'VenueName' ] . '</TD>' . $newln .
                   '</TR><TR>' . $newln .
                   '    <TD STYLE="text-align: right;">Cost per Ticket:&nbsp;</TD>' . $newln .
                   '    <TD STYLE="font-style: italic;">$' . number_format( $showdata[ 'ToArtist' ] / 100, 2 ) . '</TD>' . $newln .
                   '</TR><TR>' . $newln .
                   '    <TD STYLE="text-align: right;">Ticketing Surcharge:&nbsp;</TD>' . $newln .
                   '    <TD STYLE="font-style: italic;">$' . number_format( $surcharge / 100, 2 ) . '</TD>' . $newln .
                   '</TR>' . $newln .
                   '</TABLE><BR /><BR />' . $newln;

    $tbl = '<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" STYLE="font-weight: bold; font-size: 10pt; border-collapse: collapse;"> ' . $newln .
           '<TR STYLE="font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
           '    <TD STYLE="font-size: 12pt; border-right: 1px solid black;">Attendance Numbers</TD>' . $newln;
    foreach ($performances as $perf) {
        $datestr = date( 'F j, Y', strtotime( $perf[ 'PerformanceDate' ] ) );
        $tbl .= '    <TD STYLE="border-right: 1px solid black;">' . $datestr . '</TD>' . $newln;
    }
    $tbl .= '    <TD>Total Attendance</TD>' . $newln .
            '</TR>' . $newln .
            rptMakeAttLine( 'Online Superpasses', 'OnlineSuper', $performances ) .
            rptMakeAttLine( 'Online Individual Tickets', 'OnlineIndiv', $performances ) .
            rptMakeAttLine( 'Info Booth Superpasses', 'InfoSuper', $performances ) .
            rptMakeAttLine( 'Info Booth Individual Tickets', 'InfoNormal', $performances ) .
            rptMakeAttLine( 'Senior Tuesday Tickets', array( 'Info1000', 'Venue1000' ), $performances ) .
            rptMakeAttLine( 'Artist Special-Price Tickets', 'Venue800', $performances ) .
            rptMakeAttLine( 'At the Door Cash Sales', 'VenueNormal', $performances ) .
            rptMakeAttLine( 'At the Door Superpasses', 'VenueSuper', $performances ) .
            rptMakeAttLine( 'Total Free Tickets (all-access, host, media, etc.)&nbsp;', array( 'OnlinePromo', 'InfoPromo', 'VenuePromo' ), $performances ) .
            rptMakeAttLine( 'Artist&apos;s Comps', array( 'InfoArtist', 'VenueArtist' ), $performances ) .
            '<TR STYLE="font-size: 12pt; font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD>Total Attendance</TD>' . $newln;
    $totalatt = 0;
    foreach ($performances as $perf) {
        $sum = 0;
        foreach (array_keys( $perf ) as $k) {
            if (($k != 'PerformanceDate') && ($k != 'Payout') && ($k != 'ID')) {
                $sum += $perf[ $k ];
            }
        }
        $totalatt += $sum;
        $tbl .= '    <TD STYLE="border: 1px solid black; text-align: right;">' . $sum . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: right;">' . $totalatt . '</TD>' . $newln .
            '</TR>' . $newln .
            '<TR><TD>&nbsp;</TD></TR>' . $newln .
            '<TR STYLE="font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD STYLE="font-size: 12pt; border-right: 1px solid black;">Revenue by Category</TD>' . $newln;
    foreach ($performances as $perf) {
        $datestr = date( 'F j, Y', strtotime( $perf[ 'PerformanceDate' ] ) );
        $tbl .= '    <TD STYLE="border-right: 1px solid black;">' . $datestr . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: center;">Total Revenue</TD>' . $newln .
            '</TR>' . $newln .
            rptMakeRevLine( 'Online Superpasses', 'OnlineSuper', $revenues ) .
            rptMakeRevLine( 'Online Individual Tickets', 'OnlineIndiv', $revenues ) .
            rptMakeRevLine( 'Info Booth Superpasses', 'InfoSuper', $revenues ) .
            rptMakeRevLine( 'Info Booth Individual Tickets', 'InfoIndiv', $revenues ) .
            rptMakeRevLine( 'Senior Tuesday Tickets', 'Senior', $revenues ) .
            rptMakeRevLine( 'Artist Special-Price Tickets', 'Special', $revenues ) .
            rptMakeRevLine( 'At the Door Cash Sales', 'VenueIndiv', $revenues ) .
            rptMakeRevLine( 'At the Door Superpasses', 'VenueSuper', $revenues ) .
            rptMakeRevLine( 'Ticketing Surcharges', 'Surcharge', $revenues ) .
            '<TR STYLE="font-size: 12pt; font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD>Total Revenue</TD>' . $newln;
    $totalrev = 0;
    foreach ($revenues as $rev) {
        $totalrev += $rev[ 'Total' ];
        $tbl .= '    <TD STYLE="border: 1px solid black; text-align: right;">$' . number_format( $rev[ 'Total' ] / 100, 2 ) . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: right;">$' . number_format( $totalrev / 100, 2 ) . '</TD>' . $newln .
            '</TR>' . $newln .
            '<TR><TD>&nbsp;</TD></TR>' . $newln .
            '<TR STYLE="font-size: 12pt; font-weight: bold; border: 2px solid black; background-color: #c0c0c0;">' . $newln .
            '    <TD STYLE="border-right: 1px solid black;">Daily at the Door Cash Payout to Artist</TD>' . $newln;
    $totalpayout = 0;
    foreach ($performances as $perf) {
        $totalpayout += $perf[ 'Payout' ];
        $tbl .= '    <TD STYLE="border-right: 1px solid black; text-align: right;">$' . number_format( $perf[ 'Payout' ] / 100, 2 ) . '</TD>' . $newln;
    }
    $tbl .= '    <TD STYLE="text-align: right;">$' . number_format( $totalpayout / 100, 2 ) . $newln .
            '</TR>' . $newln .
            '</TABLE><BR /><BR />' . $newln;

    $totalsurchg = 0;
    foreach ($revenues as $rev) {
        $totalsurchg += $rev[ 'Surcharge' ];
    }
    
    $totaltoartist = $totalrev - $totalsurchg;
    if (trim( $showdata[ 'GSTNumber' ] ) == '') {
        $gsttitle = 'Less 5% GST';
        $gstlevy = (int) ($totaltoartist - ($totaltoartist / 1.05));
        $gstprint = '$' . number_format( $gstlevy / 100, 2 );
    } else {
        $gsttitle = 'GST #' . trim( $showdata[ 'GSTNumber' ] );
        $gstlevy = 0;
        $gstprint = '';
    }
    if ($showdata[ 'WithholdingTax' ] == 'Y') {
        $whldtitle = 'Less 15% Withholding Tax';
        $whldlevy = (int) ($totaltoartist - ($totaltoartist / 1.15));
        $whldprint = '$' . number_format( $whldlevy / 100, 2 );
    } else {
        $whldtitle = '';
        $whldlevy = 0;
        $whldprint = '';
    }
    
    $finalpayout = $totaltoartist - ($gstlevy + $whldlevy + $totalpayout);

    $payoutdetails =
        '<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="font-weight: bold; font-size: 10pt;"> ' . $newln .
        '<TR STYLE="text-align: right;">' . $newln .
        '    <TD>Gross Revenue&nbsp;</TD>' . $newln .
        '    <TD>' . str_repeat( '&nbsp;', 10 ) . '$' . number_format( $totalrev / 100, 2 ) . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right;">' . $newln .
        '    <TD>Less Ticketing Surcharges&nbsp;</TD>' . $newln .
        '    <TD STYLE="font-weight: normal; text-align: right; border-bottom: 1px double black;">$' . number_format( $totalsurchg / 100, 2 ) . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right; font-style: italic;">' . $newln .
        '    <TD>Revenue to Artist&nbsp;</TD>' . $newln .
        '    <TD STYLE="border-bottom: 2px solid black;">$' . number_format( $totaltoartist / 100, 2 ) . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right;">' . $newln .
        '    <TD>' . $gsttitle . '&nbsp;</TD>' . $newln .
        '    <TD>' . $gstprint . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right;">' . $newln .
        '    <TD>' . $whldtitle . '&nbsp;</TD>' . $newln .
        '    <TD>' . $whldprint . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right;">' . $newln .
        '    <TD>&nbsp;Less Total Daily Cash Payouts&nbsp;</TD>' . $newln .
        '    <TD STYLE="border-bottom: 1px double black;">$' . number_format( $totalpayout / 100, 2 ) . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right; font-size: 12pt; background-color: #c0c0c0;">' . $newln .
        '    <TD>Final Payout to Artist&nbsp;</TD>' . $newln .
        '    <TD STYLE="border-bottom: 2px solid black;">$' . number_format( $finalpayout / 100, 2 ) . '</TD>' . $newln .
        '</TR><TR STYLE="text-align: right; font-size: 12pt; background-color: #c0c0c0;">' . $newln .
        '    <TD>Total Attendance&nbsp;</TD>' . $newln .
        '    <TD STYLE="border-bottom: 2px solid black;">' . $totalatt . '</TD>' . $newln .
        '</TR>' . $newln .
        '</TABLE><BR /><BR />' . $newln;
    
    $sigs = 
        '<TABLE BORDER="0" CELLPADDING="2" CELLSPACING="0" STYLE="font-size: 10pt;"> ' . $newln .
        '<TR STYLE="text-align: right;">' . $newln .
        '    <TD COLSPAN="2" STYLE="font-size: 9pt; font-style: italic;">By signing below, all parties agree to the figures and calculations as presented above:</TD>' . $newln .
        '</TR><TR><TD COLSPAN="2">&nbsp;</TD></TR><TR>' . $newln .
        '    <TD STYLE="text-align: right; font-weight: bold;">Box Office Signature:</TD>' . $newln .
        '    <TD STYLE="border-bottom: 1px solid black;">' . str_repeat( '&nbsp;', 45 ) . '</TD>' . $newln .
        '</TR><TR><TD COLSPAN="2">&nbsp;</TD></TR><TR>' . $newln .
        '    <TD COLSPAN="2" STYLE="font-size: 9pt; font-style: italic;">We also acknowledge receipt of the Net Ticket Sales for Run of Show as outlined above:</TD>' . $newln .
        '</TR><TR><TD COLSPAN="2">&nbsp;</TD></TR><TR>' . $newln .
        '    <TD STYLE="text-align: right; font-weight: bold;">Artist(s) Signature:</TD>' . $newln .
        '    <TD STYLE="border-bottom: 1px solid black;">' . str_repeat( '&nbsp;', 45 ) . '</TD>' . $newln .
        '</TR>' . $newln .
        '</TABLE><BR />' . $newln;

    $html = $title .
            $showdetails .
            $tbl .
            $payoutdetails .
            $sigs;

    return $html;
}

function rptMkReportPage( $reportdata, $username = NULL, $errormsg = array() ) {
    global $newln;
    global $thisYear;
    
    rptCalcRevenues( $reportdata );

    $showid = uiGetHTTPParam( 'showid' );
    if ($showid == NULL) {
        $rpt = rptMkReportSummaryPage( $reportdata[ 'Summary' ] );
        $summarysel = '<OPTION SELECTED VALUE="">' . $thisYear . ' Box Office Stats<OPTION>';
    } else {
        $rpt = rptMkReportShowPage( $reportdata[ 'Shows' ][ $showid ] );
        $summarysel = '<OPTION VALUE="">' . $thisYear . ' Box Office Stats<OPTION>';
    }

    $select = 'Report page: ' .
              '<SELECT ONCHANGE="window.location.href = (\'/admin.php?action=finalboxoffice&showid=\' + this.options[ this.selectedIndex ].value);">' .
              $summarysel;
    foreach (array_keys( $reportdata[ 'Shows' ] ) as $id) {
        $selected = ($id == $showid ? 'SELECTED ' : '');
        $title = $reportdata[ 'Shows' ][ $id ][ 'Title' ];
        $select .= '<OPTION ' . $selected . 'VALUE="' . $id . '">' . $reportdata[ 'Shows' ][ $id ][ 'Title' ] . '</OPTION>';
    }
    $select .= '</SELECT><BR /><BR />' . $newln;

    $body = '<DIV CLASS="noprint">' . $select . '</DIV>' .
            '<DIV STYLE="border: 1px solid black; padding: 10px; font-family: Arial; font-size: 10pt;">' . $rpt . '</DIV>' . $newln;

    return uiCompilePage( 'Final Box Office Reports', $body, $errormsg, 'finalboxoffice', $username );
}

function rptCalcRevenues( &$report ) {
    $daylist = array();
    foreach ($report[ 'Dates' ] as $day) {
        $daylist[ $day[ 'Day' ] ] = array();
    }

    $report[ 'Summary' ][ 'Attendance' ] = $daylist;
    $report[ 'Summary' ][ 'Revenues' ] = $daylist;

    foreach (array_keys( $report[ 'Shows' ] ) as $showid) {
        $surcharge = $report[ 'Shows' ][ $showid ][ 'TicketPrice' ] - $report[ 'Shows' ][ $showid ][ 'ToArtist' ];
        $toartist = $report[ 'Shows' ][ $showid ][ 'ToArtist' ];

        $report[ 'Shows' ][ $showid ][ 'Revenues' ] = array();

        foreach ($report[ 'Shows' ][ $showid ][ 'Performances' ] as $perf) {
            $day = $perf[ 'PerformanceDate' ];
            $pid = $perf[ 'ID' ];

            if (array_key_exists( $day, $daylist )) {
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'OnlineSuper' ] += $perf[ 'OnlineSuper' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'OnlineIndiv' ] += $perf[ 'OnlineIndiv' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'InfoSuper' ] += $perf[ 'InfoSuper' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'InfoIndiv' ] += $perf[ 'InfoNormal' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'Senior' ] += ($perf[ 'Info1000' ] + $perf[ 'Venue1000' ]);
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'Special' ] += $perf[ 'Venue800' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'VenueIndiv' ] += $perf[ 'VenueNormal' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'VenueSuper' ] += $perf[ 'VenueSuper' ];
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'Free' ] += ($perf[ 'OnlinePromo' ] + $perf[ 'InfoPromo' ] + $perf[ 'VenuePromo' ]);
                $report[ 'Summary' ][ 'Attendance' ][ $day ][ 'Artist' ] += ($perf[ 'InfoArtist' ] + $perf[ 'VenueArtist' ]);
    
                $onlinesuper = $perf[ 'OnlineSuper' ] * 1000;
                $onlineindiv = $perf[ 'OnlineIndiv' ] * $toartist;
                $infosuper = $perf[ 'InfoSuper' ] * 1000;
                $infoindiv = $perf[ 'InfoNormal' ] * $toartist;
                $senior = ($perf[ 'Info1000' ] + $perf[ 'Venue1000' ]) * 1000;
                $special = $perf[ 'Venue800' ] * 800;
                $venueindiv = $perf[ 'VenueNormal' ] * $toartist;
                $venuesuper = $perf[ 'VenueSuper' ] * 1000;
                $surchg = ($perf[ 'OnlineIndiv' ] + $perf[ 'InfoNormal' ] + $perf[ 'VenueNormal' ]) * $surcharge;
                $total = $onlinesuper + $onlineindiv + $infosuper + $infoindiv + $senior + $special + $venueindiv + $venuesuper + $surchg;
    
                $report[ 'Shows' ][ $showid ][ 'Revenues' ][ $pid ] = array(
                    'Day' => $day,
                    'OnlineSuper' => $onlinesuper,
                    'OnlineIndiv' => $onlineindiv,
                    'InfoSuper' => $infosuper,
                    'InfoIndiv' => $infoindiv,
                    'Senior' => $senior,
                    'Special' => $special,
                    'VenueIndiv' => $venueindiv,
                    'VenueSuper' => $venuesuper,
                    'Surcharge' => $surchg,
                    'Total' => $total
                );
    
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'OnlineSuper' ] += $onlinesuper;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'OnlineIndiv' ] += $onlineindiv;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'InfoSuper' ] += $infosuper;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'InfoIndiv' ] += $infoindiv;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'Senior' ] += $senior;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'Special' ] += $special;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'VenueIndiv' ] += $venueindiv;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'VenueSuper' ] += $venuesuper;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'Surcharge' ] += $surchg;
                $report[ 'Summary' ][ 'Revenues' ][ $day ][ 'Total' ] += $total;
            }
        }
    }
}

?>
