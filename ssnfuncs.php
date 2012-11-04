<?php

$sessOneDay = 60 * 60 * 24;

$p = session_get_cookie_params();

session_name( 'fringe' );
session_set_cookie_params( 0, '/', 'boxoffice.calgaryfringe.ca' );
session_save_path( '/home/virtual/site42/fst/var/www/boxoffice/sessions' );
ini_set( 'session.gc_maxlifetime', $sessOneDay );
ini_set( 'session.gc_probability', 1 );
ini_set( 'session.use_cookies', 1 );
session_start();

if (isset( $_SESSION[ 'username' ] )) {
    sessSetTimeout();
}


// ------------------------------------------------------------------------------------------------
//  Session Management Functions
// ------------------------------------------------------------------------------------------------

function sessGetUser( $username ) {
    if ($username == NULL) {
        return sessGetLogin();
    } else {
        return $username;
    }
}

function sessSetTimeout() {
    setcookie( session_name(), session_id(), 0, '/', 'boxoffice.calgaryfringe.ca' ) . "\n";
}

function sessLogin( $username, $userhash ) {
    $_SESSION[ 'username' ] = $username;
    $_SESSION[ 'userhash' ] = $userhash;
}

function sessLogout() {
    global $sessOneweek;

    unset( $_SESSION[ 'username' ] );
    unset( $_SESSION[ 'userhash' ] );
    sessSetTimeout( time() - $sessOneweek );
}

function sessGetLogin() {
    if (isset( $_SESSION[ 'username' ] )) {
        return dbSafeUserPass( $_SESSION[ 'username' ] );
    }

    return '';
}

function sessGetHash() {
    if (isset( $_SESSION[ 'userhash' ] )) {
        return dbSafeUserPass( $_SESSION[ 'userhash' ] );
    }

    return '';
}

function sessCurrentPost( $postid = NULL ) {
    $oldpostid = (isset( $_SESSION[ 'postid' ] ) ? $_SESSION[ 'postid' ] : 0);

    if ($postid != NULL) {
        $_SESSION[ 'postid' ] = $postid;
    }

    return $oldpostid;
}

?>