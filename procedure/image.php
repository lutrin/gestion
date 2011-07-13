<?php
class Image {
  public static function generate( $params ) {
    global $PUBLICPATH;

    $encoded = false;

    # TODO encoded
    if( isset( $params["encoded"] ) ) {
      $encoded = true;

      # TODO already cached


      # decode and generate params
    }

    # image exists
    if( !isset( $params["image"] ) ) {
      return "Impossible to load image";
    }
    $path = $PUBLICPATH . "/" . $params["image"];
    if( !file_exists( $path ) ) {
      header( "HTTP/1.0 404 Not Found" );
      return "No exists";
    }
    $info = getimagesize( $path );

    # image type
    $mimeType = $info["mime"];
    if( !in_array( $mimeType, array( "image/jpeg", "image/pjpeg", "image/gif", "image/png" ) ) ) {
      return "Not an image $mimeType";
    }
    list( $format, $mimeExt ) = explode( "/", $mimeType );

    # image size
    if( !$size = filesize( $path ) ) {
      return "Empty";
    }

    # image string
    if( count( $params ) > 1 ) {

      # mode
      $mode = isset( $params["mode"] )? $params["mode"]: "max"; //max, crop, stretch ...
      if( $mode == "maximized" ) {
        $width = isset( $params["width"] )? $params["width"]: false;
        $height = isset( $params["height"] )? $params["height"]: false;
        if( $width || $height ) {

          # get new size
          $newSize = self::resize( $info, array( $width, $height ) );

          # get im object
          if( !$im = self::open( $path, $mimeType ) ) {
            return "unable to open";
          }
          $w = imagesx( $im );
          $h = imagesy( $im );
          $im2 = ImageCreateTrueColor( $newSize[0], $newSize[1] );
          imagecopyResampled( $im2, $im, 0, 0, 0, 0, $newSize[0], $newSize[1], $w, $h );
          imagedestroy( $im );

          setHeader( $mimeExt );
          header( 'Content-Disposition: inline; filename="' . Dir::getName( $path ) . '"' );
          header( "Content-Transfer-Encoding: binary" );
          ob_clean();
          flush();
          if( in_array( $mimeType, array( "image/jpeg", "image/pjpeg" ) ) ) {
            imagejpeg( $im2, null, 100 );
          } elseif( $mimeType == "image/gif" ) {
            imagegif( $im2 );
          } elseif( $mimeType == "image/png" ) {
            imagepng( $im, null, 0 );
          }
          imagedestroy( $im2 );
        } else {
          return "no dimension";
        }
      } else {
        return "not implanted";
      }


    } else {
      setHeader( $mimeExt );
      header( 'Content-Disposition: inline; filename="' . Dir::getName( $path ) . '"' );
      header( "Content-Transfer-Encoding: binary" );
      header( 'Content-Length: ' . $size );
      ob_clean();
      flush();
      readfile( $path );
    }
  }

  /****************************************************************************/
  protected static function open( $path, $mimeType ) {
    if( in_array( $mimeType, array( "image/jpeg", "image/pjpeg" ) ) ) {
      return imagecreatefromjpeg( $path );
    }
    if( $mimeType == "image/gif" ) {
      return imagecreatefromgif( $path );
    }
    if( $mimeType == "image/png" ) {
      return imagecreatefromgif( $path );
    }
    return false;
  }

  /****************************************************************************/
  protected static function resize( $info, $params ) {
    list( $width, $height ) = $info;
    if( $params[0] && ( $width > $params[0] ) ) {
      $height = ( ( $height / $width ) * $params[1] );
      $width = $params[0];
    }
    if( $params[1] && ( $height > $params[1] ) ) {
      $width = ( ( $width / $height ) * $params[1] );
      $height = $params[1];
    }
    return array( $width, $height );
  }
}
