<?php
class fn_Login {
  protected static $id = "login";

  /****************************************************************************/
  public static function isConnected() {

    # get editor
    if( !$username = self::getSessionEditor() ) {
      return false;
    }

    # reset
    $_SESSION["editor"] = false;
    Includer::add( "dbEditor" );
    if( !$editor = db_Editor::getInfo( $username ) ) {
      return false;
    }

    # account disabled
    if( !$editor["active"] ) {
      return false;
    }

    # set session
    $_SESSION["editor"] = $editor;

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
  public static function buildForm( $msg = "" ) {
    global $LOGIN;

    Includer::add( "uiForm" );
    
    return ui_Form::buildXml(
      self::getFormParams( $LOGIN, $msg ),
      self::getFormFields( $LOGIN )
    );
  }

  /****************************************************************************/
  public static function connect( $values ) {
    global $LOGIN;

    # valid form
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $LOGIN ),
      self::getFormFields( $LOGIN ),
      $values
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # valid user exists
    Includer::add( "dbEditor" );
    if( !$editor = db_Editor::getInfo( $values["username"], $values["password"] ) ) {
      $result["formError"] = "incorrectlogin";
      return $result;
    }

    # account disabled
    if( !$editor["active"] ) {
      $result["formError"] = "disabledaccount";
      return $result;
    }

    # set session
    $_SESSION["editor"] = $editor;
    Tokenizer::delete( self::$id );

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main",           "innerHtml" => fn_Edit::getMain() ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton() )
      )
    );
  }

  /****************************************************************************/
  public static function disconnect( $msg = "" ) {
    $_SESSION["editor"] = false;

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main",           "innerHtml" => fn_Edit::getMain( $msg ) ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton() )
      )
    );
  }

  /****************************************************************************/
  protected static function getFormParams( $LOGIN, $msg = "" ) {
    return array(
      "id"     => self::$id,
      "action" => self::$id,
      "submit" => $LOGIN["connect"][getLang()],
      "method" => "post",
      "message" => $msg
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $LOGIN ) {
    $lang = getLang();
    return array(
      "connect" => array(
        "legend" => $LOGIN["legend"][$lang],
        "type"   => "fieldset",
        "fieldlist" => array(
          "username" => array(
            "label"        => $LOGIN["username"][$lang],
            "required"     => "required",
            "maxlength"    => 30,
            "size"         => 20,
            "autofocus"    => "autofocus",
            "autocomplete" => "off",
          ),
          "password" => array(
            "label"        => $LOGIN["password"][$lang],
            "type"         => "password",
            "required"     => "required",
            "size"         => 20,
            "autocomplete" => "off",
          )
        )
      )
    );
  }
}
