<?php
class ui_Audio {
  public static function buildXml( $info, $msg ) {
    $altAudioType = array( "mp3", "ogg" );
    $srcList = array( Tag::build( "source", array( "src" => $info["url"] ) ) );

    # get file extension
    foreach( $altAudioType as $audioType ) {
      if( $audioType != $info["extension"] ) {
        $url = preg_replace( '/' . $info["extension"] . '$/', $audioType, $info["url"] );
        if( file_exists( $url ) ) {
          $srcList[] = Tag::build( "source", array( "src" => $url ) );
        }
      }
    }
    $srcList[] = Tag::build( "ui.mp3player", array( "src" => preg_replace( '/' . $info["extension"] . '$/', "mp3", $info["url"] ) ) );

    return Tag::build( "ui.audio", false, $srcList );
  }
}
