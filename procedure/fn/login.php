<?php
class fn_Login {
  protected static $id = "login";

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

    return ui_Form::buildXml(
      self::getFormParams( $LOGIN, $lang ),
      self::getFormFields( $LOGIN, $lang )
    );
  }

  /****************************************************************************/
  public static function connect( $values ) {
    global $LOGIN, $DEFAULT_LANG;

    # language
    $lang = $DEFAULT_LANG; /*TODO get language from user config*/

    # valid form
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $LOGIN, $lang ),
      self::getFormFields( $LOGIN, $lang ),
      $values
    );
    if( $result ) {

      # fatal error
      if( isset( $result["fatalError"] ) ) {
        return $result; /*TODO create token */
      }
    }

    # valid user exists
    Includer::add( "dbEditor" );
    return array( "exists" => db_Editor::getInfo( $values["username"], $values["password"] ) );
  }

  /****************************************************************************/
  protected static function getFormParams( $LOGIN, $lang ) {
    return array(
      "id"     => self::$id,
      "action" => self::$id,
      "submit" => $LOGIN["connect"][$lang],
      "method" => "post"
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $LOGIN, $lang ) {
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
