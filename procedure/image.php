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
#TODO alt=png, quality=[0-9], bgcolor=#fff, rotate=[-0-9], mode=scale|fit|crop|stretch
#http://www.nodstrum.com/2006/12/09/image-manipulation-using-php/
      # mode

      $mode = isset( $params["mode"] )? $params["mode"]: "scale"; //max, crop, stretch ...
      if( in_array( $mode, array( "scale", "thumb" ) ) ) {
        $width = isset( $params["width"] )? $params["width"]: false;
        $height = isset( $params["height"] )? $params["height"]: false;
        if( $width || $height ) {

          # get new size
          $newSize = self::resize( $info, array( $width, $height ), $mode );

          # get im object
          if( !$im = self::open( $path, $mimeType ) ) {
            return "unable to open";
          }

          $im2 = ImageCreateTrueColor( $newSize[0], $newSize[1] );
          imagecopyResampled( $im2, $im, 0, 0, $newSize[2], $newSize[3], $newSize[0], $newSize[1], $newSize[4], $newSize[5] );
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
  protected static function resize( $info, $params, $mode ) {
    global $MAXSIZE;
    list( $width, $height ) = $info;
    list( $newWidth, $newHeight ) = $params;

    # scale
    if( $mode == "scale" ) {
      $newWidth = $newWidth && $newWidth <= $MAXSIZE["width"]? $newWidth: $MAXSIZE["width"];
      $newHeight = $newHeight && $newHeight <= $MAXSIZE["height"]? $newHeight: $MAXSIZE["height"];
      $ratio = min( $newWidth / $width, $newHeight / $height );
      $newWidth = $width * $ratio;
      $newHeight = $height * $ratio;
      $x = 0;
      $y = 0;

    # thumb
    } elseif( $mode == "thumb" ) {
      $newWidth = min( ( $newWidth? $newWidth: $newHeight ), $MAXSIZE["width"] );
      $newHeight = min( ( $newHeight? $newHeight: $newWidth ), $MAXSIZE["height"] );
      $ratio = max( $newWidth / $width, $newHeight / $height );
      $x = ( $width - $newWidth / $ratio ) / 2;
      $y = ( $height - $newHeight / $ratio ) / 2;
      $width = $newWidth / $ratio;
      $height = $newHeight / $ratio;
    }

    return array( (int)$newWidth, (int)$newHeight, (int)$x, (int)$y, (int)$width, (int)$height );
  }
}
