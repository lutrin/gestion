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

  /****************************************************************************/
  public static function getForm( $lang ) {
    global $LOGIN;

    Includer::add( "uiForm" );

    # set params
    $params = array(
      "id"     => "login",
      "action" => "login",
      "submit" => $LOGIN["connect"][$lang],
      "method" => "get"
    );

    # set fields
    $fields = array(
      "connect" => array(
        "legend" => $LOGIN["legend"][$lang],
        "type"   => "fieldset",
        "fieldlist" => array(
          "username" => array(
            "label"        => $LOGIN["username"][$lang],
            "required"     => "required",
            "autofocus"    => "autofocus",
            "autocomplete" => "off",
          ),
          "password" => array(
            "label"        => $LOGIN["password"][$lang],
            "type"         => "password",
            "required"     => "required",
            "autocomplete" => "off",
          )
        )
      )
    );

    return ui_Form::buildXml( $params, $fields );
  }
}
