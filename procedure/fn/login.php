<?php
class fn_Login {
  protected $id = "login";

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
  public static function buildForm( $lang ) {
    global $LOGIN;

    Includer::add( "uiForm" );

    # set params
    $params = array(
      "id"     => self::$id,
      "action" => self::id,
      "submit" => $LOGIN["connect"][$lang],
      "method" => "get"
    );

    return ui_Form::buildXml( $params, self::getFormFields( $LOGIN, $lang ) );
  }

  /****************************************************************************/
  public static function connect( $values ) {
    global $LOGIN;

    # language
    $lang = $DEFAULT_LANG; /*TODO get language from user config*/

    # valid form
    $fields = self::getFormFields( $LOGIN, $lang );
  }

  /****************************************************************************/
  protected static function getFields( $LOGIN, $lang ) {
    return array(
      "connect" => array(
        "legend" => $LOGIN["legend"][$lang],
        "type"   => "fieldset",
        "fieldlist" => array(
          "username" => array(
            "label"        => $LOGIN["username"][$lang],
            "required"     => "required",
            "maxlength"    => 30,
            "autofocus"    => "autofocus",
            "autocomplete" => "off",
          ),
          "password" => array(
            "label"        => $LOGIN["password"][$lang],
            "type"         => "password",
            "required"     => "required",
            "maxlength"    => 30,
            "autocomplete" => "off",
          )
        )
      )
    );
  }
}
