<?php
class Dir {

  protected static $ignore = array( ".", "..", ".git" );

  /****************************************************************************/
  public static function getTree( $path = "", $k = 0 ) {
    global $PUBLICPATH;

    # path
    $path = $path? $path: $PUBLICPATH;

    # open handle
    if( !$handle = opendir( $path ) ) {
      return false;
    }

    # get list
    $list = array();
    while( false !== ( $file = readdir( $handle ) ) ) {

      # ignore
      if( in_array( $file, self::$ignore ) ) {
        continue;
      }

      # get sub path
      $subpath = "$path/$file";
      if( is_dir( $subpath ) ) {
        $folder = array(
          "k"    => str_replace( $PUBLICPATH . "/", "", $subpath ),#self::encrypt( $subpath ),
          "name" => $file,
          "path" => str_replace( $PUBLICPATH . "/", "", $path )
        );

        # get child list
        if( $childList = self::getTree( $subpath, $k++ ) ) {
          $folder["childList"] = $childList;
        }
        $list[] = $folder;
      }
    }

    # close handle
    closedir( $handle );

    # sort
    usort( $list, function( $a, $b ) {
      if( $a["name"] == $b["name"] ) {
        return 0;
      }
      return ( $a["name"] < $b["name"] )? -1: 1;
    } );
    return $list;
  }

  /****************************************************************************/
  public static function getExplore( $k ) {
    global $PUBLICPATH;
    $path = "$PUBLICPATH/$k";#self::decrypt( $k );

    # open handle
    if( !$handle = opendir( $path ) ) {
      return false;
    }

    # get list
    $list = array();
    $type = finfo_open( FILEINFO_MIME_TYPE );
    $encoding = finfo_open( FILEINFO_MIME_ENCODING );
    while( false !== ( $file = readdir( $handle ) ) ) {

      # ignore
      if( in_array( $file, self::$ignore ) ) {
        continue;
      }

      # get sub path
      $subpath = "$path/$file";
      #if( is_dir( $subpath ) ) {
        $item = array(
          "k"        => str_replace( $PUBLICPATH . "/", "", $subpath ),#self::encrypt( $subpath ),
          "name"     => $file,
          "type"     => finfo_file( $type, $subpath ),
          "encoding" => finfo_file( $encoding, $subpath ),
          "size"     => self::getHumanFileSize( filesize( $subpath ) )
        );
        $list[] = $item;
      #}
    }

    # close handle
    closedir( $handle );

    return $list;
  }

  /****************************************************************************/
  public static function exists( $path ) {
    return file_exists( $path );
  }

  /****************************************************************************/
  public static function isPermitted( $path ) {
    return fileperms( $path ) == 16895;
  }

  /****************************************************************************/
  public static function mkdir( $path ) {
    return mkdir( $path, 0777 );
  }

  /****************************************************************************/
  protected static function encrypt( $data, $key = "folder" ) {
    $result = '';
    
    for( $i = 0; $i < strlen( $data ); $i++ ) {
      $char    = substr( $data, $i, 1 );
      $keyChar = substr( $key, ( $i % strlen( $key ) ) - 1, 1 );
      $char    = chr( ord( $char ) + ord( $keyChar ) );
      $result  .= $char;
    }
    return self::encode_base64( $result ); 
  }

  /****************************************************************************/
  protected static function decrypt( $data, $key = "folder" ) {
    $result = '';
    $data   = self::decode_base64( $data );
    for( $i = 0; $i <= strlen( $data ); $i++ ) {
      $char    = substr( $data, $i, 1 );
      $keyChar = substr( $key, ( $i % strlen( $key ) ) - 1, 1 );
      $char    = chr( ord( $char ) - ord( $keyChar ) );
      $result .= $char;
    }
    return $result;
  }

  /****************************************************************************/
  protected static function encode_base64( $data ) {
    $base64 = base64_encode( $data );
    return substr( strtr( $base64, '+/', '-_' ), 0, -2 );
  }

  /****************************************************************************/
  protected static function decode_base64( $data ) {
    $base64 = strtr( $data, '-_', '+/' );
    return base64_decode( $base64.'==' );
  }

  /****************************************************************************/
  protected static function getHumanFileSize( $size ) {
    if( is_numeric( $size ) ) {
      $decr = 1024;
      $step = 0;
      $prefix = array( 'Octet', 'Ko', 'Mo', 'Go', 'To', 'Po' );
      while( ( $size / $decr ) > 0.9 ) {
        $size = $size / $decr;
        $step++;
      }
      return round( $size, 1 ) . '&nbsp;' . $prefix[$step];
    } else { 
      return '-';
    }
  }
}
