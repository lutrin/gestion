<?php
class Dir {

  protected static $ignore = array( ".", "..", ".git" );

  /****************************************************************************/
  public static function getTree( $path = "" ) {
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
          "k"    => db_Path::getK( str_replace( $PUBLICPATH . "/", "", $subpath ) ),
          "name" => $file,
          "path" => str_replace( $PUBLICPATH . "/", "", $path )
        );

        # get child list
        if( $childList = self::getTree( $subpath ) ) {
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
    $path = "$PUBLICPATH/" . db_Path::getPath( $k );

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
          "k"        => db_Path::getK( str_replace( $PUBLICPATH . "/", "", $subpath ) ),
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
  protected static function getType( $file, $type ) {
    $typeList = array(
      "folder" => array( "directory" ),
      "gif"    => array( "image/gif" ),
      "jpg"    => array( "image/jpeg" ),
      "png"    => array( "image/jpeg" ),
      "svg"    => array( "image/svg+xml" ),
      "html"   => array( "text/html" ),
      "php"    => array( "text/x-php" ),
      "text"   => array( "text/x-c++", "text/plain", "text/x-c" ),
      "xml"    => array( "application/xml" )
    );
    $textTypeList = array(
      "js"  => "javascript",
      "sql" => "sql",
      "css" => "css"
    );
    $actionList = array(
      "explore" => array( "folder" ),
      "insert"  => array( "folder" ),
      "view"    => array( "gif", "svg", "jpg", "png" ),
      "edit"    => array( "text", "html", "php", "svg", "xml", "javascript", "sql", "css" )
    );

    # default
    $class = "file";

    # get class
    foreach( $typeList as $key => $item ) {
      if( in_array( $type, $item ) ) {
        $class = $key;
        break;
      }
    }

    # get 
    if( $type == "text" ) {
      $decomposed = explode( ".", $file );
      $last = $decomposed[( count( $decomposed ) - 1 )];
      $class = isset( $textTypeList[$last] )? $textTypeList[$last]: $class;
    }
    $typeObject = array(
      "class" => $class
    );
    $actions = array();
    foreach( $actionList as $key => $action ) {
      if( in_array( $class, $action ) ) {
        $action[] = $key;
      }
    }
    if( $actions ) {
      $typeObject["action"] = $actions;
    }
    return $typeObject;
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
