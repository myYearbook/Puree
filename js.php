<?php

/**
 * Merges the given javascript files and outputs to the browser
 *
 * @author Dallas Gutauckis <dgutauckis@myyearbook.com>
 * @since 2008-10-26 23:07:54 EST
 */

function handleJS( $tmpFiles )
{
  $files = array();
  $fileContents = '';
  foreach ( $tmpFiles as &$file )
  {
    if ( strstr( '..', $file ) !== false )
    {
      continue;
    }

    $filePath = realpath( PATH_PREFIX_JS . $file . '.js' );    
    $fileContent = file_get_contents( $filePath );
    
    if ( $fileContent )
    {
      $content .= $fileContent . ";\n";
    }
  }
  
  $fileContents = $content;

  header('Pragma: cache');
  header('Cache-control: public');
  header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + intval(30*1440*60)));

  header('Content-type: text/javascript');
  echo $fileContents;
}
