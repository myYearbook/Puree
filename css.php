<?php

/**
 * Packs and merges the CSS files requested and outputs to browser
 *
 * @author Dallas Gutauckis <dallas@myyearbook.com>
 * @since 2008-10-26 23:05:58 EST
 */

function handleCSS( $files )
{
  $fileContents = '';
  header( 'Content-type: text/css' );

  foreach ( $files as &$file )
  {
    $filePath = realpath( PATH_PREFIX_CSS . $file . '.css' );
    addCSSFile( $filePath, $file );
  }
}

function addCSSFile( $filePath, $simpleName )
{
  $sizeBefore = 0;
  $sizeAfter = 0;
  
  $fileContents = '';
  $data = compressCSS( $filePath, $sizeBefore, $sizeAfter );
  // Uncomment the following to see your filesize difference
  //$fileContents .= '/*' . $simpleName . ':' . $sizeBefore . ':' . $sizeAfter . '*/';
  $fileContents .= $data . "\n";
  
  echo $fileContents;
}

function compressCSS( $filePath, &$sizeBefore, &$sizeAfter )
{
  $input = file_get_contents( $filePath );
  $pathInfo = pathinfo( $filePath );
  
  $sizeBefore = strlen( $input );
  $done = preg_replace( '#\s*/\*.*?\*/\s*#m', '', $input ); // Remove comments
  $done = preg_replace( '/\s*\{\s*\n?\s*/', '{', $done ); // Compress newlines after opening braces and strip spaces before and after brace
  $done = str_replace( "\t", ' ', $done ); // Replace tabs with spaces
  $done = preg_replace( '/\s*\n*\}\s*/', '}', $done ); // Compress newlines before closing braces and strip spaces before and after brace
  $done = preg_replace( '/\s*:\s*/', '$1:', $done ); // Strip spaces after property names
  $done = preg_replace( '/;\n?[ ]?/', ';', $done ); // Strip newlines after property values
  $done = preg_replace( '/[ ]{2,}/', ' ', $done ); // Replace needless extra spaces
  $done = preg_replace( '/#([a-fA-F0-9])(?=\1).([a-fA-F0-9])(?=\2).([a-fA-F0-9])(?=\3)./', '#$1$2$3', $done ); // Change long hex to short-hex in color codes (#ffffff to #fff, #ffee00 to #fe0, but no changes to #fe0000)
  $done = str_replace( "\n", "", $done ); // Remove newlines
  $done = str_replace( "\r", "", $done ); // Remove newlines
  $done = preg_replace( '/;[ ]+/', ';', $done ); // Strip whitespace after property values
  $done = preg_replace( '/,[ \n]?/', ',', $done ); // Strip whitespace inside of a multiple selector selector (between selectors)
  
  // Find @import (sadface)
  preg_match_all( '/@import(.[^;]+);/', $done, $matches );
  // We want what is inside of the parenthesis in the pattern... let's clean it up.
  foreach ( $matches[1] as $match )
  {
    $match = trim( $match );
    $match = trim( $match, "'" );
    // Remove query string, if any
    list( $match, $queryString ) = explode( '?', $match );
    addCSSFile( $pathInfo['dirname'] . '/' . $match, '@' . $match );
  }
  
  // And... now, remove imports...
  $done = preg_replace( '/@import.[^;]+;/', '', $done );
  
  // Handle URL declarations properly, since we're in puree and it thinks it should be in assets!
  preg_match_all( '/url\((.[^\)]+)\)/', $done, $matches );  
  foreach ( $matches[1] as $match )
  {
    $new = "url('" . ASSETS_URL . ltrim( trim( $match, "'" ), '/' ) . "')";
    $done = str_replace( 'url(' . $match . ')', $new, $done );
  }
  
  $done = str_replace( ':url(/', ':url(' . ASSETS_URL, $done );
  $sizeAfter = strlen( $done );
  
  return $done;
}
