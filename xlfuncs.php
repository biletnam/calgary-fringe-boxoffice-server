<?php

require_once( dirname( __FILE__ ) . '/PHPExcel/Classes/PHPExcel.php' );

$datecolumns = array(
    '2011-07-28' => 'C',
    '2011-07-29' => 'D',
    '2011-07-30' => 'E',
    '2011-07-31' => 'F',
    '2011-08-01' => 'G',
    '2011-08-02' => 'H',
    '2011-08-03' => 'I',
    '2011-08-04' => 'J',
    '2011-08-05' => 'K',
    '2011-08-06' => 'L'
);

function xlPopulateData( $report, $reportdata ) {
    xlPopulateSummarySheet( $report->getSheet( 0 ), $reportdata[ 'Summary' ] );

    $i = 1;
    foreach ($reportdata[ 'Shows' ] as $showdata) {
        xlPopulateShowSheet( $report->getSheet( $i ), $showdata );

        $i = $i + 1;
    }

    $report->setActiveSheetIndex( 0 );
}

function xlPopulateSummarySheet( $sheet, $summarydata ) {
    global $datecolumns;
    
    $sheet->setCellValue( 'B29', ($summarydata[ 'PresoldFringers' ] / 100) );
    $sheet->setCellValue( 'B30', ($summarydata[ 'PresoldBuddys' ] / 100) );
    $sheet->setCellValue( 'B31', ($summarydata[ 'PresoldBingers' ] / 100) );
    
    foreach (array_keys( $summarydata[ 'Daily' ] ) as $day) {
        $col = decr( $datecolumns[ $day ] );
        $sheet->setCellValue( $col . '28', ($summarydata[ 'Daily' ][ $day ][ 'Buttons' ] / 100) );
        $sheet->setCellValue( $col . '29', ($summarydata[ 'Daily' ][ $day ][ 'Fringers' ] / 100) );
        $sheet->setCellValue( $col . '30', ($summarydata[ 'Daily' ][ $day ][ 'Buddys' ] / 100) );
        $sheet->setCellValue( $col . '31', ($summarydata[ 'Daily' ][ $day ][ 'Bingers' ] / 100) );
    }
    
    $sheet->setSelectedCell();
}

function xlPopulateShowSheet( $sheet, $showdata ) {
    global $datecolumns;

    $surcharge = $showdata[ 'TicketPrice' ] - $showdata[ 'ToArtist' ];

    $sheet->setCellValue( 'B3', $showdata[ 'ArtistName' ] );
    $sheet->setCellValue( 'B4', $showdata[ 'Title' ] );
    $sheet->setCellValue( 'B5', $showdata[ 'VenueName' ] );
    $sheet->setCellValue( 'B6', number_format( $showdata[ 'ToArtist' ] / 100, 2 ) );
    $sheet->setCellValue( 'B7', number_format( $surcharge / 100, 2 ) );

    foreach ($showdata[ 'Performances' ] as $performance) {
        $pdate = $performance[ 'PerformanceDate' ];
        $seniors = $performance[ 'Info1000' ] + $performance[ 'Venue1000' ];
        $freebies = $performance[ 'OnlinePromo' ] + $performance[ 'InfoPromo' ] + $performance[ 'VenuePromo' ];
        $artists = $performance[ 'InfoArtist' ] + $performance[ 'VenueArtist' ];

        $col = $datecolumns[ $pdate ];

        $sheet->getColumnDimension( $col )->setWidth( 16.5 );
        $sheet->setCellValue( $col . '10', $performance[ 'OnlineSuper' ] );
        $sheet->setCellValue( $col . '13', $performance[ 'OnlineIndiv' ] );
        $sheet->setCellValue( $col . '14', $performance[ 'InfoSuper' ] );
        $sheet->setCellValue( $col . '15', $performance[ 'InfoNormal' ] );
        $sheet->setCellValue( $col . '16', $seniors );
        $sheet->setCellValue( $col . '17', $performance[ 'VenueNormal' ] );
        $sheet->setCellValue( $col . '18', $performance[ 'VenueSuper' ] );
        $sheet->setCellValue( $col . '19', $freebies );
        $sheet->setCellValue( $col . '20', $artists );

        $sheet->setCellValue( $col . '34', ($performance[ 'Payout' ] / 100) );
    }
    
    if (isset( $showdata[ 'GSTNumber' ] )) {
        $sheet->setCellValue( 'A39', 'GST #' . $showdata[ 'GSTNumber' ] );
        if ($showdata[ 'WithholdingTax' ] == 'Y') {
            $sheet->setCellValue( 'B40', '=(B38-(B38/1.15))' );
        } else {
            $sheet->setCellValue( 'A40', '' );
        }
    } else {
        if ($showdata[ 'WithholdingTax' ] == 'Y') {
            $sheet->setCellValue( 'B39', '=(B38-(B38/1.2))*0.25' );
            $sheet->setCellValue( 'B39', '=(B38-(B38/1.2))*0.75' );
        } else {
            $sheet->setCellValue( 'B39', '=(B38-(B38/1.05))' );
            $sheet->setCellValue( 'A40', '' );
        }
    }

    $sheet->setSelectedCell();
}

