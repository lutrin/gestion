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
        } else {
          $folder["indAction"] = "delete";
        }
        $list[] = $folder;
      }
    }

    # close handle
    closedir( $handle );

    # sort
    usort( $list, function( $a, $b ) {
      $aName = strtolower( $a["name"] );
      $bName = strtolower( $b["name"] );
      if( $aName == $bName ) {
        return 0;
      }
      return ( $aName < $bName )? -1: 1;
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
      $aName = strtolower( $a["name"] );
      $bName = strtolower( $b["name"] );
      if( $aName == $bName && $a["mimetype"] == $b["mimetype"] ) {
        return 0;
      }
      if( $a["mimetype"] == "directory" && $b["mimetype"] != "directory" ) {
        return -1;
      }
      if( $b["mimetype"] == "directory" && $a["mimetype"] != "directory" ) {
        return 1;
      }
      return ( $aName < $bName )? -1: 1;
    } );
    return $list;
  }

  /****************************************************************************/
  public static function getName( $path ) {
    $pathList = explode( "/", $path );
    return array_pop( $pathList );
  }

  /****************************************************************************/
  public static function getInfo( $k ) {
    global $PUBLICPATH;
    $path = self::getPath( $k );
    $type = finfo_open( FILEINFO_MIME_TYPE );
    $encoding = finfo_open( FILEINFO_MIME_ENCODING );
    return array(
      "k"        => $k,
      "name"     => self::getName( $path ),
      "mimetype" => finfo_file( $type, $path ),
      "encoding" => finfo_file( $encoding, $path ),
      "size"     => filesize( $path ),
      "path"     => str_replace( $PUBLICPATH . "/", "", $path )
    );
  }

  /****************************************************************************/
  public static function getNewPath( $parentPath, $newName ) {
    $pathList = explode( "/", $parentPath );
    $pathList[] = $newName;
    return join( "/", $pathList );
  }

  /****************************************************************************/
  public static function exists( $k, $name = "" ) {
    global $PUBLICPATH;
    return file_exists( self::getPath( $k, $name ) );
  }

  /****************************************************************************/
  public static function save( $k, $content ) {
    $file = fopen( self::getPath( $k ), 'w+' );
    fwrite( $file, $content );
    fclose( $file );
    return true;
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
  public static function rename( $oldK, $newK ) {
    return rename( self::getPath( $oldK ), self::getPath( $newK ) );
  }

  /***************************************************************************/
  public static function delete( $k ) {
    $path = self::getPath( $k );
    if( is_dir( $path ) ) {
      return rmdir( $path );
    } else {
      return unlink( $path );
    }
  }

  /****************************************************************************/
  public static function getParent( $k ) {
    $path = self::getPath( $k );
    $pathList = explode( "/", $path );
    array_pop( $pathList );
    return join( "/", $pathList );
  }

  /****************************************************************************/
  public static function getParentK( $k ) {
    return self::getK( self::getParent( $k ) );
  }

  /****************************************************************************/
  public static function getContent( $path ) {
    global $PUBLICPATH;
    return file_get_contents( $PUBLICPATH . "/" . $path, FILE_USE_INCLUDE_PATH );
  }

  /****************************************************************************/
  public static function calculateMaxFileSize( $ini = "file" ) {
    $iniList = array(
      "file" => "upload_max_filesize",
      "post" => "post_max_size"
    );
    $uploadMaxFilesize = ini_get( $iniList[$ini] );
    $value = intval( $uploadMaxFilesize );
    $size = str_replace( ( string ) $value, "", $uploadMaxFilesize );
    $multiple = 0;
    if( $size == "K" ) {
      $multiple = 1;
    } elseif( $size == "M" ) {
      $multiple = 2;
    } elseif( $size == "G" ) {
      $multiple = 3;
    }
    return $value * pow( 1024, $multiple );
  }

  /****************************************************************************/
  public static function putFile( $targetK, $filename ) {
    $path = self::getPath( $targetK ) . "/";
    $file = $path . $filename;

    # already exists
    while( file_exists( $file ) ) {
      $exploded = exploded( ".", $filename );

      # get extension
      $ext = false;
      if( count( $exploded ) > 1 ) {
        $ext = array_pop( $exploded );
      }
      $tmp = join( ".", $exploded );

      # get index
      $exploded = exploded( "-", $tmp );
      $index = 1;
      if( count( $exploded ) > 1 ) {
        $index = array_pop( $exploded );
        if( !is_numeric( $index ) ) {
          $index .= "-1";
        } else {
          $index = (int)$index + 1;
        }
      }
      array_push( $exploded, $index );
      $file = join( "-", $exploded );
    }

    # write file
    $f = fopen( $file, 'w+' );
    fwrite( $f, file_get_contents( "php://input" ) );
    fclose( $f );
    return true;
  }

  /****************************************************************************/
  protected static function getK( $subpath ) {
    global $PUBLICPATH;
    if( $subpath == $PUBLICPATH ) {
      return 0;
    }
    return db_Path::getK( str_replace( $PUBLICPATH . "/", "", $subpath ) );
  }

  /****************************************************************************/
  protected static function getPath( $k, $name = 0 ) {
    global $PUBLICPATH;
    return "$PUBLICPATH/" . db_Path::getPath( $k ) . ( $name? "/$name": "" );
  }
}
