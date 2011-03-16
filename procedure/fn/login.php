<?php
class fn_Login {
  /****************************************************************************/
  public static function isConnected() {

    # get editor
    if( !$editor = self::getSessionEditor() ) {
      return false;
    }

    # get key
    $key = "session_" . $editor["user"];

return true;

    # get cookie token
    if( !$token = ( isset( $_COOKIE[$key] )? $_COOKIE[$key]: false ) ) {
      return false;
    }

    # valid token
    return Tokenizer::exists( $key, $token );
  }

  /****************************************************************************/
  public static function getSessionEditor() {
    return isset( $_SESSION["editor"]["user"] )? $_SESSION["editor"]["user"]: false;
  }
}
