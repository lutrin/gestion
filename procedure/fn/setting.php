<?php
class fn_Setting {
  protected static $id = "setting";

  /****************************************************************************/
  public static function display() {
    global $SETTING;

    Includer::add( "uiForm" );

    # language
    $lang = getLang();
    
    return array( "dialog" => ui_Form::buildXml(
      self::getFormParams( $SETTING, $lang ),
      self::getFormFields( $SETTING, $lang ),
      $_SESSION["editor"]
    ), $_SESSION["editor"] );
  }

  /****************************************************************************/
  public static function save( $k, $token ) {
    global $SETTING, $APP, $EDITOR;

    # allowed
    if( $k != $_SESSION["editor"]["k"] ) {
      return array( "fatalError" => "notpermitted" );
    }

    # language
    $lang = getLang();

    # valid form
    $values = $_GET;
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $SETTING, $lang ),
      self::getFormFields( $SETTING, $lang ),
      $values
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # update editor
    Includer::add( "dbEditor" );
    if( !db_Editor::save( array( "lang" => $values["lang"], "longname" => $values["longname"] ), $k ) ) {
      return array();
    }
    $_SESSION["editor"] = db_Editor::getInfo( $_SESSION["editor"]["username"] );
    $lang = $values["lang"];

    # title
    $tagtitle = $APP["name"][$lang] . " - " . $APP["site"];

    # replacement
    Includer::add( "fnEdit" );
    return array(
      "replacement" => array(
        array( "query" => "#main",           "innerHtml" => fn_Edit::getMain( $lang ) ),
        array( "query" => "#header-buttons", "innerHtml" => fn_Edit::getHeaderButton( $lang ) ),
        array( "query" => "#title",          "innerHtml" => $APP["name"][$lang] . "&nbsp;-&nbsp;" . $APP["site"] ),
        array( "query" => "#currentUser",    "innerHtml" => $_SESSION["editor"]["longname"] ),
        array( "query" => "#about",          "innerHtml" => $EDITOR["about"][$lang] ),
        array( "query" => "#condition",      "innerHtml" => $EDITOR["condition"][$lang] ),
        array( "query" => "#help",           "innerHtml" => $EDITOR["help"][$lang] ),
        array( "query" => "[lang]", "attributeList" => array( "name" => "lang", "value" => $lang ) )
      )
    );
  }

  /****************************************************************************/
  protected static function getFormParams( $SETTING, $lang ) {
    return array(
      "id"     => self::$id,
      "action" => "save",
      "submit" => $SETTING["apply"][$lang],
      "method" => "post",
      "headtitle"  => "Configurations"
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $SETTING, $lang ) {
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
