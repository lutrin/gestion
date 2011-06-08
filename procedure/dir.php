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
      $mimetype = finfo_file( $type, $subpath );
      $item = array(
        "k"        => db_Path::getK( str_replace( $PUBLICPATH . "/", "", $subpath ) ),
        "name"     => $file,
        "mimetype" => finfo_file( $type, $subpath ),
        "encoding" => finfo_file( $encoding, $subpath ),
        "size"     => filesize( $subpath )
      );
      $list[] = $item;
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
}
