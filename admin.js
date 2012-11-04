function readIntFromElement( elementID ) {
    var n = parseInt( document.getElementById( elementID ).value );
    if (isNaN( n )) {
        n = 0;
    }
    
    return n;
}

function readDollarFromElement( elementID ) {
    var n = parseFloat( document.getElementById( elementID ).value.substring( 1 ) );
    if (isNaN( n )) {
        n = 0.0;
    }
    
    return (n * 100);
}

function calculateFloat() {
    var hundreds = 10000 * readIntFromElement( 'calc_hundreds' );
    var fifties = 5000 * readIntFromElement( 'calc_fifties' );
    var twenties = 2000 * readIntFromElement( 'calc_twenties' );
    var tens = 1000 * readIntFromElement( 'calc_tens' );
    var fives = 500 * readIntFromElement( 'calc_fives' );
    var toonies = 200 * readIntFromElement( 'calc_toonies' );
    var loonies = 100 * readIntFromElement( 'calc_loonies' );
    var quarters = 25 * readIntFromElement( 'calc_quarters' );

    var total = (hundreds + fifties + twenties + tens + fives + toonies + loonies + quarters) / 100;
    document.getElementById( 'calc_total' ).value = '$' + total.toFixed( 2 );
}

function submitDeleteForm( formname, formaction, formfields ) {
    document.getElementById( formname + '_action' ).value = formaction;
    document.getElementById( formname + '_fields' ).value = formfields;

    var oktosubmit;

    oktosubmit = confirm( 'REALLY delete record from ' + formname + ' where ' + formfields + '?\n\n' +
                          'If you click \'OK,\' this action cannot be undone!' );

    if (oktosubmit) {
        document.forms[ formname ].submit();
    }
}

function submitEditForm( formname, formaction, formfields ) {
    document.getElementById( formname + '_action' ).value = formaction;
    document.getElementById( formname + '_fields' ).value = formfields;

    document.forms[ formname ].submit();
}

function submitFloatForm() {
    var amt = readDollarFromElement( 'calc_total' );
    
    document.getElementById( 'form_amount' ).value = amt;
    
    document.forms[ 'floatform' ].submit();
}

function submitNewFestivalForm() {
    var startdate = document.getElementById( 'startdate' ).value;
    var enddate   = document.getElementById( 'enddate' ).value;
    
    if (document.getElementById( 'verifycreation' ).checked) {
        document.forms[ 'newfestivalform' ].submit();
    } else {
        alert( 'Please verify you want to reset all data between ' + startdate + ' and ' + enddate +
               ', and check the checkbox to confirm.' );
    }
}
