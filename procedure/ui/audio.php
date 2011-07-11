<?php
class ui_Audio {
  public static function buildXml( $info, $msg ) {
    $altAudioType = array( "mp3", "ogg", "wav", "m4a" );
    $srcList = array(
      Tag::build( "source", array(
        "src" => $info["url"],
        "data-type" => $info["extension"],
        "type" => $info["mimetype"]
      ) )
    );

    # get file extension
    $type = finfo_open( FILEINFO_MIME_TYPE );
    foreach( $altAudioType as $audioType ) {
      if( $audioType != $info["extension"] ) {
        $url = preg_replace( '/' . $info["extension"] . '$/', $audioType, $info["url"] );
        if( file_exists( $url ) ) {
          $srcList[] = Tag::build( "source", array(
            "src" => $url,
            "data-type" => $audioType,
            "type" => finfo_file( $type, $url )
          ) );
        }
      }
    }

    # mp3 flash player
    $url = preg_replace( '/' . $info["extension"] . '$/', "mp3", $info["url"] );
    if( file_exists( $url ) ) {
      $srcList[] = Tag::build( "ui.mp3player", array( "src" => $url ) );
    }

    return Tag::build( "ui.audio", false, $srcList );
  }
}
