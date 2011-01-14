<?php

/**
 * Handle delegation of tasks for Puree requests
 *
 * @author Dallas Gutauckis <dallas@myyearbook.com>
 * @since 2008-11-13 23:40:51
 */

/*
 * Configuration parameter that defines the root of your assets URL.
 * This is used for replacing relative URLs with the full URL for the
 * sake of exactness and potential drop-in capabilities
 */
define( 'ASSETS_URL', 'http://assets.devk.it/' );

/*
 * The path that Puree has been installed to
 */
define( 'INSTALL_PATH', '/proj/puree/' );

/*
 * Path prefixes for each content type
 */
define( 'PATH_PREFIX_CSS', '../puree-assets/CSS/' );
define( 'PATH_PREFIX_JS', '../puree-assets/JS/' );

/*
 * Add body to this function to log errors if desired
 *
 * @param string $error Error message
 * @return void
 */
function logError( $error )
{
}

// No configuration beyond this point

header( 'Pragma: cache' );
header( 'Cache-control: public' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 2592000 ) );

$validTypes = array( 'css', 'js' );

// Remove the install path from the request uri
$uri = ltrim( $_SERVER['REQUEST_URI'], INSTALL_PATH );

if ( ! $uri )
{
  // Invalid uri
  logError( 'Invalid uri requested' );
  exit;
}

list( $type, $params ) = explode( '/', $uri, 2 );

if ( ! in_array( $type, $validTypes ) )
{
  // Invalid type
  logError( 'Invalid type ("' . $type . '") requested' );
  exit;
}

$params = explode( ';', $params );

$files = array();
// Iterate over the files+cachebuster requested and remove the cachebuster and build the file list
foreach ( $params as $file )
{
  if ( false !== strpos( $file, ':' ) )
  {
    // Strip the cachebuster off of the end of the file requested
    list( $file, $ignore ) = explode( ':', $file, 2 );
    // Add the file name to the file array
  }

  if ( false !== strpos( $file, '..' ) )
  {
    continue;
  }

  $files[] = $file;
}

// Bring in the code related to handling this type
require( $type . '.php' );
// Call our type handler function
call_user_func( 'handle' . strtoupper( $type ), $files );