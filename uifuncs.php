<?php

function nz( $var, $defaultval ) {
    return ($var == NULL ? $defaultval : $var);
}

function sqldump( $sql, $params ) {
    global $newln;
    
    $plst = '';
    
    echo '[' . $sql . ']<BR />' . $newln;
    echo 'with {';
    if (count( $params ) > 0) {
        foreach ($params as $param) {
            $plst .= ', ' . $param;
        }
        echo substr( $plst, 2 );
    }
    echo '}<BR /><BR />' . $newln;
}

function uiMkTable( $tbl, $name = NULL, $keysforedit = NULL, $formname = NULL, $addparam = NULL ) {
    global $newln;
    global $dbError;
    
    $addlink = ($keysforedit == NULL ? '' : '[<A HREF="/admin.php?action=add' . $formname . '&' . $addparam . '">Add...</A>]');

    $tblhtml = NULL;
    if ($tbl == NULL) {
        $tblhtml = '<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1">' . $newln .
                   '<TR><TD>No entries. ' . $addlink . '</TD></TR>' . $newln .
                   '</TABLE>' . $newln;
    }

    $output = '';
    if ($keysforedit != NULL) {
        $output .= '<FORM CLASS="admininfo" NAME="' . $formname . '" ACTION="/admin.php" METHOD="post">' . $newln .
                   '<INPUT TYPE="hidden" NAME="action" ID="' . $formname . '_action" VALUE="">' . $newln .
                   '<INPUT TYPE="hidden" NAME="fields" ID="' . $formname . '_fields" VALUE="">' . $newln;
    }
    $output .= ($name == NULL ? '' : '<STRONG>' . $name . '</STRONG><BR />' . $newln);
    if ($tblhtml == NULL) {
        $cols = array_keys( $tbl[ 0 ] );

        $output .= '<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1">' . $newln .
                   '<TR>' . $newln;
        foreach ($cols as $col) {
            $output .= '<TD><EM>' . $col . '</EM></TD>';
        }
        if ($keysforedit != NULL) {
            $output .= '<TD><EM>(Actions)</EM></TD>';
        }
        $output .= '</TR>';
        foreach ($tbl as $row) {
            $output .= '<TR>' . $newln;
            foreach ($cols as $col) {
                $output .= '<TD>' . $row[ $col ] . '</TD>';
            }
            if ($keysforedit != NULL) {
                $idstring = '';
                foreach ($keysforedit as $k) {
                    $idstring .= '&' . $k . '=' .$row[ $k ];
                }
                $idstring = substr( $idstring, 1 );

                $output .= '<TD>[<A HREF="/admin.php?action=edit' . $formname . '&' . $idstring . '">Edit</A>] ' .
                           '<INPUT TYPE="button" VALUE="X" ONCLICK="submitDeleteForm( \'' . $formname . '\', \'formdelete' . $formname . '\', \'' . $idstring . '\' );" /></TD>';
            }
            $output .= '</TR>' . $newln;
        }
        if ($keysforedit != NULL) {
            $output .= '<TR>' . $newln;
            foreach ($cols as $col) {
                $output .= '<TD></TD>';
            }
            $output .= '<TD>' . $addlink . '</TD></TR>' . $newln;
        }
        $output .= '</TABLE>' . $newln;
    } else {
        $output .= $tblhtml;
    }
    if ($keysforedit != NULL) {
        $output .= '</FORM>' . $newln;
    }

    return $output;
}

function uiMkEditTable( $tbl, $formname, $idstring, $name = NULL ) {
    global $newln;
    global $dbError;
    
    if ($tbl == NULL) {
        return $dbError;
    }

    $output = '<FORM CLASS="admininfo" NAME="' . $formname . '" ACTION="/admin.php" METHOD="post">' . $newln .
              '<INPUT TYPE="hidden" NAME="action" ID="' . $formname . '_action" VALUE="">' . $newln .
              '<INPUT TYPE="hidden" NAME="fields" ID="' . $formname . '_fields" VALUE="">' . $newln .
              ($name == NULL ? '' : '<STRONG>' . $name . '</STRONG><BR /><BR />' . $newln) .
              '<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1">' . $newln;
    foreach ($tbl as $name => $data) {
        $value = $data[ 'Value' ];
        $ftype = $data[ 'Type' ];
        $fparams = $data[ 'Params' ];
        $id = $data[ 'ID' ];
        
        $output .= '<TR><TD><EM>' . $name . '</EM></TD><TD>' .
                   uiMkEditField( $name, $ftype, $value, $fparams, $id ) .
                   '</TD></TR>' . $newln;
    }
    $output .= '<TR><TD></TD><TD><INPUT TYPE="button" VALUE="Save Changes" ONCLICK="submitEditForm( \'' . $formname . '\', \'formedit' . $formname . '\', \'' . $idstring . '\' );" /></TD>' . $newln .
               '</TABLE>' . $newln .
               '</FORM>' . $newln;

    return $output;
}

function uiMkEditField( $name, $ftype = NULL, $value = NULL, $fparams = NULL, $id = NULL ) {
    if ($ftype == NULL) {
        $ftype = 'string';
    }
    if ($id == NULL) {
        $id = $name;
    }
    
    $output = '';
    
    switch ($ftype) {
        case 'readonly':
            $output = ($value == NULL ? '&nbsp;' : $value);
            
            break;
        case 'bool':
            $checked = ($value == 'Y' ? 'CHECKED ' : '');
            $output = '<INPUT TYPE="checkbox" NAME="' . $name .'" ID="' . $id . '" VALUE="Y" ' . $checked . '/>';
            
            break;
        case 'currency':
            if ($value == NULL) {
                $value = 0;
            }

            $value = number_format( $value / 100, 2 );
            $output = '$<INPUT TYPE="text" SIZE="10" MAXLENGTH="10" NAME="' . $name . '" ID="' . $id . '" VALUE="' . $value . '" />';
            
            break;
        case 'enum':
            if ($fparams != NULL) {
                $output = '<SELECT NAME="' . $name . '" ID="' . $id . '">';
                foreach (array_keys( $fparams ) as $key) {
                    $optiontext = $fparams[ $key ];
                    $selected = ($value == $optiontext ? 'SELECTED ' : '');
                    $output .= '<OPTION ' . $selected . 'VALUE="' . $key . '">' . $optiontext . '</OPTION>';
                }
                $output .= '</SELECT>';
            }
            
            break;
        case 'int':
            if ($fparams == NULL) {
                $fparams = 6;
            }

            // Fallthrough -- no break statement
        case 'string':
            if ($fparams == NULL) {
                $fparams = 2 * ($value == NULL ? 10 : strlen( $value ));
            }
            $output = '<INPUT TYPE="text" SIZE="' . $fparams .'" MAXLENGTH="255" NAME="' . $name . '" ID="' . $id . '" VALUE="' . $value . '" />';
                
            break;
        case 'time':
            if ($value == NULL) {
                $value = '00:00:00';
            }
            
            $value_h = (int) substr( $value, 0, 2 );
            $value_m = (int) substr( $value, 3, 2 );
            $value_s = (int) substr( $value, 6, 2 );
            
            $hbox = '';
            $mbox = '';
            $sbox = '';
            for ($i = 0; $i < 60; $i++) {
                $fi = sprintf( '%02d', $i );
                $optsel = '<OPTION SELECTED VALUE="' . $fi . '">' . $fi . '</OPTION>';
                $optnot = '<OPTION VALUE="' . $fi . '">' . $fi . '</OPTION>';

                if ($i < 24) {
                    $hbox .= ($value_h == $i ? $optsel : $optnot);
                }
                $mbox .= ($value_m == $i ? $optsel : $optnot);
                $sbox .= ($value_s == $i ? $optsel : $optnot);
            }

            $output = '<SELECT NAME="' . $name . '_h" ID="' . $id . '_h">' . $hbox . '</SELECT>:' .
                      '<SELECT NAME="' . $name . '_m" ID="' . $id . '_m">' . $mbox . '</SELECT>:' .
                      '<SELECT NAME="' . $name . '_s" ID="' . $id . '_s">' . $sbox . '</SELECT>';
            
            break;
        case 'subtable':
            $output = uiMkTable( $value, NULL );
            
            break;
    }
    
    return $output;
}

function uiGetHTTPParam( $paramname, $default = NULL ) {
    return nz( $_GET[ $paramname ], nz( $_POST[ $paramname ], $default ) );
}

function uiGetHTTPTimeParam( $paramname, $default = NULL ) {
    $hrs = nz( $_GET[ $paramname . '_h' ], $_POST[ $paramname . '_h' ] );
    $min = nz( $_GET[ $paramname . '_m' ], $_POST[ $paramname . '_m' ] );
    $sec = nz( $_GET[ $paramname . '_s' ], $_POST[ $paramname . '_s' ] );
    
    if (($hrs == NULL) || ($min == NULL) || ($sec == NULL)) {
        return $default;
    }
    
    return $hrs . ':' . $min . ':' . $sec;
}

