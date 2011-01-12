<?php

/**
 * Handle delegation of tasks
 *
 * @author Dallas Gutauckis <dallas@myyearbook.com>
 * @since 2008-11-13 23:40:51
 */

/*
 * Configuration parameter that defines the root of your assets URL.
 * This is used for replacing relative URLs with the full URL for the
 * sake of exactness and potential drop-in capabilities
 */
define( 'ASSETS_URL', 'http://assets.mydomain.com/' );

// No configuration beyond this point

$validTypes = array( 'css', 'js' );

list( $type, $params ) = explode( '/', ltrim( $_SERVER['REQUEST_URI'], '/' ), 2 );
$type = in_array( $type, $validTypes ) ? ( $type ) : ( 'error' );
$params = explode( ';', $params );

$files = array();
foreach ( $params as $param )
{
  list( $file, $nada ) = explode( ':', $param, 2 );
  $files[] = $file;
}

require( $type . '.php' );
call_user_func( 'handle' . strtoupper( $type ), $files );
