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
    $key = "session_" . $editor["username"];

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
    return isset( $_SESSION["editor"]["username"] )? $_SESSION["editor"]["username"]: false;
  }

  /****************************************************************************/
  public static function buildForm( $lang, $msg = "" ) {
    global $LOGIN;

    Includer::add( "uiForm" );

    return ui_Form::buildXml(
      self::getFormParams( $LOGIN, $lang, $msg ),
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

    # fatal error or error list
    if( isset( $result["fatalError"] ) && isset( $result["errorList"] ) ) {
      return $result;
    }

    # valid user exists
    Includer::add( "dbEditor" );
    if( !$info = db_Editor::getInfo( $values["username"], $values["password"] ) ) {
      $result["formError"] = "incorrectlogin";
      return $result;
    }

    # account disabled
    if( !$info["active"] ) {
      $result["formError"] = "disabledaccount";
      return $result;
    }

    # set session
    $_SESSION["editor"] = $info;
    Tokenizer::delete( self::$id );

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main", "innerHtml" => fn_Edit::getMain( $lang ) ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton( $lang ) )
      )
    );
  }

  /****************************************************************************/
  public static function disconnect( $msg = "" ) {
    global $DEFAULT_LANG;

    # language
    $lang = $DEFAULT_LANG; /*TODO get language from user config*/

    $_SESSION["editor"] = false;

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main", "innerHtml" => fn_Edit::getMain( $lang, $msg ) ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton( $lang ) )
      )
    );
  }

  /****************************************************************************/
  protected static function getFormParams( $LOGIN, $lang, $msg = "" ) {
    return array(
      "id"     => self::$id,
      "action" => self::$id,
      "submit" => $LOGIN["connect"][$lang],
      "method" => "post",
      "message" => $msg
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