function uiCompilePage( $pagename, $pagebody, $errormsg = array(), $menupage = NULL, $username = NULL ) {
    return uiMkPageHeader( $pagename, $errormsg, $menupage, $username ) .
           $pagebody .
           uiMkPageFooter( $menupage, $username );
}

function uiMkPageHeader( $pagename, $errormsg = array(), $menupage = NULL, $username = NULL ) {
    global $uiPageName;
    global $uiUseEditor;

    global $newln;

    $uiPageName = $pagename;
    $uiUseEditor = $useeditor;

    $pagename = 'Calgary Fringe Festival Box Office Central &mdash; ' . $pagename;

    $html = '<HTML>' . $newln .
            '<HEAD>' . $newln .
            '    <TITLE>' . $pagename . '</TITLE>' . $newln .
            '    <META HTTP-EQUIV="Content-Language" CONTENT="en-ca">' . $newln .
            '    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">' . $newln .
            '    <META NAME="robots" CONTENT="noindex, nofollow">' . $newln .
            '    <LINK REL="stylesheet" TYPE="text/css" HREF="/boxoffice.css">' . $newln .
            '    <SCRIPT TYPE="text/javascript" SRC="/admin.js"></SCRIPT>' . $newln .
            '    <STYLE MEDIA="print">' . $newln .
            '        .noprint { display: none; }' . $newln .
            '    </STYLE>' . $newln .
            '</HEAD>' . $newln .
            '<BODY>' . $newln .
            (isset( $menupage ) ? uiMkPageMenu( $menupage, $username ) : '') . $newln .
            '    <DIV CLASS="admincontent">' . $newln .
            '    <H2 CLASS="noprint">' . $pagename . '</H2>' . $newln . $newln .
            uiMkErrorMessage( $errormsg );

    return $html;
}

function uiMkPageFooter( $menupage = NULL, $username = NULL ) {
    global $newln;

    $html = '    </DIV>';

    if (isset( $menupage )) {
        // TODO: $fullname = $username . ' [' . dbGetFullName( $username ) . ']';
    }

    $fullname = $username;

    $html .= $newln .
             '    <BR /><BR />' . $newln .
             '    <DIV CLASS="adminfooter noprint">' . $newln .
             '        <SPAN CLASS="floatleft">' . date( 'l M. d, Y @ g:i A' ) . '</SPAN>' . $newln .
             '        <SPAN CLASS="floatright">Logged in as: ' . $fullname . '</SPAN>' . $newln .
             '    </DIV>' . $newln .
             '</BODY>' . $newln .
             '</HTML>';

    return $html;
}

function uiMkPageMenu( $currentpage, $username = NULL ) {
    global $newln;

    $items = array(
        'venues' => 'Venues',
        'shows' => 'Shows',
        'performances' => 'Performances',
        'endofday' => 'Daily Summary Report',
        'venuesuper' => 'Venue Supervisor Recons',
        'tickets' => 'Ticket Sales',
        'finalboxoffice' => 'Final Box Office Rept',
        'media' => 'Media Att',
        'cpoptix' => 'CarbonPop Search',
        'float' => 'Float Calc',
        'createfestival' => 'New Festival',
        'logout' => 'Log Out'
    );

    $menu = '';
    foreach (array_keys( $items ) as $action) {
        $itemtext = $items[ $action ];
        if ($action == $currentpage) {
            $itemtext = '<STRONG>' . $itemtext . '</STRONG>';
        } else {
            $itemtext = '<A HREF="/admin.php?action=' . $action . '">' . $itemtext . '</A>';
        }
        $menu .= '| ' . $itemtext . ' ';
    }

    return '    <DIV CLASS="admintopmenu noprint">' . substr( $menu, 1 ) . '</DIV><BR />' . $newln;
}

function uiMkErrorMessage( $errors ) {
    global $newln;

    if (count( $errors ) == 0) {
        return '';
    }

    $html = '    <DIV CLASS="adminerror">' . $newln .
            '        <P>The following errors occurred on this page:</P>' . $newln .
            '        <UL>' . $newln;
    foreach ($errors as $err) {
        $html .= '            <LI>' . $err . '</LI>' . $newln;
    }
    $html .= '        </UL>' . $newln .
             '    </DIV>' . $newln;

    return $html;
}

function uiMkLoginPage( $justloggedout = false, $badcred = false ) {
    global $newln;

    $errormsg = array();
    if ($badcred) {
        $errormsg[] = 'Invalid login credentials supplied. Please log in again.';
    }

    $body = '';
    if ($justloggedout) {
        $body .=
            '    <DIV CLASS="admininfo">' . $newln .
            '        <P>Logged out. Please supply username and password to log in again.</P>' . $newln .
            '    </DIV>' . $newln;
    }

    $body .=
        '    <FORM CLASS="admininfo" ACTION="admin.php" METHOD="post">' . $newln .
        '        <INPUT TYPE="hidden" NAME="action" VALUE="trylogin" />' . $newln .
        '        <LABEL ACCESSKEY="u"><SPAN CLASS="fixedlabel">Username:</SPAN><INPUT TYPE="text" NAME="userlogin" SIZE="40" MAXLENGTH="64" /></LABEL><BR />' . $newln .
        '        <LABEL ACCESSKEY="p"><SPAN CLASS="fixedlabel">Password:</SPAN><INPUT TYPE="password" NAME="passlogin" SIZE="40" MAXLENGTH="32" /></LABEL><BR />' . $newln .
        '        <SPAN CLASS="fixedlabel">&nbsp;</SPAN><INPUT TYPE="submit" VALUE="Login" ACCESSKEY="l" />' . $newln .
        '    </FORM>';

    return uiCompilePage( 'User Login', $body, $errormsg );
}

function uiMkMenuPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $body =
        '    <P>Select one of the following options:</P>' . $newln .
        '    <DIV CLASS="admincontentmenu">' . $newln .
        '        <A HREF="/admin.php?action=venues">View / Edit Venue Info</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=shows">View / Edit Show Info</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=performances">View / Edit Performance Info</A><BR /><BR />' . $newln .
        '        Reports:<BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=endofday" CLASS="indent">&raquo; End of Day Summary</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=venuesuper" CLASS="indent">&raquo; Venue Supervisor Reconciliations</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=tickets" CLASS="indent">&raquo; Ticket Sale Breakdown</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=finalboxoffice" CLASS="indent">&raquo; Final Box Office Report</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=media" CLASS="indent">&raquo; Media Attendance Summary</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=cpoptix">Seach CarbonPop Ticket Orders</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=float">Calculate Float</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=createfestival">Create new festival from CarbonPop data</A><BR /><BR />' . $newln .
        '        <A HREF="/admin.php?action=logout">Log Out ' . $username . '</A><BR /><BR />' . $newln .
        '    </DIV>' . $newln;

    return uiCompilePage( 'Main Menu', $body, $errormsg, '', $username );
}

function uiMkVenueListPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $venues = dbGetVenueList();
    if ($venues === false) {
        $errormsg[] = 'Unable to retrieve venue list.';
        $body = '';
    } else {
        $body =
            '    <P>Select one of the following venues:</P>' . $newln .
            '    <DIV CLASS="adminitemlist">' . $newln;

        foreach ($venues as $venue) {
            $link = '/admin.php?action=venuedetails&venueid=' . $venue[ 'ID' ];
            $body .= '    <A HREF="' . $link . '">' . $venue[ 'Name' ] . '</A><BR />' . $newln;
        }

        $body .=
            '    </DIV>' . $newln;
    }

    return uiCompilePage( 'Venue List', $body, $errormsg, 'venues', $username );
}

function uiMkVenueDetailPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $venueid = uiGetHTTPParam( 'venueid' );
    if ($venueid == NULL) {
        $errormsg[] = 'Venue ID not supplied.';
        return uiMkVenueListPage( $username, $errormsg );
    }

    $venue = dbGetVenueDetails( $venueid );
    if ($venue === false) {
        $errormsg[] = 'Unable to retrieve venue details.';
        $body = '';
    } else {
        $body = uiMkEditTable( $venue[ 'Details' ], 'venuedetails', 'ID=' . $venueid, $venue[ 'Name' ] ) . '<BR /><BR />' . $newln .
                '<STRONG>Button Sales, Concessions and Donations:</STRONG><BR /><UL>' . $newln;
                
        $days = dbGetPerformanceDates();
        if ($days == NULL) {
            $errormsg[] = 'Unable to retrieve list of performance days';
        } else {
            foreach ($days as $day) {
                $link = '/admin.php?action=venuesales&venueid=' . $venueid . '&day=' . $day[ 'Day' ];
                $body .= '    <A HREF="' . $link . '">' . $day[ 'Day' ] . '</A><BR />' . $newln;
            }
        }
        $body .= '</UL>' . $newln;
    }

    return uiCompilePage( 'Venue Details', $body, $errormsg, '', $username );
}

function uiMkVenueSalesPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $venueid = uiGetHTTPParam( 'venueid' );
    $day = uiGetHTTPParam( 'day' );
    
    if ($venueid == NULL) {
        $errormsg[] = 'Venue ID not supplied.';
        return uiMkVenueListPage( $username, $errormsg );
    }
    
    if ($day == NULL) {
        $errormsg[] = 'Day not supplied.';
        return uiMkVenueDetailPage( $username, $errormsg );
    }
    
    $windowid = dbGetWindowID( $venueid, $day );
    $venue = dbGetVenueName( $venueid );
    $numbuttons = dbGetNumButtonsSold( $windowid );
    $txns = dbGetWindowTxns( $windowid );
    $addparam = 'venueid=' . $venueid . '&day=' . $day;
    $body .= '<STRONG>For ' . $venue . ' on ' . $day . ':</STRONG><BR /><BR />' . $newln .
             uiMkTable( $txns[ 'ButtonSales' ], 'Button Sales', array( 'ID' ), 'buttons', $addparam ) . $newln .
             '(Total buttons sold = ' . ((int) $numbuttons[ 0 ][ 'Total' ]) . ')<BR /><BR /><BR />' . $newln .
             uiMkTable( $txns[ 'ConcessionSales' ], 'Concession Sales', array( 'ID' ), 'concessions', $addparam ) . '<BR />' . $newln .
             uiMkTable( $txns[ 'Donations' ], 'Donations', array( 'ID' ), 'donations', $addparam );
    
    return uiCompilePage( 'Venue Button Sales, Concessions and Donations', $body, $errormsg, '', $username );
}

function uiMkShowListPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $shows = dbGetShowList();
    if ($shows === false) {
        $errormsg[] = 'Unable to retrieve show list.';
        $body = '';
    } else {
        $body =
            '    <P>Select one of the following shows:</P>' . $newln .
            '    <DIV CLASS="adminitemlist">' . $newln;

        foreach ($shows as $show) {
            $link = '/admin.php?action=showdetails&showid=' . $show[ 'ID' ];
            $body .= '    <A HREF="' . $link . '">' . $show[ 'Name' ] . '</A><BR />' . $newln;
        }

        $body .=
            '    </DIV>' . $newln;
    }

    return uiCompilePage( 'Show List', $body, $errormsg, 'shows', $username );
}

function uiMkShowDetailPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $showid = uiGetHTTPParam( 'showid' );
    if ($showid == NULL) {
        $errormsg[] = 'Show ID not supplied.';
        return uiMkShowListPage( $username, $errormsg );
    }
    
    $show = dbGetShowDetails( $showid );
    if ($show === false) {
        $errormsg[] = 'Unable to retrieve show details.';
        $body = '';
    } else {
        $body = uiMkEditTable( $show[ 'Details' ], 'showdetails', 'ID=' . $showid, $show[ 'Name' ] );
    }
    
    return uiCompilePage( 'Show Details', $body, $errormsg, '', $username );
}

function uiMkPerformanceListPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $listday = dbGetFestivalDay( uiGetHTTPParam( 'day' ) );
    
    $days = dbGetPerformanceDates();
    $dateselect = '<SELECT ONCHANGE="window.location.href = (\'/admin.php?action=performances&day=\' + this.options[ this.selectedIndex ].value);">';
    foreach ($days as $day) {
        $selected = ($day[ 'Day' ] == $listday ? 'SELECTED ' : '');
        $dateselect .= '<OPTION ' . $selected . 'VALUE="' . $day[ 'Day' ] .'">' . $day[ 'Day' ] .'</OPTION>';
    }
    $dateselect .= '</SELECT>';
    
    $perfs = dbGetPerformanceList( $listday );
    if ($perfs === false) {
        $errormsg[] = 'Unable to retrieve performance list.';
        $body = '';
    } else {
        $body =
            '    <P>Select one of the following performances:</P>' . $newln .
            '    <DIV CLASS="adminitemlist">' . $newln;
        
        $day = '';
        $venue = '';
        foreach ($perfs as $perf) {
            if ($perf[ 'PerformanceDate' ] != $day) {
                $day = $perf[ 'PerformanceDate' ];
                $body .= '        <STRONG>Date: ' . $dateselect . '</STRONG><BR />' . $newln;
            }
            if ($perf[ 'VenueName' ] != $venue) {
                $venue = $perf[ 'VenueName' ];
                $body .= '        <SPAN STYLE="margin: 0px 30px;"><STRONG>Venue: ' . $venue . '</STRONG></SPAN><BR />' . $newln;
            }
            $link = '/admin.php?action=performancedetails&perfid=' . $perf[ 'ID' ];
            $body .= '    <A HREF="' . $link . '" STYLE="margin: 0px 60px;">' . $perf[ 'ShowNameTime' ] . '</A><BR />' . $newln;
        }

        $body .=
            '    </DIV>' . $newln;
    }

    return uiCompilePage( 'Performance List', $body, $errormsg, 'performances', $username );
}

function uiMkPerformanceDetailPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $perfid = uiGetHTTPParam( 'perfid' );
    if ($perfid == NULL) {
        $errormsg[] = 'Performance ID not supplied';
        return uiMkPerformanceListPage( $username, $errormsg );
    }
    
    $perf = dbGetPerformanceDetails( $perfid );
    if ($perf === false) {
        $errormsg[] = 'Unable to retrieve performance details';
        $body = '';
    } else {
        $body = uiMkEditTable( $perf[ 'Details' ], 'performancedetails', 'ID=' . $perfid, $perf[ 'Name' ] ) . '<BR />' . $newln .
                '<SPAN STYLE="margin: 0px 30px;">&#8627; <A HREF="#presales">Presales</A></SPAN><BR />' . $newln .
                '<SPAN STYLE="margin: 0px 30px;">&#8627; <A HREF="#info">Info Tent Transactions</A></SPAN><BR />' . $newln .
                '<SPAN STYLE="margin: 0px 30px;">&#8627; <A HREF="#venue">At-venue Transactions</A></SPAN><BR />' . $newln .
                '<SPAN STYLE="margin: 0px 30px;">&#8627; <A HREF="#summary">Ticket Sale Summary</A></SPAN><BR />' . $newln .
                '<SPAN STYLE="margin: 0px 30px;">&#8627; <A HREF="#payout">Artist Payout</A></SPAN><BR />' . $newln .
                '<BR /><BR />' . $newln;

        $presales = dbGetPresales( $perfid );
        $addparam = 'pid=' . $perfid;
        $body .= '<A NAME="presales" /><STRONG>Online Presales:</STRONG><BR /><UL>' . $newln .
                 uiMkTable( $presales, 'Online Presales', array( 'OrderNum' ), 'presales', $addparam ) . '<BR />' . $newln .
                 '</UL>' . $newln;

        $txns = dbGetPerformanceTxns( $perfid, false );
        $addparam = 'pid=' . $perfid . '&info=y&type=';
        $body .= '<A NAME="info" /><STRONG>Info Tent Transactions:</STRONG><BR /><UL>' . $newln .
                 uiMkTable( $txns[ 'CashSales' ], 'Cash Sales', array( 'ID' ), 'infosales', $addparam . 'cash' ) . '<BR />' . $newln .
                 uiMkTable( $txns[ 'Comps' ], 'Ticket Comps', array( 'ID' ), 'infocomps', $addparam . 'comp' ) . '<BR />' . $newln .
                 '</UL>' . $newln;

        $txns = dbGetPerformanceTxns( $perfid, true );
        $addparam = 'pid=' . $perfid . '&info=n&type=';
        $body .= '<A NAME="venue" /><STRONG>At-Venue Transactions:</STRONG><BR /><UL>' . $newln .
                 uiMkTable( $txns[ 'CashSales' ], 'Cash Sales', array( 'ID' ), 'venuesales', $addparam . 'cash' ) . '<BR />' . $newln .
                 uiMkTable( $txns[ 'Comps' ], 'Ticket Comps', array( 'ID' ), 'venuecomps', $addparam . 'comp' ) . '<BR />' . $newln .
                 '</UL>' . $newln;

        $body .= '<A NAME="summary" /><STRONG>Ticket Sale Summary:</STRONG><BR />' . $newln .
                 uiMkTable( array( dbGetPerformanceTicketSummary( $perfid ) ) ) . '<BR /><BR />' . $newln;

        $body .= '<A NAME="payout" /><STRONG>Artist Payout:</STRONG><BR />' . $newln .
                 uiMkPayoutTable( $perfid );
    }

    return uiCompilePage( 'Performance Details', $body, $errormsg, '', $username );
}