function xlSanitizeTitle( $title ) {
    $title = substr( $title, 0, 31 );
    $title = str_replace( array( ':', '\\', '/', '?', '*', '[', ']' ), '_', $title );

    return $title;
}

function xlSetFontBold( $sheet, $cellid, $isbold = true ) {
    xlSetFont( $sheet, $cellid, array( 'bold' => $isbold ) );
}

function xlSetFontItalic( $sheet, $cellid, $isitalic = true ) {
    xlSetFont( $sheet, $cellid, array( 'italic' => $isitalic ) );
}

function xlSetFontSize( $sheet, $cellid, $size ) {
    xlSetFont( $sheet, $cellid, array( 'size' => $size ) );
}

function xlSetFontName( $sheet, $cellid, $fontname ) {
    xlSetFont( $sheet, $cellid, array( 'name' => $fontname ) );
}

function xlSetFont( $sheet, $cellid, $attribs ) {
    $font = $sheet->getStyle( $cellid )->getFont();

    if (isset( $attribs[ 'bold' ] )) {
        $font->setBold( $attribs[ 'bold' ] );
    }
    if (isset( $attribs[ 'size' ] )) {
        $font->setSize( $attribs[ 'size' ] );
    }
    if (isset( $attribs[ 'name' ] )) {
        $font->setName( $attribs[ 'name' ] );
    }
    if (isset( $attribs[ 'italic' ] )) {
        $font->setItalic( $attribs[ 'italic' ] );
    }
}

function xlSetHAlign( $sheet, $cellid, $align = '' ) {
    switch ($align) {
        case 'left':
            $align = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
            break;
        case 'right':
            $align = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
            break;
        case 'center':
            $align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
            break;
        default:
            $align = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
            break;
    }

    $sheet->getStyle( $cellid )->getAlignment()->setHorizontal( $align );
}

function xlSetBGColor( $sheet, $cellid, $color ) {
    $fill = $sheet->getStyle( $cellid )->getFill();
    $fill->setFillType( PHPExcel_Style_Fill::FILL_SOLID );
    $fill->getStartColor()->setARGB( 'FF' . $color );
}

function xlSetTopBorder( $sheet, $cellid, $style ) {
    xlSetBorder( $sheet->getStyle( $cellid )->getBorders()->getTop(), $style );
}

function xlSetBottomBorder( $sheet, $cellid, $style ) {
    xlSetBorder( $sheet->getStyle( $cellid )->getBorders()->getBottom(), $style );
}

function xlSetLeftBorder( $sheet, $cellid, $style ) {
    xlSetBorder( $sheet->getStyle( $cellid )->getBorders()->getLeft(), $style );
}

function xlSetRightBorder( $sheet, $cellid, $style ) {
    xlSetBorder( $sheet->getStyle( $cellid )->getBorders()->getRight(), $style );
}

function xlSetBorder( $border, $style ) {
    if (isset( $style[ 'style' ] )) {
        $border->setBorderStyle( $style[ 'style' ] );
    }
    if (isset( $style[ 'color' ] )) {
        $border->getColor()->setARGB( 'FF' . $style[ 'color' ] );
    }
}

function xlSetCellValues( $sheet, $cellid, $values, $vertical = false ) {
    if (! is_array( $values )) {
        $values = array( $values );
    }

    $cell = $sheet->getCell( $cellid );
    $row = $cell->getRow();
    $col = $cell->getColumn();

    foreach ($values as $value) {
        $sheet->setCellValueByColumnAndRow( $col, $row, $value );
        if ($vertical) {
            $row = $row + 1;
        } else {
            $col = $col + 1;
        }
    }
}

function xlOutputReport( $report, $filename = NULL ) {
    if ($filename == NULL) {
        $filename = date( 'Y-m-d' ) . ' Final Box Office Reports 2011.xls';
    }
    
    header( 'Content-Type: application/vnd.ms-excel' );
    header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
    header( 'Cache-Control: max-age=0' );
    
    $writer = PHPExcel_IOFactory::createWriter( $report, 'Excel5' );
    $writer->setPreCalculateFormulas( false );
    $writer->save( 'php://output' );
}

function xlReadReportTemplate( $filename = NULL ) {
    if ($filename == NULL) {
        $filename = 'Final_Box_Office_Reports_2011_Template.xls';
    }
    
    return PHPExcel_IOFactory::load( $filename );
}

function xlPrintSheetStats( $report ) {
    $output = '';
    
    $n = $report->getSheetCount();
    for ($i = 0; $i < $n; $i++) {
        $output .= $i . ': [' . $report->getSheet( $i )->getTitle() . ']<BR />' . "\n";
    }
    $output .= $n . ': [' . $report->getSheet( $n )->getTitle() . ']<BR />' . "\n";
    
    return $output;
}

function decr( $char ) {
    return chr( ord( $char ) - 1 );
}

?>
