<?php
class fn_Login {
  protected static $id = "login";
//TODO function get Editor from session
  /****************************************************************************/
  public static function isConnected() {

    # get editor
    if( !$username = ( isset( $_SESSION["editor"]["username"] )? $_SESSION["editor"]["username"]: false ) ) {
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

    # get useEditor
    if( $username = ( isset( $_SESSION["useEditor"]["username"] )? $_SESSION["useEditor"]["username"]: false ) ) {

      # reset
      $_SESSION["useEditor"] = false;
      $useEditor = db_Editor::getInfo( $username );
      if( $editor["admin"] && $useEditor && $useEditor["active"] ) {
        $_SESSION["useEditor"] = $useEditor;
      }
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
  protected static function getUsername() {
    $sessionEditor = self::getSessionEditor();
    return isset( $_SESSION["editor"]["username"] )? $_SESSION["editor"]["username"]: false;
  }

  /****************************************************************************/
  public static function getSessionEditor() {
    if( isset( $_SESSION["editor"] ) &&
        isset( $_SESSION["editor"]["admin"] ) &&
        $_SESSION["editor"]["admin"] &&
        isset( $_SESSION["useEditor"] ) &&
        $_SESSION["useEditor"] ) {
      return $_SESSION["useEditor"];
    }
    return isset( $_SESSION["editor"] )? $_SESSION["editor"]: false;
  }

  /****************************************************************************/
  public static function setSessionEditor( $info ) {
    if( isset( $_SESSION["editor"] ) &&
        isset( $_SESSION["editor"]["admin"] ) &&
        $_SESSION["editor"]["admin"] &&
        isset( $_SESSION["useEditor"]["k"] ) &&
        ( $_SESSION["useEditor"]["k"] == $info["k"] ) ) {
      $_SESSION["useEditor"] = $info;
      return $_SESSION["useEditor"];
    }
    $_SESSION["useEditor"] = false;
    if( isset( $_SESSION["editor"]["k"] ) && $_SESSION["editor"]["k"] == $info["k"] ) {
      $_SESSION["editor"] = $info;
    }
    return $_SESSION["editor"];
  }

  /****************************************************************************/
  public static function getSessionKList() {
    $kList = array();
    if( isset( $_SESSION["editor"]["k"] ) && $_SESSION["editor"]["k"] ) {
      $kList[] = $_SESSION["editor"]["k"];
      if( $_SESSION["editor"]["admin"] && isset( $_SESSION["useEditor"]["k"] ) && $_SESSION["useEditor"]["k"] ) {
        $kList[] = $_SESSION["useEditor"]["k"];
      }
    }
    return $kList;
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
    if( isset( $_SESSION["useEditor"] ) && $_SESSION["useEditor"] ) {
      $_SESSION["useEditor"] = false;
    } else {
      $_SESSION["editor"] = false;
    }

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
  public static function useAccount( $k ) {

    # get user info
    Includer::add( "dbEditor" );
    if( !$editor = db_Editor::getInfoByK( $k ) ) {
      Includer::add( array( "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( "Utilisation de compte", "Le compte est introuvable." )
      );
    }

    # account disabled
    if( !$editor["active"] ) {
      Includer::add( array( "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( "Utilisation de compte", "Le compte est désactivé." )
      );
    }

    # set session
    $_SESSION["useEditor"] = $editor;

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
  public static function isNotAllowed( $idList = false ) {
    global $PERMISSION;
    $lang = getLang();

    # is admin
    $sessionEditor = self::getSessionEditor();
    if( !( $isAdmin = $sessionEditor["admin"] ||
           ( $idList? in_array( $idList, $sessionEditor["toolList"] ): false ) ) ) {
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
        )
      );
    }
    return false;
  }

  /****************************************************************************/
  public static function isNotImplanted() {
    global $IM;
    $lang = getLang();

    # is admin
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( "Application", "Pas encore implanté." ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
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