function uiMkPayoutTable( $perfid ) {
    global $newln;

    $payout = dbGetArtistPayout( $perfid );

    $output = '<FORM ACTION="/admin.php"> ' . $newln .
              '<INPUT TYPE="hidden" NAME="perfid" VALUE="' . $perfid . '" />' . $newln .
              '<TABLE BORDER="1" CELLPADDING="2" CELLSPACING="0">' . $newln .
              '<TR>' . $newln;

    $n = count( $payout[ 'tickets' ] );
    if ($n > 0) {
        $output .= '    <TD ROWSPAN="' . $n . '" VALIGN="top">Ticket Sales:</TD>' . $newln;

        foreach (array_keys( $payout[ 'tickets' ] ) as $price) {
            if ($price == 'Default') {
                $desc = 'Normal payout ($' . number_format( $payout[ 'ToArtist' ] / 100, 2 ) . ')';
                $num = $payout[ 'tickets' ][ $price ];
                $val = $num . ' = $' . number_format( ($num * $payout[ 'ToArtist' ]) / 100, 2 );
            } else {
                $desc = 'Price override ($' . number_format( $price / 100, 2 ) . ')';
                $num = $payout[ 'tickets' ][ $price ];
                $val = $num . ' = $' . number_format( ($num * $price) / 100, 2 );
            }
            $output .= '    <TD>' . $desc . '</TD>' . $newln .
                       '    <TD>' . $val . '</TD>' . $newln .
                       '</TR><TR>' . $newln;
        }
    }

    $n = count( $payout[ 'comps' ] );
    if ($n > 0) {
        $output .= '    <TD ROWSPAN="' . $n . '" VALIGN="top">Ticket Comps:</TD>' . $newln;

        foreach (array_keys( $payout[ 'comps' ] ) as $kind) {
            if ($kind == 'Superpass') {
                $desc = 'Superpasses ($10.00)';
                $num = $payout[ 'comps' ][ $kind ];
                $val = $num . ' = $' . number_format( $num * 10, 2 );
            } else {
                $desc = $kind;
                $val = $payout[ 'comps' ][ $kind ];
            }
            $output .= '    <TD>' . $desc . '</TD>' . $newln .
                       '    <TD>' . $val . '</TD>' . $newln .
                       '</TR><TR>' . $newln;
        }
    }

    if (trim( $payout[ 'media' ] ) != '') {
        $output .= '    <TD VALIGN="top">Media:</TD>' . $newln .
                   '    <TD COLSPAN="2">' . $payout[ 'media' ] . '</TD>' . $newln .
                   '</TR><TR>' . $newln;
    }

    $output .= '    <TD><STRONG>Payout:</STRONG></TD>' . $newln .
               '    <TD COLSPAN="2" ALIGN="right"><STRONG>$' . number_format( $payout[ 'total' ] / 100, 2 ) . '</STRONG></TD>' . $newln .
               '</TR><TR>' . $newln;

    $payoutmade = dbGetPayoutMade( $perfid, false );
    if ($payoutmade === false) {
        $makepayoutfield = '$<INPUT TYPE="text" NAME="amount" VALUE="' . number_format( $payout[ 'total' ] / 100, 2 ) . '" SIZE="7" />' . $newln .
                           '<INPUT TYPE="hidden" NAME="action" VALUE="formmakepayout" />' . $newln .
                           '<INPUT TYPE="submit" VALUE="Make Payout Now" />';
    } else {
        list( $idmade, $amtmade ) = $payoutmade;
        $makepayoutfield = '<EM>(Payout Made: $' . number_format( $amtmade / 100, 2 ) . ')</EM><BR /><BR />' . $newln .
                           '$<INPUT TYPE="text" NAME="amount" VALUE="' . number_format( $amtmade / 100, 2 ) . '" SIZE="7" />' . $newln .
                           '<INPUT TYPE="hidden" NAME="action" VALUE="formupdatepayout" />' . $newln .
                           '<INPUT TYPE="hidden" NAME="payouttxnid" VALUE="' . $idmade . '" />' . $newln .
                           '<INPUT TYPE="submit" VALUE="Update the Payout Made" />';
    }

    $output .= '    <TD COLSPAN="3" ALIGN="right">' . $makepayoutfield . '</TD>' . $newln .
               '</TR>' . $newln .
               '</TABLE>' . $newln .
               '</FORM>' . $newln;

    return $output;
}

function uiMkTicketBreakdownListPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $days = dbGetPerformanceDates();
    if ($days == NULL) {
        $errormsg[] = 'Unable to retrieve list of performance days.';
        $body = '';
    } else {
        $body = '    <P>Show Ticket Breakdown for:</P>' . $newln .
                '    <DIV CLASS="adminitemlist">' . $newln;
        foreach ($days as $day) {
            $link = '/admin.php?action=dailytickets&day=' . $day[ 'Day' ];
            $body .= '        <A HREF="' . $link . '">' . $day[ 'Day' ] . '</A><BR />' . $newln;
        }
        $body .= '    </DIV>' . $newln;
    }

    return uiCompilePage( 'Ticket Breakdowns', $body, $errormsg, 'tickets', $username );
};

function uiMkTicketBreakdownPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $day = uiGetHTTPParam( 'day' );
    if ($day == NULL) {
        $errormsg[] = 'Day not supplied.';
        return uiMkTicketBreakdownListPage( $username, $errormsg );
    }

    $body = '';

//    $breakdown = dbGetDailyTicketBreakdown( $day );
    $breakdown = dbGetDailyTotalTicketSummary( $day );
    if ($breakdown == NULL) {
        $errormsg[] = 'Unable to retrieve daily ticket sale breakdown.';
    } else {
        $body .= uiMkTable( $breakdown, 'Ticket Sales for ' . $day );
    }
    
    return uiCompilePage( 'Ticket Sale Breakdown', $body, $errormsg, '', $username );
}

function uiMkEndOfDayListPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $days = dbGetPerformanceDates();
    if ($days == NULL) {
        $errormsg[] = 'Unable to retrieve list of performance days.';
        $body = '';
    } else {
        $body = '    <P>Show End-Of-Day Summary for:</P>' . $newln .
                '    <DIV CLASS="adminitemlist">' . $newln;
        foreach ($days as $day) {
            $link = '/admin.php?action=dailyendofday&day=' . $day[ 'Day' ];
            $body .= '        <A HREF="' . $link . '">' . $day[ 'Day' ] . '</A><BR />' . $newln;
        }
        $body .= '    </DIV>' . $newln;
    }

    return uiCompilePage( 'End-Of-Day Summary', $body, $errormsg, 'endofday', $username );
}

function uiMkEndOfDaySummaryPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $day = uiGetHTTPParam( 'day' );
    if ($day == NULL) {
        $errormsg[] = 'Day not supplied.';
        return uiMkEndOfDayListPage( $username, $errormsg );
    }

    $reports = dbGetEndOfDaySummary( $day );
    if ($reports == NULL) {
        $errormsg[] = 'Unable to retrieve end-of-day summary';
        $body = '';
    } else {
        $body = '<STRONG>End Of Day Summary Report For ' . $day . ':</STRONG><BR /><BR />' . $newln . $newln .
                '<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="1">' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Ticket Sales:</EM></TD><TD ALIGN="right">' . $reports[ 'normal sales' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Special Sales (e.g.: Senior Tuesday):</EM></TD><TD ALIGN="right">' . $reports[ 'special sales' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Button Sales:</EM></TD><TD ALIGN="right">' . $reports[ 'button sales' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Donations:</EM></TD><TD ALIGN="right">' . $reports[ 'donations' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Concessions:</EM></TD><TD ALIGN="right">' . $reports[ 'concessions' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Merchandise:</EM></TD><TD ALIGN="right">' . $reports[ 'merchandise' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>Superpasses:</EM></TD><TD ALIGN="right">' . $reports[ 'superpasses' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM>TOTAL CASH RECEIVED:</EM></TD><TD STYLE="border-top: 1px solid black;" ALIGN="right">' . $reports[ 'TOTAL CASH RECEIVED' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="3">&nbsp;</TD></TR>' . $newln .
                '    <TR><TD><EM>ARTIST PAYOUTS:</EM></TD><TD>- </TD><TD ALIGN="right">' . $reports[ 'ARTIST PAYOUTS' ] . '</TD></TR>' . $newln .
                '    <TR><TD COLSPAN="2"><EM><STRONG>REMAINING CASH:</STRONG></EM></TD><TD STYLE="border-top: 1px solid black;" ALIGN="right"><STRONG>' . $reports[ 'REMAINING CASH' ] . '</STRONG></TD></TR>' . $newln .
                '</TABLE><BR /><BR /><BR />' . $newln . $newln;

        $windows = dbGetWindowListByDay( $day );

        if ($windows !== false) {
            $body .= '<STRONG>End Of Day Cash Per Venue</STRONG>:<BR /><BR />' . $newln .
                     '<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="2" CLASS="adminitemlist">' . $newln;
            foreach ($windows as $window) {
                $wid = $window[ 'ID' ];
                $details = dbGetEndOfDayCash( $wid );
                $body .= '<TR><TD>' .
                         $window[ 'Name' ] . ': &nbsp; &nbsp; ' .
                         '</TD><TD ALIGN="right">$' . number_format( $details[ 'Summary' ] / 100, 2 ) . ' ' .
                         '</TD><TD> &nbsp; .. &nbsp; ' .
                         '</TD><TD ALIGN="right">' . ($details[ 'Buttons' ] / 500) . ' buttons sold ' .
                         '</TD><TD>' .
                         '&nbsp; <SMALL><A HREF="/admin.php?action=windowcash&id=' . $wid . '">(See Breakdown)</A><SMALL><BR />' .
                         '</TD></TR>' . $newln;
            }
            $body .= '</TABLE>';
        }
    }

    return uiCompilePage( 'End-Of-Day Summary', $body, $errormsg, '', $username );
}

function uiMkVenueSuperListPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $days = dbGetPerformanceDates();
    if ($days == NULL) {
        $errormsg[] = 'Unable to retrieve list of performance days.';
        $body = '';
    } else {
        $body = '    <P>Show Venue Supervisor and Artist Reconciliation Reports for:</P>' . $newln .
                '    <DIV CLASS="adminitemlist">' . $newln;
        foreach ($days as $day) {
            $link = '/admin.php?action=dailyvenuesuper&day=' . $day[ 'Day' ];
            $body .= '        <A HREF="' . $link . '">' . $day[ 'Day' ] . '</A><BR />' . $newln;
        }
        $body .= '    </DIV>' . $newln;
    }

    return uiCompilePage( 'Venue Supervisor / Artist Reconciliation Reports', $body, $errormsg, 'venuesuper', $username );
}

function uiMkVenueSuperReconciliationsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $day = uiGetHTTPParam( 'day' );
    if ($day == NULL) {
        $errormsg[] = 'Day not supplied.';
        return uiMkVenueSuperListPage( $username, $errormsg );
    }

    $reports = dbGetVenueSuperReconciliations( $day );
    if ($reports == NULL) {
        $errormsg[] = 'Unable to retrieve list of reconciliation reports';
        $body = '';
    } else {
        $body = uiMkTable( $reports, 'Venue Supervisor and Artist Reconciliation Reports For ' . $day . ':' );
    }

    return uiCompilePage( 'Venue Supervisor / Artist Reconciliation Reports', $body, $errormsg, '', $username );
}

function uiMkFinalReportXL( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $errormsg[] = 'Not yet implemented';
    $body = '';
    
    return uiCompilePage( 'Final Box Office Report', $body, $errormsg, 'finalboxoffice', $username );
}

function uiMkFloatCalculatorPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $body = '    <STRONG>Float Calculator:</STRONG><BR /><BR />' . $newln .
            '    <TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1">' . $newln .
            '    <TR>' . $newln .
            '        <TD><EM>Denomination</EM></TD>' . $newln .
            '        <TD><EM>Quantity</EM></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$100.00 &nbsp;</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_hundreds" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$50.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_fifties" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$20.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_twenties" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$10.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_tens" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$5.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_fives" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$2.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_toonies" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$1.00</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_loonies" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD>$0.25</TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="3" MAXLENGTH="3" ID="calc_quarters" VALUE="0" /></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD COLSPAN="2"></TD>' . $newln .
            '    </TR><TR>' . $newln .
            '        <TD ALIGN="right"><INPUT TYPE="button" VALUE="Calculate >>>" ONCLICK="calculateFloat();" /></TD>' . $newln .
            '        <TD><INPUT TYPE="text" SIZE="10" MAXLENGTH="10" ID="calc_total" VALUE="$0.00" READONLY /></TD>' . $newln .
            '    </TR>' . $newln .
            '    </TABLE><BR /><BR />' . $newln;
            
    $days = dbGetPerformanceDates();
    $venues = dbGetVenueList();
    $today = date( 'Y-m-d' );
    
    $body .= '    <FORM NAME="floatform" ACTION="/admin.php" METHOD="post" />' . $newln .
             '    <INPUT TYPE="hidden" NAME="action" VALUE="formmakefloat" />' . $newln .
             '    <INPUT TYPE="hidden" ID="form_amount" NAME="amount" VALUE="" />' . $newln .
             '    Give the above as a float on ' . $newln .
             '    <SELECT NAME="floatday">';
    foreach ($days as $day) {
        $selected = (($day[ 'Day' ] == $today) ? 'SELECTED ' : '');
        $body .= '<OPTION ' . $selected . 'VALUE="' . $day[ 'Day' ] . '">' . $day[ 'Day' ] . '</OPTION>';
    }
    $body .= '</SELECT> to:<BR />' . $newln .
             '    <DIV CLASS="adminitemlist">' . $newln;
    foreach ($venues as $venue) {
        // Venue 12 is Lantern basement - doesn't get a float
        if ($venue[ 'ID' ] != 12) {
            $body .= '        <INPUT TYPE="checkbox" NAME="' . $venue[ 'ID' ] . '" VALUE="yes" /> ' . $venue[ 'Name' ] . '<BR />' . $newln;
        }
    }
    $body .= '    </DIV>' . $newln .
             '    <INPUT TYPE="button" VALUE="Update Float(s)" ONCLICK="submitFloatForm();" />' . $newln .
             '    </FORM><BR />' . $newln;

    return uiCompilePage( 'Float Calculator', $body, $errormsg, 'float', $username );
}

function uiMkEditButtonsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $id = uiGetHTTPParam( 'ID' );

    $lineitem = dbGetButtonSaleLineItem( $id );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'buttonsale', 'ID=' . $id, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Button Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkEditConcessionsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $id = uiGetHTTPParam( 'ID' );

    $lineitem = dbGetConcessionSaleLineItem( $id );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'concessionsale', 'ID=' . $id, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Concession Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkEditDonationsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $id = uiGetHTTPParam( 'ID' );

    $lineitem = dbGetDonationLineItem( $id );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'donation', 'ID=' . $id, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Donation Line Item', $body, $errormsg, '', $username );
}

function uiMkEditPresalesPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $ordernum = uiGetHTTPParam( 'OrderNum' );

    $lineitem = dbGetPresaleLineItem( $ordernum );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'presale', 'OrderNum=' . $ordernum, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Presale Line Item', $body, $errormsg, '', $username );
}

function uiMkEditTicketSalesPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $id = uiGetHTTPParam( 'ID' );

    $lineitem = dbGetTicketSaleLineItem( $id );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'ticketsale', 'ID=' . $id, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Ticket Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkEditTicketCompsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $id = uiGetHTTPParam( 'ID' );

    $lineitem = dbGetTicketCompLineItem( $id );
    if ($lineitem === false) {
        $errormsg[] = 'Unable to retrieve line item for editing.';
        $body = false;
    } else {
        $body = uiMkEditTable( $lineitem[ 'Data' ], 'ticketcomp', 'ID=' . $id, $lineitem[ 'Name' ] );
    }

    return uiCompilePage( 'Edit Ticket Comp Line Item', $body, $errormsg, '', $username );
}

function uiMkAddButtonsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $vid = uiGetHTTPParam( 'venueid' );
    $day = uiGetHTTPParam( 'day' );
    $idstring = 'venueid=' . $vid . '&day=' . $day;

    $venue = dbGetVenueName( $vid );

    $tbl = array(
        'Venue' => array( 'Value' => $venue, 'Type' => 'readonly' ),
        'Day' => array( 'Value' => $day, 'Type' => 'readonly' ),
        'SaleTime' => array( 'Value' => date( 'H:i:s' ), 'Type' => 'time' ),
        'NumButtons' => array( 'Value' => 0, 'Type' => 'int' ),
        'Note' => array( 'Value' => '', 'Type' => 'string', 'Params' => 50 )
    );
    $body = uiMkEditTable( $tbl, 'newbuttonsale', $idstring, 'New Button Sale' );

    return uiCompilePage( 'Add Button Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkAddConcessionsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $vid = uiGetHTTPParam( 'venueid' );
    $day = uiGetHTTPParam( 'day' );
    $idstring = 'venueid=' . $vid . '&day=' . $day;

    $venue = dbGetVenueName( $vid );
    $itemlist = dbGetConcessionItemParamList();

    $itemids = array_keys( $itemlist );
    $item0 = $itemlist[ $itemids[ 0 ] ];

    $tbl = array(
        'Venue' => array( 'Value' => $venue, 'Type' => 'readonly' ),
        'Day' => array( 'Value' => $day, 'Type' => 'readonly' ),
        'SaleTime' => array( 'Value' => date( 'H:i:s' ), 'Type' => 'time' ),
        'ItemName' => array( 'Value' => $item0, 'Type' => 'enum', 'Params' => $itemlist ),
        'NumItems' => array( 'Value' => 0, 'Type' => 'int' )
    );
    $body = uiMkEditTable( $tbl, 'newconcessionsale', $idstring, 'New Concession Sale' );

    return uiCompilePage( 'Add Concession Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkAddDonationsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $vid = uiGetHTTPParam( 'venueid' );
    $day = uiGetHTTPParam( 'day' );
    $idstring = 'venueid=' . $vid . '&day=' . $day;

    $venue = dbGetVenueName( $vid );

    $tbl = array(
        'Venue' => array( 'Value' => $venue, 'Type' => 'readonly' ),
        'Day' => array( 'Value' => $day, 'Type' => 'readonly' ),
        'DonationTime' => array( 'Value' => date( 'H:i:s' ), 'Type' => 'time' ),
        'Amount' => array( 'Value' => 0, 'Type' => 'currency' ),
        'Note' => array( 'Value' => '', 'Type' => 'string', 'Params' => 50 )
    );
    $body = uiMkEditTable( $tbl, 'newdonation', $idstring, 'New Donation' );

    return uiCompilePage( 'Add Donation Line Item', $body, $errormsg, '', $username );
}

function uiMkAddPresalesPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $pid = uiGetHTTPParam( 'pid' );

    $pdefns = dbGetPerformancesParamList();
    $pdefn = dbGetPerformanceDefn( $pid );

    // This next call isn't remotely thread-safe!
    // TODO: Yeah. Do something about that.
    $ordernum = dbGetNextFreePresaleOrderNum();

    $tbl = array(
        'OrderNum' => array( 'Value' => $ordernum, 'Type' => 'string', 'Params' => 10 ),
        'GuestName' => array( 'Value' => '', 'Type' => 'string', 'Params' => 40 ),
        'Performance' => array( 'Value' => $pdefn, 'Type' => 'enum', 'Params' => $pdefns ),
        'NumTickets' => array( 'Value' => 0, 'Type' => 'int' ),
        'PickedUp' => array( 'Value' => 'N', 'Type' => 'bool' ),
        'PassType' => array( 'Value' => 'Manual', 'Type' => 'readonly' )
    );
    $body = uiMkEditTable( $tbl, 'newpresale', 'pID=' . $pid, 'New Presale' );

    return uiCompilePage( 'Add Presale Line Item', $body, $errormsg, '', $username );
}

function uiMkAddTicketSalesPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $pid = uiGetHTTPParam( 'pid' );
    $pdefn = dbGetPerformanceDefn( $pid );

    $info = strtoupper( uiGetHTTPParam( 'info' ) );
    $venue = ($info == 'Y' ? 'Information Tent' : dbGetPerformanceVenueName( $pid ));
    
    $idstring = 'pID=' . $pid . '&info=' . $info;

    $tbl = array(
        'Location' => array( 'Value' => $venue, 'Type' => 'readonly' ),
        'Performance' => array( 'Value' => $pdefn, 'Type' => 'readonly' ),
        'SaleTime' => array( 'Value' => date( 'H:i:s' ), 'Type' => 'time' ),
        'NumTickets' => array( 'Value' => 0, 'Type' => 'int' ),
        'Note' => array( 'Value' => '', 'Type' => 'string', 'Params' => 50 ),
        'PriceOverride' => array( 'Value' => 0, 'Type' => 'currency' ),
        'ToArtistOverride' => array( 'Value' => 0, 'Type' => 'currency' )
    );
    $body = uiMkEditTable( $tbl, 'newticketsale', $idstring, 'New Ticket Sale' );

    return uiCompilePage( 'Add Ticket Sale Line Item', $body, $errormsg, '', $username );
}

function uiMkAddTicketCompsPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $pid = uiGetHTTPParam( 'pid' );
    $pdefn = dbGetPerformanceDefn( $pid );

    $info = strtoupper( uiGetHTTPParam( 'info' ) );
    $venue = ($info == 'Y' ? 'Information Tent' : dbGetPerformanceVenueName( $pid ));
    
    $idstring = 'pID=' . $pid . '&info=' . $info;

    $reasons = dbGetCompReasonsParamList();

    $tbl = array(
        'Location' => array( 'Value' => $venue, 'Type' => 'readonly' ),
        'Performance' => array( 'Value' => $pdefn, 'Type' => 'readonly' ),
        'SaleTime' => array( 'Value' => date( 'H:i:s' ), 'Type' => 'time' ),
        'NumTickets' => array( 'Value' => 0, 'Type' => 'int' ),
        'Reason' => array( 'Value' => 'All-Access', 'Type' => 'enum', 'Params' => $reasons ),
        'Note' => array( 'Value' => '', 'Type' => 'string', 'Params' => 50 )
    );
    $body = uiMkEditTable( $tbl, 'newticketcomp', $idstring, 'New Ticket Comp' );

    return uiCompilePage( 'Add Ticket Comp Line Item', $body, $errormsg, '', $username );
}

function uiMkWindowCashSummaryPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $windowid = uiGetHTTPParam( 'id' );
    if ($windowid == NULL) {
        $errormsg[] = 'Venue not supplied.';
        return uiMkEndOfDayListPage( $username, $errormsg );
    } else {
        $body = '';
        
        $cashlist = dbGetEndOfDayCash( $windowid );
        if ($cashlist == NULL) {
            $errormsg[] = 'Unable to retrieve list of day\'s transactions.';
        } else {
            $headline = 'Daily Cash Transaction Summary';
            $details = dbGetDayAndVenue( $windowid );
            if ($details !== false) {
                $headline .= ' for ' . $details[ 'Venue' ] . ' on ' . $details[ 'Day' ];
            }

            $body = '<STRONG>' . $headline . '</STRONG><BR /><BR />' . $newln . $newln .
                    '<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="adminitemlist">' . $newln .
                    '<TR><TD COLSPAN="2">Regular-price ticket sales:</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Normal' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TR><TD COLSPAN="2">Special-price ticket sales:</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Special' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TR><TD COLSPAN="2">Button sales:</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Buttons' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TR><TD COLSPAN="2">Superpass sales:</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Superpass' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TR><TD>Artist payouts (for venue booths): &nbsp; &nbsp;</TD><TD>-&nbsp;</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Payouts' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TR><TD>Surplus cash runs (for info tent):</TD><TD>-&nbsp;</TD><TD ALIGN="right">$' . number_format( $cashlist[ 'Surplus' ] / 100, 2 ) . '</TD></TR>' . $newln .
                    '<TD><TD COLSPAN="3">&nbsp;</TD></TR>' . $newln .
                    '<TR><TD COLSPAN="2"><STRONG>Cash at end of day:</STRONG></TD><TD ALIGN="right"><STRONG>$' . number_format( $cashlist[ 'Summary' ] / 100, 2 ) . '</STRONG></TD></TR>' . $newln .
                    '</TABLE>' . $newln;
        }
    }

    return uiCompilePage( 'Venue Cash Summary', $body, $errormsg, '', $username );
}

function uiMkMediaAttSummaryPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $body = uiMkTable( dbGetMediaAttSummary(), 'Media Attendance Summary' );
    
    return uiCompilePage( 'Media Attendance Summary', $body, $errormsg, 'media', $username );
}

function uiMkCarbonPopTicketCheckPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $span = dbGetFestivalSpan();
    $endtime = $span[ 'EndDate' ] . ' 23:59:59';
    $starttime = date( 'Y-m-d H:i:s', time() - 7200 ); // Because CarbonPop lives 2 hours in the future...

    $cpopsellouts = getCPopOnlineSoldOut( $starttime, $endtime, 9 );

    $guestname = uiGetHTTPParam( 'guestname' );
    if (trim( $guestname ) != '') {
        $headline = 'CarbonPop Orders With Guest Names Containing "' . $guestname . '"';
        $body = uiMkTable( dbGetPresalesByName( $guestname ), $headline ) . '<BR /><BR />' . $newln .
                'Check CarbonPop orders for guest names containing the following:' . $newln;
    } else {
        $body = '<STRONG>Check CarbonPop orders for guest names containing the following:</STRONG><BR />' . $newln;
    }

    $body .= '<BR />' . $newln .
             '<FORM ACTION="/admin.php" METHOD="get">' . $newln .
             '    <INPUT TYPE="hidden" NAME="action" VALUE="cpoptix" />' . $newln .
             '    <INPUT TYPE="text" NAME="guestname" VALUE="" SIZE="50" /><BR />' . $newln .
             '    <INPUT TYPE="submit" VALUE="Check Now" />' . $newln .
             '</FORM><BR /><BR />' . $newln .
             '<STRONG>Remaining shows with fewer than 10 online tickets left:</STRONG><BR />' . $newln .
             '<TABLE CELLPADDING="2" CELLSPACING="0" BORDER="1" CLASS="admininfo">' . $newln .
             '<TR><TD><EM>Title</EM></TD><TD><EM>ShowsAt</EM></TD><TD><EM>Remaining Tickets</EM></TD></TR>' . $newln;

    if ($cpopsellouts !== false) {
        foreach ($cpopsellouts as $perf) {
            if ($perf[ 'remaining' ] == 0) {
                $class = 'alert';
                $remaining = '<STRONG>0</STRONG>';
            } else {
                $class = '';
                $remaining = $perf[ 'remaining' ];
            }
            $body .= '<TR>' .
                     '<TD CLASS="' . $class . '">' . $perf[ 'title' ] . '</TD>' .
                     '<TD CLASS="' . $class . '">' . $perf[ 'shows_at' ] . '</TD>' .
                     '<TD CLASS="' . $class . '">' . $remaining . '</TD>' .
                     '</TR>' . $newln;
        }
    }
    
    $body .= '</TABLE><BR />' . $newln;

    return uiCompilePage( 'CarbonPop Order Check', $body, $errormsg, 'cpoptix', $username );
}

