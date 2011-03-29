<?php
class fn_Setting {
  protected static $id = "setting";

  /****************************************************************************/
  public static function display() {
    global $SETTING, $DEFAULT_LANG;

    Includer::add( "uiForm" );

    # language
    $lang = $DEFAULT_LANG; /*TODO get language from user config*/
    
    return array( "dialog" => ui_Form::buildXml(
      self::getFormParams( $SETTING, $lang ),
      self::getFormFields( $SETTING, $lang ),
      $_SESSION["editor"]
    ), $_SESSION["editor"] );
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
      "login" => array(
        "legend" => $SETTING["login"][$lang],
        "type" => "fieldset",
        "fieldlist" => array(
          "username" => array(
            "label"        => $SETTING["username"][$lang],
            "required"     => "required",
            "maxlength"    => 30,
            "size"         => 20,
            "autofocus"    => "autofocus",
            "autocomplete" => "off"
          ),
          "password" => array(
            "label"        => $SETTING["password"][$lang],
            "type"         => "password",
            "maxlength"    => 30,
            "size"         => 20,
            "autocomplete" => "off"
          ),
          "confirmpassword" => array(
            "label"        => $SETTING["confirmpassword"][$lang],
            "type"         => "password",
            "maxlength"    => 30,
            "size"         => 20,
            "autocomplete" => "off"
          )
        )
      ),
      "edit" => array(
        "legend" => $SETTING["edit"][$lang],
        "type" => "fieldset",
        "fieldlist" => array(
          "longname" => array(
            "label" => $SETTING["longname"][$lang],
            "maxlenght" => 255,
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
