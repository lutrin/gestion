<?php
class fn_Setting {
  public static function display() {
    return array( "dialog" => "<div style='background-color: #fff;margin: 100px;'><h2>Configuration</h2></div>" );
  }

  /****************************************************************************/
  protected static function getFormFields( $SETTING, $lang ) {
    return array(
      "login" => array(
        "legend" => $SETTING["login"][$lang],
        "type" => "fieldset",
        "fieldList" => array(
          "username" => array(
            "label"        => $SETTING["username"][$lang],
            "required"     => "required",
            "maxlength"    => 30,
            "autofocus"    => "autofocus",
            "autocomplete" => "off"
          ),
          "password" => array(
            "label"        => $SETTING["password"][$lang],
            "type"         => "password",
            "maxlength"    => 30,
            "autocomplete" => "off"
          ),
          "confirmpassword" => array(
            "label"        => $SETTING["confirmpassword"][$lang],
            "type"         => "password",
            "maxlength"    => 30,
            "autocomplete" => "off"
          )
        )
      ),
      "edit" => array(
        "legend" => $SETTING["edit"][$lang],
        "type" => "fieldset",
        "fieldList" => array(
          "lang" => array(
            "label" => $SETTING["lang"][$lang],
            "type" => "radioList",
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
