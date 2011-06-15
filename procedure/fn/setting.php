<?php
class fn_Setting {
  protected static $id = "setting";

  /****************************************************************************/
  public static function display() {
    global $SETTING;

    Includer::add( "uiForm" );
    return array( "dialog" => ui_Form::buildXml(
      self::getFormParams( $SETTING ),
      self::getFormFields( $SETTING ),
      $_SESSION["editor"]
    ), $_SESSION["editor"] );
  }

  /****************************************************************************/
  public static function save( $k, $values ) {
    global $SETTING, $APP, $FOOTERLINK;

    # allowed
    if( $k != $_SESSION["editor"]["k"] ) {
      return array( "fatalError" => "notpermitted" );
    }

    # valid form
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $SETTING ),
      self::getFormFields( $SETTING ),
      $values
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # update editor
    Includer::add( "dbEditor" );
    $values = array( "lang" => $values["lang"], "longname" => $values["longname"] );
    $valuesToSave = array();
    foreach( $values as $key => $value ) {
        $valuesToSave[$key] = "'" . DB::mysql_prep( $value ) . "'";
    }
    if( !db_Editor::save( $valuesToSave, $k ) ) {
      return array();
    }
    $_SESSION["editor"] = db_Editor::getInfo( $_SESSION["editor"]["username"] );
    $lang = getLang();

    # title
    $tagtitle = $APP["name"][$lang] . " - " . $APP["site"];

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main",           "innerHtml" => fn_Edit::getMain() ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton() ),
        array( "query" => "#title",          "innerHtml" => $APP["name"][$lang] . "&nbsp;-&nbsp;" . $APP["site"] ),
        array( "query" => "#currentUser",    "innerHtml" => $_SESSION["editor"]["longname"] ),
        array( "query" => "#about",          "innerHtml" => $FOOTERLINK["about"][$lang] ),
        array( "query" => "#condition",      "innerHtml" => $FOOTERLINK["condition"][$lang] ),
        array( "query" => "#help",           "innerHtml" => $FOOTERLINK["help"][$lang] ),
        array( "query" => "[lang]", "attributeList" => array( "name" => "lang", "value" => $lang ) )
      )
    );
  }

  /****************************************************************************/
  public static function setAccountStorage( $name, $value ) {
    Includer::add( "dbEditor" );
    $storage = self::getStorage();
    if( !is_array( $storage ) ) {
      $storage = array();
    }
    $storage[$name] = $value;
    return array(
      "action" => "setAccountStorage",
      "name"   => $name,
      "value"  => $value,
      "saved"  => ( self::setStorage( $storage )? true: false )
    );
  }

  /****************************************************************************/
  public static function getAccountStorage( $name ) {
    Includer::add( "dbEditor" );
    if( $storage = self::getStorage() ) {
      return isset( $storage[$name] )? $storage[$name]: false;
    }
    return false;
  }

  /****************************************************************************/
  public static function removeAccountStorage( $name ) {
    Includer::add( "dbEditor" );
    $storage = self::getStorage();
    if( !is_array( $storage ) ) {
      $storage = array();
    }
    unset( $storage[$name] );
    return array(
      "action" => "removeAccountStorage",
      "name"   => $name,
      "saved"  => ( self::setStorage( $storage )? true: false )
    );
  }

  /****************************************************************************/
  protected static function setStorage( $storage ) {
    return db_Editor::save(
      array( "storage" => "'" . DB::mysql_prep( json_encode( $storage ) ) . "'" ),
      $_SESSION["editor"]["k"]
    );
  }

  /****************************************************************************/
  protected static function getStorage() {
    if( $result = db_Editor::get( "storage", "k=" . $_SESSION["editor"]["k"] ) ) {
      return json_decode( $result[0]["storage"], true );
    }
    return false;
  }

  /****************************************************************************/
  protected static function getFormParams( $SETTING ) {
    return array(
      "id"     => self::$id,
      "action" => "save",
      "submit" => $SETTING["apply"][getLang()],
      "method" => "post",
      "headtitle"  => "Configurations"
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $SETTING ) {
    $lang = getLang();
    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => self::$id
      ),
      "edit" => array(
        "legend" => $SETTING["edit"][$lang],
        "type" => "fieldset",
        "fieldlist" => array(
          "longname" => array(
            "label" => $SETTING["longname"][$lang],
            "maxlenght" => 255,
            "required" => "required",
            "size" => 20
          ),
          "lang" => array(
            "label" => $SETTING["lang"][$lang],
            "type" => "select",
            "list" => array(
              "fr" => array(
                "label" => "Français",
                "value" => "fr"
              ),
              "en" => array(
                "label" => "English",
                "value" => "en"
              )
            )
          )
        )
      )
    );
  }
}