function uiMkCreateFestivalPage( $username = NULL, $errormsg = array() ) {
    global $newln;

    $year = date( 'Y' );
    $span = dbGetFestivalSpan( $year );
    if ($span === false) {
        $start = $year . '-08-01';
        $end   = $year . '-08-09';
    } else {
        $start = $span[ 'StartDate' ];
        $end   = $span[ 'EndDate' ];
    }

    $body = '<STRONG>Import ' . $year . ' Festival Data From CarbonPop</STRONG><BR />' . $newln .
            '<BR />' . $newln .
            '<FORM CLASS="admininfo" Name="newfestivalform" ACTION="/admin.php" METHOD="post">' . $newln .
            '    <INPUT TYPE="hidden" NAME="action" VALUE="formcreatefestival" />' . $newln .
            '    <EM><SPAN CLASS="alert">This will erase and reset all show and performance data between ' . $newln .
            '        <INPUT TYPE="text" NAME="startdate" ID="startdate" VALUE="' . $start . '" SIZE="10" /> ' . $newln .
            '    and ' . $newln .
            '        <INPUT TYPE="text" NAME="enddate" ID="enddate" VALUE="' . $end . '" SIZE="10" /> ' . $newln .
            '    , inclusive.<BR />' . $newln . 
            '    Be sure this is what you want before proceeding!</SPAN></EM><BR />' . $newln .
            '    <INPUT TYPE="checkbox" NAME="verify" ID="verifycreation" VALUE="yes" > Yes, I am sure.<BR /><BR />' . $newln .
            '    <INPUT TYPE="button" VALUE="Import ' . $year . ' Festival Data NOW" ONCLICK="submitNewFestivalForm();" />' . $newln .
            '</FORM><BR />' . $newln;
    
    return uiCompilePage( 'Create New Festival', $body, $errormsg, 'createfestival', $username );
}

function uiMkFestivalCreatedPage( $username = NULL, $errormsg = array() ) {
    global $newln;
    
    $startdate          = uiGetHTTPParam( 'startdate' );
    $enddate            = uiGetHTTPParam( 'enddate' );
    $stats_artists      = uiGetHTTPParam( 'stats_artists' );
    $stats_shows        = uiGetHTTPParam( 'stats_shows' );
    $stats_performances = uiGetHTTPParam( 'stats_performances' );
    $stats_windows      = uiGetHTTPParam( 'stats_windows' );
    $stats_pwindows     = uiGetHTTPParam( 'stats_pwindows' );
    
    $year = date( 'Y' );
    
    if (count( $errormsg ) == 0) {
        $body = 'Artist, Show, Window and Performance data for the ' . $year . ' festival has been imported from CarbonPop.<BR /><BR />' . $newln .
                'Artists imported: ' . $stats_artists . '<BR />' . $newln .
                'Shows imported: ' . $stats_shows . '<BR />' . $newln .
                'Performances imported: ' . $stats_performances . '<BR />' . $newln .
                'Windows imported: ' . $stats_windows . '<BR />' . $newln .
                'Performance Windows imported: ' . $stats_pwindows . '<BR />' . $newln .
                'The festival runs from ' . $startdate . ' through ' . $enddate . '.<BR /><BR />' . $newln .
                'Please remember to flip the <TT>$thisYear</TT> variable in <TT>dbfuncs_boxoffice.php</TT>.<BR /><BR />' . $newln;
    } else {
        $body = 'No new festival created.' . $newln;
    }
    
    return uiCompilePage( 'New Festival Created', $body, $errormsg, '', $username );
}

