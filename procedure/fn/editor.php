<?php
class fn_Editor {
  protected static $idList = "editors";  

  /****************************************************************************/
  public static function displayNav() {
    global $PERMISSION, $TOOLS, $TOOLS_EDITOR;

    # language
    $lang = getLang();

    # is admin
    if( !$isAdmin = $_SESSION["editor"]["admin"] ) {
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
        )
      );
    }

    $editorsTabList = array(
      ( self::$idList . "-individual" ) => array(
        "label"     => $TOOLS_EDITOR["individual"][$lang],
        "selected"  => true,
        "innerHtml" => self::getIndividualList()
      ),
      ( self::$idList . "-group" ) => array(
        "label"  => $TOOLS_EDITOR["group"][$lang]
      )
    );

    # params
    $params = array(
      "id"        => ( self::$idList . "-nav" ),
      "mode"      => "tabs",
      "headtitle" => $TOOLS[self::$idList][$lang]
    );

    Includer::add( "uiNav" );
    return array(
      "replacement" => array(
        "query"     => "#" . self::$idList,
        "innerHtml" => ui_Nav::buildXml( $params, $editorsTabList )
      ),
      "hash" => true
    );
  }

  /****************************************************************************/
  public static function displayIndividualList() {
    global $TOOLS_EDITOR;
  
    # language
    $lang = getLang();

    # is admin
    if( !$isAdmin = $_SESSION["editor"]["admin"] ) {
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
        )
      );
    }

    # list
    return array(
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => self::getIndividualList()
      ),
    );
  }

  /****************************************************************************/
  protected static function getIndividualList() {
    global $TOOLS_EDITOR_INDIVIDUAL;
    $lang = getLang();

    # params
    $params = array(
      "id"         => "editors-individualList",
      "mode"       => array(
        "table"   => "Tableau",
        "compact" => "Compacte",
        "tree"    => "Arbre",
        "gallery" => "Galerie"
      ),
      #"headtitle"  => $TOOLS_EDITOR["individual"][$lang],
      "primary"    => "k",
      "main"       => "username",
      "order"      => "username",
      "selectable" => true,
      "columns"    => array(
        "k"        => array(
          "label"  => $TOOLS_EDITOR_INDIVIDUAL["k"][$lang],
          "hidden" => true
        ),
        "username" => array(
          "label"    => $TOOLS_EDITOR_INDIVIDUAL["username"][$lang],
          "class"    => "editor",
          "sortable" => true/*,
          "filtrable" => true*/
        ),
        "active"   => array(
          "label" => $TOOLS_EDITOR_INDIVIDUAL["active"][$lang],
          "sortable" => true,
          "field" => "IF( active = 1, 'oui', '' )"
        ),
        "admin"    => array(
          "label" => $TOOLS_EDITOR_INDIVIDUAL["admin"][$lang],
          "sortable" => true,
          "field" => "IF( admin = 1, 'oui', '' )"
        ),
        "longname" => array(
          "label"  => $TOOLS_EDITOR_INDIVIDUAL["longname"][$lang],
          "sortable" => true/*,
          "filtrable" => true*/
        )
      ),
      "actions" => array(
        "edit" => array(
          "title" => "Modifier"
        ),
        "delete" => array(
          "title" => "Supprimer",
          "multiple" => true
        )
      )
    );

    # field
    $fields = array();
    foreach( $params["columns"] as $key => $column ) {
      $fields[] = isset( $column["field"] )? ( $column["field"] . " AS $key" ): $key;
    }

    Includer::add( array( "dbEditor", "uiList" ) );
    return ui_List::buildXml( $params, db_Editor::get( $fields, false, $params["order"] ) );
  }

  /****************************************************************************/
  public static function displayGroupList() {
    global $PERMISSION;
    $lang = getLang();
    Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
    return array(
      "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
      "replacement" => array(
        "query" => "#main",
        "innerHtml" => fn_edit::getMain() 
      )
    );
  }

  /****************************************************************************/
  public static function edit( $k ) {
    global $PERMISSION, $SETTING, $LOGIN;
    $lang = getLang();

    # is admin
    if( !$isAdmin = $_SESSION["editor"]["admin"] ) {
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
        )
      );
    }

    if( !$values = db_Editor::get( array( "k", "username", "longname", "lang", "admin", "active" ), "k=$k" ) ) {
return "Introuvable";
    }

    $params = array(
      "id"     => "editor-$k",
      "action" => "save",
      "submit" => "Enregistrer",
      "method" => "post",
      "class"  => "editor",
      "headtitle"  => $values[0]["username"] . " - Éditeur"
    );

    $fields = array(
      "k"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "editor"
      ),
      "active" => array(
        "label"        => "Compte activé",
        "type"         => "checkbox",
        "value"        => "1"
      ),
      "login" => array(
        "legend" => "Paramètres de connexion",
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
            "maxlength"    => 30,
            "size"         => 20,
            "autocomplete" => "off",
          ),
          "confirmpassword" => array(
            "label"        => "Confirmation de mot de passe",
            "type"         => "password",
            "maxlength"    => 30,
            "size"         => 20,
            "autocomplete" => "off",
          ),
          "admin" => array(
            "label"        => "Administrateur",
            "type"         => "checkbox",
            "value"        => "1"
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

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        $values[0]
      )
    );
  }

  /****************************************************************************/
  public static function delete( $k ) {
    global $PERMISSION;
    $lang = getLang();
    Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
    return array(
      "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
      "replacement" => array(
        "query" => "#main",
        "innerHtml" => fn_edit::getMain() 
      )
    );
  }
}
