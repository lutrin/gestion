<?php
class fn_Editor extends fn {
  protected static $idList = "editors";  

  /****************************************************************************/
  public static function getContent() {
    global $PERMISSION, $TOOLS, $TOOLS_EDITOR;

    # language
    $lang = getLang();

    # is admin
    if( !( ( $isAdmin = $_SESSION["editor"]["admin"] ) ||
           ( in_array( self::$idList, $_SESSION["editor"]["toolList"] ) ) ) ) {
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
      "id"   => ( self::$idList . "-nav" ),
      "mode" => "tabs"
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
  public static function pick_individualList( $excludedKList, $for ) {
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

    Includer::add( "fnSetting" );

    $id = "editors-individualList-pick";

    # params
    $params = array(
      "id"          => $id,
      "mode"        => array(
        "compact" => "Compacte"
      ),
      "primary"     => "k",
      "main"        => "username",
      "mainTrigger" => "add",
      "mainHref"    => $for,
      "columns"     => array(
        "k"        => array(
          "hidden" => true
        ),
        "username" => array(
          "class"    => "editor"
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "dbEditor", "uiList", "uiDialog" ) );

    # excluded
    $where = false;
    if( $excludedKList ) {
      $where = "NOT k IN (" . join( ",", $excludedKList ) . ")";
    }

    return array(
      "dialog" => ui_Dialog::buildXml( "Liste", ui_List::buildXml( $params, db_Editor::get( $fields, $where ) ) ),
    );
  }

  /****************************************************************************/
  public static function pick_groupList( $excludedKList, $for ) {
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

    Includer::add( "fnSetting" );

    $id = "editors-groupList-pick";

    # params
    $params = array(
      "id"          => $id,
      "mode"        => array(
        "tree" => "Arbre"
      ),
      "primary"     => "k",
      "main"        => "name",
      "mainTrigger" => "add",
      "mainHref"    => $for,
      "expandable"  => true,
      "columns"     => array(
        "k"        => array(
          "hidden" => true
        ),
        "name" => array(
          "class"    => "groupEditor"
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "dbGroupEditor", "uiList", "uiDialog" ) );

    # excluded
    $where = false;
    if( $excludedKList ) {
      $where = "NOT k IN (" . join( ",", $excludedKList ) . ")";
    }

    return array(
      "dialog" => ui_Dialog::buildXml( "Liste", ui_List::buildXml( $params, db_GroupEditor::getTree( $fields, 0, $where ) ) ),
    );
  }

  /****************************************************************************/
  public static function getContent_group() {
    global $TOOLS_EDITOR, $PERMISSION;
  
    # language
    $lang = getLang();

    # is admin
    if( !( ( $isAdmin = $_SESSION["editor"]["admin"] ) ||
           ( in_array( self::$idList, $_SESSION["editor"]["toolList"] ) ) ) ) {
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
        "query" => "#editors-group",
        "innerHtml" => self::getGroupList()
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
      "details" => self::getEditIndividual( $k )
    );
  }

  /****************************************************************************/
  public static function edit_groupList( $k ) {
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
      "details" => self::getEditGroup( $k )
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
    $params = self::getFormParamsIndividual();
    $params["headtitle"] = "Nouvel&nbsp;éditeur";
    $fields = self::getFormFieldsIndividual( 0 );

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
  public static function add_groupList( $parentK = 0 ) {
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
    Includer::add( "dbGroupEditor" );
    $defaults = db_GroupEditor::defaults();
    $defaults["k"] = 0;
    $defaults["parentK"] = $parentK;

    # get params
    $params = self::getFormParamsGroup();
    $params["headtitle"] = "Nouveau&nbsp;groupe";
    $fields = self::getFormFieldsGroup( 0 );

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
  public static function insert_groupList( $k ) {
    return self::add_groupList( $k );
  }

  /****************************************************************************/
  public static function refresh_individualList() {
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
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => self::getIndividualList()
      ),
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function refresh_groupList() {
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
      "replacement" => array(
        "query" => "#editors-group",
        "innerHtml" => self::getGroupList()
      ),
      "details" => " "
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
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function delete_groupList( $kList ) {
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

    /*# is myself
    if( in_array( $_SESSION["editor"]["k"], $kList ) ) {
      Includer::add( array( "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $DELETE["title"][$lang], $DELETE["message"][$lang] )
      );
    }*/

    # remove
    Includer::add( "dbGroupEditor" );
    db_GroupEditor::remove( $kList );
    
    return array(
      "replacement" => array(
        "query" => "#editors-group",
        "innerHtml" => self::getGroupList()
      ),
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function save_individual( $k ) {
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
      self::getFormParamsIndividual( $k ),
      self::getFormFieldsIndividual( $k ),
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
    $valuesToSave = db_Editor::getEmptyValues();
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

      # tool list
      } elseif( $key == "toolList" ) {
        if( $value ) {
          $valuesToSave[$key] = "'" . join( ",", DB::ensureArray( $value ) ) . "'";
        }
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
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function save_group( $k ) {
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
      self::getFormParamsGroup( $k ),
      self::getFormFieldsGroup( $k ),
      $values,
      $k
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # name unique
    $name = $values["name"];
    $parentK = $values["parentK"];
    Includer::add( array( "dbGroupEditor", "dbEditorInGroup" ) );
    if( db_GroupEditor::count( "k", array( "NOT k=$k", "name='$name'", "parentK='$parentK'" ) ) ) {
      $result["errorList"][] = array( "name" => "name", "msg" => "mustbeunique" );
      return $result;
    }

    # values
    $valuesToSave = db_GroupEditor::getEmptyValues();
    $editorKList = array();
    foreach( $values as $key => $value ) {
  
      # not in database
      if( in_array( $key, array( "token", "k", "object", "action" ) ) ) {
        continue;
      }

      # tool list
      if( $key == "toolList" ) {
        if( $value ) {
          $valuesToSave[$key] = "'" . join( ",", DB::ensureArray( $value ) ) . "'";
        }
        continue;
      }

      # editor list
      if( $key == "editorList" ) {
        if( $value ) {
          $editorKList = DB::ensureArray( $value );
        }
        continue;
      }

      # add quotes
      $valuesToSave[$key] = "'" . DB::mysql_prep( $value ) . "'";
    }

    # update or insert
    $newK = db_GroupEditor::save( $valuesToSave, $k );
    if( !$k ) {
      $k = $newK[0];
    }

    # save editor list
    db_EditorInGroup::saveEditorList( $editorKList, $k );

    # list
    return array(
      "replacement" => array(
        "query" => "#editors-group",
        "innerHtml" => self::getGroupList()
      ),
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function getEditIndividual( $k ) {
    if( !$values = db_Editor::get( array( "k", "username", "longname", "lang", "admin", "active", "toolList" ), "k=$k" ) ) {
      return "Introuvable $k";
    }

    $params = self::getFormParamsIndividual( $k );
    $params["headtitle"] = $values[0]["username"] . "&nbsp;-&nbsp;Éditeur";
    $fields = self::getFormFieldsIndividual( $k );

    Includer::add( array( "uiForm" ) );
    return ui_Form::buildXml(
      $params,
      $fields,
      $values[0]
    );
  }

  /****************************************************************************/
  public static function getEditGroup( $k ) {

    # inner value
    Includer::add( array( "dbGroupEditor", "dbEditorInGroup", "dbEditor" ) );
    if( !$values = db_GroupEditor::get( array( "k", "parentK", "name", "longname", "active", "toolList" ), "k=$k" ) ) {
      return "Introuvable $k";
    }
    $values = $values[0];

    # outer values
    $editors = array();
    if( $editorList = db_EditorInGroup::get( "editorK", "groupK=$k" ) ) {
      $values["editorList"] = join( ",", array_map( function( $editor ) {
        return $editor["editorK"];
      }, $editorList ) );
    }

    $params = self::getFormParamsGroup( $k );
    $params["headtitle"] = $values["name"] . "&nbsp;-&nbsp;Groupe";
    $fields = self::getFormFieldsGroup( $k );

    Includer::add( array( "uiForm" ) );
    return ui_Form::buildXml(
      $params,
      $fields,
      $values
    );
  }

  /****************************************************************************/
  protected static function getIndividualList( $partOnly = false ) {
    Includer::add( "fnSetting" );

    $id = "editors-individualList";
    $storedValue = fn_Setting::getAccountStorage( "$id-sort" );
    $order = $storedValue? $storedValue: "username";

    # params
    $params = array(
      "id"          => $id,
      "mode"        => array(
        "compact" => "Compacte"
      ),
      /*"mode"        => array(
        "table"   => "Tableau",
        "compact" => "Compacte",
        "gallery" => "Galerie"
      ),*/
      "primary"     => "k",
      "main"        => "username",
      "mainAction"  => "edit",
      "rowAction"   => "edit",
      "order"       => $order,
      "selectable"  => true,
      "addable"     => true,
      "refreshable" => true,
      "columns"     => self::getIndividualColumns(),
      "actions"     => array(
        "edit"   => array(
          "title" => "Modifier"
        ),
        "delete" => array(
          "title"    => "Supprimer",
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
  protected static function getGroupList() {
    Includer::add( "fnSetting" );

    $id = "editors-groupList";
    $storedValue = fn_Setting::getAccountStorage( "$id-sort" );
    $order = $storedValue? $storedValue: "name";

    # params
    $params = array(
      "id"          => $id,
      "mode"        => array(
        "tree"   => "Arbre"
      ),
      "primary"     => "k",
      "main"        => "name",
      "mainAction"  => "edit",
      "rowAction"   => "expand",
      "order"       => $order,
      "selectable"  => true,
      "addable"     => true,
      "refreshable" => true,
      "expandable"  => true,
      "columns"     => self::getGroupColumns(),
      "actions"     => array(
        "edit"   => array(
          "title" => "Modifier"
        ),
        "insert"   => array(
          "title" => "Insérer"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "dbGroupEditor", "uiList" ) );

    return ui_List::buildXml( $params, db_GroupEditor::getTree( $fields, 0, false, $params["order"] ) );
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
  protected static function getGroupColumns() {
    global $TOOLS_EDITOR_GROUP;
    $lang = getLang();

    return array(
      "k"        => array(
        "label"  => $TOOLS_EDITOR_GROUP["k"][$lang],
        "hidden" => true
      ),
      "name" => array(
        "label"    => $TOOLS_EDITOR_GROUP["name"][$lang],
        "class"    => "groupEditor"/*,
        "sortable" => true*/
      )/*,
      "active"   => array(
        "label" => $TOOLS_EDITOR_GROUP["active"][$lang],
        "sortable" => true,
        "field" => "IF( active = 1, '', 'inactif' )"
      ),
      "longname" => array(
        "label"  => $TOOLS_EDITOR_GROUP["longname"][$lang],
        "sortable" => true
      )*/
    );
  }

  /****************************************************************************/
  protected static function getFormParamsIndividual( $k = 0 ) {
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
  protected static function getFormParamsGroup( $k = 0 ) {
    return array(
      "id"       => "groupEditor-$k",
      "action"   => "save",
      "submit"   => "Enregistrer",
      "method"   => "post",
      "class"    => "groupEditor",
      "closable" => true
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsIndividual( $k ) {
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

    # tool list
    Includer::add( "fnEdit" );
    $list = array();
    foreach( fn_Edit::getToolList() as $key => $tool ) {
      $list[$key] = array(
        "label" => $tool["label"],
        "value" => $key
      );
    }
    $toolList = array(
      "label" => "Outils",
      "type"  => "checklist",
      "list"  => $list
    );

    if( $k == $_SESSION["editor"]["k"] ) {
      $admin["disabled"] = "disabled";
      $active["disabled"] = "disabled";
      $toolList["disabled"] = "disabled";
    }

    # get editor list
    Includer::add( "dbGroupEditor" );
    $groupList = array();
    foreach( db_GroupEditor::get( array( "k", "name" ) ) as $group ) {
      $groupList[$group["k"]] = array(
        "value" => $group["k"],
        "label" => $group["name"]
      );
    }
    

    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "editor-individual"
      ),
      "separator" => array(
        "type"     => "separator",
        "itemlist" => array(
          "general" => array(
            "label" => "Propriétés générales",
            "content" => array(
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
            )
          ),
          "permission" => array(
            "label"   => "Permissions",
            "content" => array(
              "status" => array(
                "legend" => "État",
                "type" => "fieldset",
                "fieldlist" => array(
                  "active" => $active,
                  "admin" => $admin
                )
              ),
              "toolList" => $toolList/*,
              "groupList" => array(
                "label"    => "Groupes éditeurs",
                "type"     => "picklist",
                "multiple" => "multiple",
                "object"   => "editors-groupList",
                "list"     => $groupList
              )*/
            )
          )
        )
      )
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsGroup( $k ) {
    global $LOGIN, $GROUPEDITOR, $SETTING;
    $lang = getLang();

    # tool list
    Includer::add( "fnEdit" );
    $toolList = array();
    foreach( fn_Edit::getToolList() as $key => $tool ) {
      $toolList[$key] = array(
        "label" => $tool["label"],
        "value" => $key
      );
    }

    # get editor list
    Includer::add( "dbEditor" );
    $editorList = array();
    foreach( db_Editor::get( array( "k", "username" ) ) as $editor ) {
      $editorList[$editor["k"]] = array(
        "value" => $editor["k"],
        "label" => $editor["username"]
      );
    }

    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "parentK" => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "editor-group"
      ),
      "separator" => array(
        "type"     => "separator",
        "itemlist" => array(
          "general" => array(
            "label" => "Propriétés générales",
            "content" => array(
              "id" => array(
                "legend" => "Identification",
                "type" => "fieldset",
                "fieldlist" => array(
                  "name" => array(
                    "label"        => $GROUPEDITOR["name"][$lang],
                    "required"     => "required",
                    "maxlength"    => 30,
                    "size"         => 20,
                    "autofocus"    => "autofocus",
                    "autocomplete" => "off",
                  ),
                  "longname" => array(
                    "label" => $SETTING["longname"][$lang],
                    "maxlenght" => 255,
                    "required" => "required",
                    "size" => 20
                  )
                )
              )
            )
          ),
          "permission" => array(
            "label"   => "Permissions",
            "content" => array(
              "status" => array(
                "legend" => "État",
                "type" => "fieldset",
                "fieldlist" => array(
                  "active" => array(
                    "label" => $GROUPEDITOR["active"][$lang],
                    "type"  => "checkbox",
                    "value" => "1"
                  )
                )
              ),
              "toolList" => array(
                "label" => "Outils",
                "type"  => "checklist",
                "list" => $toolList
              ),
              "editorList" => array(
                "label"    => "Membres éditeurs",
                "type"     => "picklist",
                "multiple" => "multiple",
                "object"   => "editors-individualList",
                "list"     => $editorList
              )
            )
          )
        )
      )
    );
  }
}