function uiTakeAction( $action, $username = NULL ) {
    $errormsg = array();

    $params = array();
    $fields = uiGetHTTPParam( 'fields' );
    if ($fields != NULL) {
        foreach (explode( '&', $fields ) as $field){
            list( $k, $v ) = explode( '=', $field, 2 );
            $params[ $k ] = $v;
        }
    }

    switch ($action) {
        case 'formdeleteinfosales':
        case 'formdeletevenuesales':
            $pid = dbFormDeleteCashSale( $params[ 'ID' ] );
            if ($pid === false) {
                $errormsg[] = 'Could not delete ticket sale item.';
                $action = 'menu';
            } else {
                $_GET[ 'perfid' ] = $pid;
                $action = 'performancedetails';
            }

            break;
        case 'formdeleteinfocomps':
        case 'formdeletevenuecomps':
            $pid = dbFormDeleteComp( $params[ 'ID' ] );
            if ($pid === false) {
                $errormsg[] = 'Could not delete ticket sale item.';
                $action = 'menu';
            } else {
                $_GET[ 'perfid' ] = $pid;
                $action = 'performancedetails';
            }

            break;
        case 'formdeletebuttons':
        case 'formdeletedonations':
        case 'formdeleteconcessions':
            if ($action == 'formdeletebuttons') {
                $tblname = 'ButtonSales';
            } else if ($action == 'formdeletedonations') {
                $tblname = 'Donations';
            } else if ($action == 'formdeleteconcessions') {
                $tblname = 'ConcessionSales';
            }
            
            $wspec = dbFormDeleteWindowSale( $params[ 'ID' ], $tblname );
            if ($wspec === false) {
                $errormsg[] = 'Could not delete ' . $tblname . ' item.';
                $action = 'menu';
            } else {
                list( $vid, $day ) = $wspec;
                $_GET[ 'venueid' ] = $vid;
                $_GET[ 'day' ] = $day;
                $action = 'venuesales';
            }

            break;
        case 'formeditbuttonsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numbuttons = (int) uiGetHTTPParam( 'NumButtons' );
            $note = uiGetHTTPParam( 'Note' );

            $wspec = dbFormEditButtonSale( $params[ 'ID' ], $saletime, $numbuttons, $note );
            if ($wspec === false) {
                $errormsg[] = 'Could not edit button sale line item.';
                $action = 'menu';
            } else {
                list( $vid, $day ) = $wspec;
                $_GET[ 'venueid' ] = $vid;
                $_GET[ 'day' ] = $day;
                $action = 'venuesales';
            }

            break;
        case 'formeditconcessionsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $itemid = (int) uiGetHTTPParam( 'ItemName' );
            $numitems = (int) uiGetHTTPParam( 'NumItems' );

            $wspec = dbFormEditConcessionSale( $params[ 'ID' ], $saletime, $itemid, $numitems );
            if ($wspec === false) {
                $errormsg[] = 'Could not edit concession sale line item.';
                $action = 'menu';
            } else {
                list( $vid, $day ) = $wspec;
                $_GET[ 'venueid' ] = $vid;
                $_GET[ 'day' ] = $day;
                $action = 'venuesales';
            }

            break;
        case 'formeditdonation':
            $donationtime = uiGetHTTPTimeParam( 'DonationTime' );
            $amount = (int) ((uiGetHTTPParam( 'Amount' ) * 100) + 0.1);
            $note = uiGetHTTPParam( 'Note' );

            $wspec = dbFormEditDonation( $params[ 'ID' ], $donationtime, $amount, $note );
            if ($wspec === false) {
                $errormsg[] = 'Could not edit donation line item.';
                $action = 'menu';
            } else {
                list( $vid, $day ) = $wspec;
                $_GET[ 'venueid' ] = $vid;
                $_GET[ 'day' ] = $day;
                $action = 'venuesales';
            }

            break;
        case 'formeditpresale':
            $guestname = uiGetHTTPParam( 'GuestName' );
            $pid = (int) uiGetHTTPParam( 'Performance' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $redeemed = (uiGetHTTPParam( 'PickedUp' ) == 'Y' ? 'Y' : 'N');

            $pid = dbFormEditPresale( $pid, $params[ 'OrderNum' ], $guestname, $numtickets, $redeemed );
            if ($pid === false) {
                $errormsg[] = 'Could not edit presale line item.';
                $action = 'menu';
            } else {
                $action = 'performancedetails';
                $_GET[ 'perfid' ] = $pid;
            }    

            break;
        case 'formeditticketsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $priceoverride = (int) ((uiGetHTTPParam( 'PriceOverride' ) * 100) + 0.1);
            $toartistoverride = (int) ((uiGetHTTPParam( 'ToArtistOverride' ) * 100) + 0.1);
            $note = uiGetHTTPParam( 'Note' );

            $pid = dbFormEditTicketSale( $params[ 'ID' ], $saletime, $numtickets,
                                         $priceoverride, $toartistoverride, $note );
            if ($pid === false) {
                $errormsg[] = 'Could not create new ticket sale line item.';
                $action = 'menu';
            } else {
                $action = 'performancedetails';
                $_GET[ 'perfid' ] = $pid;
            }

            break;
        case 'formeditticketcomp':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $reason = (int) uiGetHTTPParam( 'Reason' );
            $note = uiGetHTTPParam( 'Note' );

            $pid = dbFormEditTicketComp( $params[ 'ID' ], $saletime, $numtickets, $reason, $note );
            if ($pid === false) {
                $errormsg[] = 'Could not create new ticket comp line item.';
                $action = 'menu';
            } else {
                $action = 'performancedetails';
                $_GET[ 'perfid' ] = $pid;
            }

            break;
        case 'formeditnewbuttonsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numbuttons = (int) uiGetHTTPParam( 'NumButtons' );
            $note = uiGetHTTPParam( 'Note' );

            if (!dbFormNewButtonSale( $params[ 'venueid' ], $params[ 'day' ], $saletime, $numbuttons, $note )) {
                $errormsg[] = 'Could not create new button sale line item.';
            }

            $action = 'venuesales';
            $_GET[ 'venueid' ] = $params[ 'venueid' ];
            $_GET[ 'day' ] = $params[ 'day' ];

            break;
        case 'formeditnewconcessionsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $itemid = (int) uiGetHTTPParam( 'ItemName' );
            $numitems = (int) uiGetHTTPParam( 'NumItems' );

            if (!dbFormNewConcessionSale( $params[ 'venueid' ], $params[ 'day' ], $saletime, $itemid, $numitems )) {
                $errormsg[] = 'Could not create new concession sale line item.';
            }

            $action = 'venuesales';
            $_GET[ 'venueid' ] = $params[ 'venueid' ];
            $_GET[ 'day' ] = $params[ 'day' ];

            break;
        case 'formeditnewdonation':
            $donationtime = uiGetHTTPTimeParam( 'DonationTime' );
            $amount = (int) ((uiGetHTTPParam( 'Amount' ) * 100) + 0.1);
            $note = uiGetHTTPParam( 'Note' );

            if (!dbFormNewDonation( $params[ 'venueid' ], $params[ 'day' ], $donationtime, $amount, $note )) {
                $errormsg[] = 'Could not create new donation line item.';
            }

            $action = 'venuesales';
            $_GET[ 'venueid' ] = $params[ 'venueid' ];
            $_GET[ 'day' ] = $params[ 'day' ];

            break;
        case 'formeditnewpresale':
            $ordernum = uiGetHTTPParam( 'OrderNum' );
            $guestname = uiGetHTTPParam( 'GuestName' );
            $pid = (int) uiGetHTTPParam( 'Performance' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $redeemed = (uiGetHTTPParam( 'PickedUp' ) == 'Y' ? 'Y' : 'N');

            if (!dbFormNewPresale( $pid, $ordernum, $guestname, $numtickets, $redeemed )) {
                $errormsg[] = 'Could not create new presale line item.';
            }

            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $pid;

            break;
        case 'formeditnewticketsale':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $priceoverride = (int) ((uiGetHTTPParam( 'PriceOverride' ) * 100) + 0.1);
            $toartistoverride = (int) ((uiGetHTTPParam( 'ToArtistOverride' ) * 100) + 0.1);
            $note = uiGetHTTPParam( 'Note' );

            if (!dbFormNewTicketSale( $params[ 'pID' ], $params[ 'info' ], $saletime, $numtickets,
                                      $priceoverride, $toartistoverride, $note )) {
                $errormsg[] = 'Could not create new ticket sale line item.';
            }

            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $params[ 'pID' ];

            break;
        case 'formeditnewticketcomp':
            $saletime = uiGetHTTPTimeParam( 'SaleTime' );
            $numtickets = (int) uiGetHTTPParam( 'NumTickets' );
            $reason = (int) uiGetHTTPParam( 'Reason' );
            $note = uiGetHTTPParam( 'Note' );

            if (!dbFormNewTicketComp( $params[ 'pID' ], $params[ 'info' ], $saletime, $numtickets, $reason, $note )) {
                $errormsg[] = 'Could not create new ticket comp line item.';
            }

            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $params[ 'pID' ];

            break;
        case 'formeditvenuedetails':
            $capacity = (int) uiGetHTTPParam( 'Capacity' );
            $overflow = (int) uiGetHTTPParam( 'Overflow' );
            $float = (int) ((uiGetHTTPParam( 'StartingFloat' ) * 100) + 0.1);
            $buttons = (int) uiGetHTTPParam( 'StartingButtons' );
            
            if (!dbFormEditVenueDetails( $params[ 'ID' ], $capacity, $overflow, $float, $buttons )) {
                $errormsg[] = 'Could not change all venue details.';
            }

            $action = 'venuedetails';
            $_GET[ 'venueid' ] = $params[ 'ID' ];
            
            break;
        case 'formeditshowdetails':
            $ticketprice = (int) ((uiGetHTTPParam( 'TicketPrice' ) * 100) + 0.1);
            $toartist = (int) ((uiGetHTTPParam( 'ToArtist' ) * 100) + 0.1);
            $gstnumber = uiGetHTTPParam( 'GSTNumber' );
            $wholdingtax = (uiGetHTTPParam( 'WithholdingTax' ) == 'Y' ? 'Y' : 'N');
            
            if (!dbFormEditShowDetails( $params[ 'ID' ], $ticketprice, $toartist, $gstnumber, $wholdingtax )) {
                $errormsg[] = 'Could not change all show details.';
            }
            
            $action = 'showdetails';
            $_GET[ 'showid' ] = $params[ 'ID' ];
            
            break;
        case 'formeditperformancedetails':
            $venueid = (int) uiGetHTTPParam( 'Venue' );
            $time = uiGetHTTPTimeParam( 'Time' );
            $infotix = (int) uiGetHTTPParam( 'InfoTentStartingTickets' );
            $infostop = uiGetHTTPTimeParam( 'InfoTentSalesStopAt' );
            $venuetix = (int) uiGetHTTPParam( 'VenueStartingTickets' );
            $venuestop = uiGetHTTPTimeParam( 'VenueSalesStopAt' );
            
            if (!dbFormEditPerformanceDetails( $params[ 'ID' ], $venueid, $time, $infotix, $infostop, $venuetix, $venuestop )) {
                $errormsg[] = 'Could not change all performance details.';
            }
            
            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $params[ 'ID' ];
            
            break;
        case 'formmakefloat':
            $amount = (int) uiGetHTTPParam( 'amount' );
            $day = uiGetHTTPParam( 'floatday' );
            $venues = array();
            
            foreach (dbGetVenueList() as $venue) {
                if (uiGetHTTPParam( $venue[ 'ID' ] ) != NULL) {
                    $venues[] = $venue[ 'ID' ];
                }
            }
            if (!dbFormMakeFloats( $amount, $day, $venues )) {
                $errormsg[] = 'Unable to set all floats';
            }
            
            $action = 'float';
            
            break;
        case 'formmakepayout':
            $perfid = (int) uiGetHTTPParam( 'perfid' );
            $amount = (int) ((uiGetHTTPParam( 'amount' ) * 100) + 0.1);

            if (!dbMakeArtistPayout( $perfid, $amount )) {
                $errormsg[] = 'Unable to make artist payout.';
            }

            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $perfid;

            break;
        case 'formupdatepayout':
            $perfid = (int) uiGetHTTPParam( 'perfid' );
            $txnid = (int) uiGetHTTPParam( 'payouttxnid' );
            $amount = (int) ((uiGetHTTPParam( 'amount' ) * 100) + 0.1);
            
            if (!dbUpdateArtistPayout( $txnid, $amount )) {
                $errormsg[] = 'Unable to update artist payout.';
            }
            
            $action = 'performancedetails';
            $_GET[ 'perfid' ] = $perfid;

            break;
        case 'formcreatefestival':
            $startdate = uiGetHTTPParam( 'startdate' );
            $enddate   = uiGetHTTPParam( 'enddate' );
            
            if (!dbFormCreateNewFestival( getCPopFestivalData( $startdate, $enddate ), $startdate, $enddate )) {
                $errormsg[] = 'Unable to create festival.';
            }
            
            $action = 'festivalcreated';
            
            break;
        default:
            echo 'params: ';
            print_r( $params );
            echo "<BR />\nPOST: ";
            print_r( $_POST );

            $errormsg[] = $action . '[' . $fields . '] not yet implemented.';
            $action = 'menu';
            break;
    }

    return array( $action, $errormsg );
}

?>
