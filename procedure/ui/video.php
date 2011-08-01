<?php
class ui_Video {
  public static function buildXml( $info, $msg ) {
    $altVideoType = array( "mp4", "ogv", "webm" );
    $srcList = array(
      Tag::build( "source", array(
        "src" => $info["url"],
        "data-type" => $info["extension"],
        "type" => $info["mimetype"]
      ) )
    );

    # get file extension
    $type = finfo_open( FILEINFO_MIME_TYPE );
    foreach( $altVideoType as $videoType ) {
      if( $videoType != $info["extension"] ) {
        $url = preg_replace( '/' . $info["extension"] . '$/', $videoType, $info["url"] );
        if( file_exists( $url ) ) {
          $srcList[] = Tag::build( "source", array(
            "src" => $url,
            "data-type" => $videoType,
            "type" => finfo_file( $type, $url )
          ) );
        }
      }
    }

    # mp3 flash player
    /*$url = preg_replace( '/' . $info["extension"] . '$/', "mp3", $info["url"] );
    if( file_exists( $url ) ) {
      $srcList[] = Tag::build( "ui.mp3player", array( "src" => $url ) );
    }*/

    return Tag::build( "ui.video", false, $srcList );
  }
}
