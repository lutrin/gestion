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
          "k"    => self::getK( $subpath ),
          "name" => $file
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
    $path = self::getPath( $k );

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
      $mimetype = finfo_file( $type, $subpath );
      $item = array(
        "k"        => self::getK( $subpath ),
        "name"     => $file,
        "mimetype" => finfo_file( $type, $subpath ),
        "encoding" => finfo_file( $encoding, $subpath ),
        "size"     => filesize( $subpath )
      );
      $list[] = $item;
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
  public static function getName( $path ) {
    $pathList = explode( "/", $path );
    return array_pop( $pathList );
  }

  /****************************************************************************/
  public static function exists( $k, $name = "" ) {
    global $PUBLICPATH;
    return file_exists( self::getPath( $k, $name ) );
  }

  /****************************************************************************/
  public static function isPermitted( $k, $name = "" ) {
    return fileperms( self::getPath( $k, $name ) ) == 16895;
  }

  /****************************************************************************/
  public static function mkdir( $k, $name = "" ) {
    return mkdir( self::getPath( $k, $name ), 0777 );
  }

  /****************************************************************************/
  public static function rename( $k, $name, $oldname ) {
    return rename( self::getPath( $k, $name ), self::getPath( $k, $oldname ) );
  }

  /****************************************************************************/
  public static function getParent( $k ) {
    $path = self::getPath( $k );
    $pathList = explode( "/", $path );
    array_pop( $pathList );
    return join( "/", $pathList );
  }

  /****************************************************************************/
  protected static function getK( $subpath ) {
    global $PUBLICPATH;
    return db_Path::getK( str_replace( $PUBLICPATH . "/", "", $subpath ) );
  }

  /****************************************************************************/
  protected static function getPath( $k, $name = 0 ) {
    global $PUBLICPATH;
    return "$PUBLICPATH/" . db_Path::getPath( $k ) . ( $name? "/$name": "" );
  }
}
