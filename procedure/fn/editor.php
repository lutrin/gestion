<?php
class fn_Editor extends fn {
  protected static $idList = "editors";  

  /****************************************************************************/
  public static function getContent() {
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
      "mode"      => "tabs"/*,
      "headtitle" => $TOOLS[self::$idList][$lang]*/
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
  public static function getContent_individual() {
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
      )
    );
  }

  /****************************************************************************/
  protected static function getIndividualList( $partOnly = false ) {

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
      "mainAction" => "edit",
      "rowAction"  => "edit",
      "order"      => "username",
      "selectable" => true,
      "addable"    => true,
      "columns"    => self::getIndividualColumns(),
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
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "dbEditor", "uiList" ) );

    return ui_List::buildXml( $params, db_Editor::get( $fields, false, $params["order"] ), $partOnly );
  }

  /****************************************************************************/
  protected static function prepareFields( $columns ) {
    $fields = array();
    foreach( $columns as $key => $column ) {
      $fields[] = isset( $column["field"] )? ( $column["field"] . " AS $key" ): $key;
    }
    return $fields;
  }

  /****************************************************************************/
  protected static function getIndividualColumns() {
    global $TOOLS_EDITOR_INDIVIDUAL;
    $lang = getLang();

    return array(
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
        "field" => "IF( active = 1, '', 'inactif' )"
      ),
      "admin"    => array(
        "label" => $TOOLS_EDITOR_INDIVIDUAL["admin"][$lang],
        "sortable" => true,
        "field" => "IF( admin = 1, 'admin', '' )"
      ),
      "longname" => array(
        "label"  => $TOOLS_EDITOR_INDIVIDUAL["longname"][$lang],
        "sortable" => true/*,
        "filtrable" => true*/
      )
    );
  }

  /****************************************************************************/
  public static function getContent_group() {
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
  public static function edit_individualList( $k ) {
    global $PERMISSION;
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
    return array(
      "details" => self::getEdit( $k )
    );
  }

  /****************************************************************************/
  public static function add_individualList() {
    global $PERMISSION;
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

    # get default values
    Includer::add( "dbEditor" );
    $defaults = db_Editor::defaults();
    $defaults["k"] = 0;

    # get params
    $params = self::getFormParams();
    $params["headtitle"] = "Nouvel éditeur";
    $fields = self::getFormFields( 0 );

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        $defaults
      )
    );
  }

  /****************************************************************************/
  public static function delete_individualList( $kList ) {
    global $PERMISSION, $DELETE;
    $lang = getLang();

    # is admin
    if( ( !$isAdmin = $_SESSION["editor"]["admin"] ) ) {
      Includer::add( array( "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain() 
        )
      );
    }

    # is myself
    if( in_array( $_SESSION["editor"]["k"], $kList ) ) {
      Includer::add( array( "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $DELETE["title"][$lang], $DELETE["message"][$lang] )
      );
    }

    # remove
    Includer::add( "dbEditor" );
    db_Editor::remove( $kList );
    
    return array(
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => self::getIndividualList()
      ),
      "details" => ""
    );
  }

  /****************************************************************************/
  public static function getEdit( $k ) {
    if( !$values = db_Editor::get( array( "k", "username", "longname", "lang", "admin", "active" ), "k=$k" ) ) {
      return "Introuvable $k";
    }

    $params = self::getFormParams( $k );
    $params["headtitle"] = $values[0]["username"] . " - Éditeur";
    $fields = self::getFormFields( $k );

    Includer::add( array( "uiForm" ) );
    return ui_Form::buildXml(
      $params,
      $fields,
      $values[0]
    );
  }

  /****************************************************************************/
  public static function save( $k ) {
    global $PERMISSION;
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

    # valid form
    $values = $_GET;
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $k ),
      self::getFormFields( $k ),
      $values,
      $k
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # username unique
    $username = $values["username"];
    Includer::add( "dbEditor" );
    if( db_Editor::count( "k", array( "NOT k=$k", "username='$username'" ) ) ) {
      $result["errorList"][] = array( "name" => "username", "msg" => "mustbeunique" );
      return $result;
    }

    # values
    $valuesToSave = array();
    foreach( $values as $key => $value ) {
  
      # not in database
      if( in_array( $key, array( "token", "confirmpassword", "k", "object", "action" ) ) ) {
        continue;
      }
  
      # password
      if( $key == "password" ) {
        if( !$value ) {
          continue;
        }
        $valuesToSave[$key] = "PASSWORD('" . DB::mysql_prep( $value ) . "')";
        continue;
      }

      # add quotes
      $valuesToSave[$key] = "'" . DB::mysql_prep( $value ) . "'";
    }

    # update or insert
    $newK = db_Editor::save( $valuesToSave, $k );
    if( !$k ) {
      $k = $newK[0];
    }

   # list
    return array(
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => self::getIndividualList()
      ),
      "details" => self::getEdit( $k )
    );
  }

  /****************************************************************************/
  protected static function getFormParams( $k = 0 ) {
    return array(
      "id"       => "editor-$k",
      "action"   => "save",
      "submit"   => "Enregistrer",
      "method"   => "post",
      "class"    => "editor",
      "closable" => true
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $k ) {
    global $LOGIN, $EDITOR, $SETTING;
    $lang = getLang();
  
    # password field
    $password = array(
      "type"         => "password",
      "maxlength"    => 30,
      "size"         => 20,
      "autocomplete" => "off"
    );
    if( !$k ) {
      $password["label"] = $EDITOR["password"][$lang];
      $password["required"] = "required";
    } else {
      $password["label"] = $EDITOR["passwordoptional"][$lang];
    }

    # active and admin checkbox
    $active = array(
      "label" => $EDITOR["active"][$lang],
      "type"  => "checkbox",
      "value" => "1"
    );
    $admin =  array(
      "label" => $EDITOR["admin"][$lang],
      "type"  => "checkbox",
      "value" => "1"
    );
    if( $k == $_SESSION["editor"]["k"] ) {
      $admin["disabled"] = "disabled";
      $active["disabled"] = "disabled";
    }

    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "editor"
      ),
      "active" => $active,
      "login" => array(
        "legend" => $SETTING["login"][$lang],
        "type"   => "fieldset",
        "fieldlist" => array(
          "username" => array(
            "label"        => $EDITOR["username"][$lang],
            "required"     => "required",
            "maxlength"    => 30,
            "size"         => 20,
            "autofocus"    => "autofocus",
            "autocomplete" => "off",
          ),
          "password" => $password,
          "confirmpassword" => array(
            "label"        => $EDITOR["confirmpassword"][$lang],
            "type"         => "password",
            "maxlength"    => 30,
            "size"         => 20,
            "autocomplete" => "off",
            "equal"        => "password"
          ),
          "admin" => $admin
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
  }
}
