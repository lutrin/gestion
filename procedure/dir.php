<?php
class Dir {

  protected static $ignore = array( ".", "..", ".git" );

  /****************************************************************************/
  public static function getTree( $path ) {

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
