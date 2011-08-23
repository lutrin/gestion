<?php
class fn_Mountpoint extends fn {
  protected static $idList = "mountpoint";

  /****************************************************************************/
  public static function getContent() {
    Includer::add( array( "uiList", "dbMountpoint" ) );
    return ui_List::buildXml( self::getListParams(), db_Mountpoint::get( "k,name" ) );
  }

  /****************************************************************************/
  public static function pick( $excludedKList, $for ) {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }

    Includer::add( "fnSetting" );

    $id = "mountpoint-pick";

    # params
    $params = array(
      "id"          => $id,
      "mode"        => array(
        "compact" => "Compacte"
      ),
      "class"       => "listToPick",
      "primary"     => "k",
      "main"        => "name",
      "mainTrigger" => "add",
      "mainHref"    => $for,
      "columns"     => array(
        "k"        => array(
          "hidden" => true
        ),
        "name" => array(
          "class"    => "mountpoint"
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "dbMountpoint", "uiList", "uiDialog" ) );

    # excluded
    $where = false;
    if( $excludedKList ) {
      $where = "NOT k IN (" . join( ",", $excludedKList ) . ")";
    }

    return array(
      "dialog" => ui_Dialog::buildXml( "Liste de points de montage", ui_List::buildXml( $params, db_Mountpoint::get( $fields, $where ) ) ),
    );
  }

  /****************************************************************************/
  public static function refresh() {
    return array(
      "replacement" => array(
        "query" => "#" . self::$idList,
        "innerHtml" => self::getContent()
      ),
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function add() {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }
    Includer::add( array( "uiForm", "dbMountpoint" ) );

    # get params
    $params = self::getFormParams();
    $params["headtitle"] = "Nouveau&nbsp;point de montage";
    $defaults = db_Mountpoint::defaults();
    $defaults["k"] = 0;


    return array(
      "details" => ui_Form::buildXml(
        $params,
        self::getFormFields(),
        $defaults
      )
    );
  }

  /****************************************************************************/
  public static function edit( $k ) {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }

    # get values
    Includer::add( array( "uiForm", "dbMountpoint" ) );
    if( !$values = db_Mountpoint::get( db_Mountpoint::$fields, "k=$k" ) ) {
      return "Introuvable $k";
    }
    $values = $values[0];

    # get fields
    $fields = self::getFormFields();

    # get params
    $params = self::getFormParams( $k );
    $params["headtitle"] = $values["name"] . "&nbsp;-&nbsp;Point de montage";

    # outer values
    if( $editorList = db_Association::get( "editor", "mountpoint", $k ) ) {
      $values["editorList"] = join( ",", array_map( function( $editor ) {
        return $editor["k"];
      }, $editorList ) );
    }
    if( $groupList = db_Association::get( "groupEditor", "mountpoint", $k ) ) {
      $values["groupList"] = join( ",", array_map( function( $group ) {
        return $group["k"];
      }, $groupList ) );
    }

    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        $values
      )
    );
  }

  /****************************************************************************/
  public static function save( $k, $values ) {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }

    # valid form
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParams( $k ),
      self::getFormFields(),
      $values,
      $k
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # name unique
    $name = $values["name"];
    Includer::add( "dbMountpoint" );
    if( db_Mountpoint::count( "k", array( "NOT k=$k", "name='$name'" ) ) ) {
      $result["errorList"][] = array( "name" => "name", "msg" => "mustbeunique" );
      return $result;
    }

    # values
    $valuesToSave = db_Mountpoint::$emptyValues;
    $editorKList = array(); //editorList
    $groupKList = array();  //groupList
    foreach( $values as $key => $value ) {
  
      # not in database
      if( in_array( $key, array( "token", "k", "object", "action" ) ) ) {
        continue;
      }

      # editor list
      if( $key == "editorList" ) {
        if( $value ) {
          $editorKList = DB::ensureArray( $value );
        }
        continue;
      }

      # group list
      if( $key == "groupList" ) {
        if( $value ) {
          $groupKList = DB::ensureArray( $value );
        }
        continue;
      }

      # add quotes
      $valuesToSave[$key] = "'" . DB::mysql_prep( $value ) . "'";
    }

    # update or insert
    $newK = db_Mountpoint::save( $valuesToSave, $k );
    if( !$k ) {
      $k = $newK[0];
    }

    # save editor list
    db_Association::save( array(
        "mountpoint" => $k,
        "editor" => $editorKList
    ) );

    # save group list
    db_Association::save( array(
        "mountpoint" => $k,
        "groupEditor" => $groupKList
    ) );

    # list
    return self::refresh();
  }

  /****************************************************************************/
  public static function delete( $kList ) {
  }

  /****************************************************************************/
  public static function getList() {
    Includer::add( array( "dbAssociation", "dbMountpoint", "dbEditor" ) );
    $editor = fn_Login::getSessionEditor();
    $mountpointList = db_Association::get( "mountpoint", "editor", $editor["k"] );
    if( $groupKList = db_Editor::getGroupKList( $editor["k"] ) ) {
      foreach( db_Association::get( "mountpoint", "groupEditor", $groupKList ) as $mountpoint ) {
        $mountpointList[] = $mountpoint;
      }
    }
    return $mountpointList;
  }

  /****************************************************************************/
  public static function mapKList( $list ) {
    Includer::add( "dbMountpoint" );
    return array_map(
      "mapK",
      db_Mountpoint::get(
        "pathK as k",
        "k IN ( " . join( ",", array_map( "mapK", $list ) ) . ")"
      )
    )
  }

  /****************************************************************************/
  protected static function getListParams() {
    return array(
      "id"          => self::$idList,
      "mode"        => array(
        "tree" => "compact"
      ),
      "main"        => "name",
      "mainAction"  => "edit",
      "rowAction"   => "edit",
      "addable"     => true,
      "refreshable" => true,
      "columns"     => array(
        "k"    => array(
          "hidden" => true
        ),
        "name" => array(
          "label"    => "Nom",
          "class"    => "mountpoint"
        )
      ),
      "actions"     => array(
        "edit"   => array(
          "title" => "Ouvrir"
        ),
        "add"   => array(
          "title" => "Ajouter"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "individual" => true
        )
      )
    );
  }

  /****************************************************************************/
  protected static function getFormParams( $k = 0 ) {
    return array(
      "id"        => "mountpoint-$k",
      "action"    => "save",
      "submit"    => "Enregistrer",
      "method"    => "post",
      "class"     => "mountpoint",
      "closable"  => true
    );
  }

  /****************************************************************************/
  protected static function getFormFields( $pathK = false ) {
    # get path list
    Includer::add( "dir" );
    $pathList = array();
    foreach( Dir::getList() as $path ) {
      $pathList[$path["k"]] = array(
        "value" => $path["k"],
        "label" => $path["name"]
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

    # get group list
    Includer::add( "dbGroupEditor" );
    $groupList = array();
    foreach( db_GroupEditor::get( array( "k", "name" ) ) as $group ) {
      $groupList[$group["k"]] = array(
        "value" => $group["k"],
        "label" => $group["name"]
      );
    }

    return array(
      "k" => array(
        "type" => "hidden"
      ),
      "object" => array(
        "type" => "hidden",
        "value" => "mountpoint"
      ),
      "identification" => array(
        "type" => "fieldset",
        "legend" => "Identification",
        "fieldlist" => array(
          "name" => array(
            "label" => "Nom",
            "required" => "required",
            "maxlength" => 255,
            "size"      => 30
          )
        )
      ),
      "pathK" => array(
        "label"    => "Répertoire",
        "type"     => "picklist",
        "class"    => "folder",
        "required" => "required",
        "object"   => "files-folder",
        "list"     => $pathList
      ),
      "status" => array(
        "type" => "fieldset",
        "legend" => "État",
        "fieldlist" => array(
          "active" => array(
            "label" => "Activé",
            "type"  => "checkbox",
            "value" => 1
          )
        )
      ),
      "permission" => array(
        "type" => "fieldset",
        "legend" => "Droits",
        "fieldlist" => array(
          "canView" => array(
            "label" => "Visualiser",
            "type"  => "checkbox",
            "value" => 1
          ),
          "canRename" => array(
            "label" => "Renommer",
            "type"  => "checkbox",
            "value" => 1
          ),
          "canEdit" => array(
            "label" => "Modifier le contenu",
            "type"  => "checkbox",
            "value" => 1
          ),
          "canDelete" => array(
            "label" => "Supprimer",
            "type"  => "checkbox",
            "value" => 1
          ),
          "canAdd" => array(
            "label" => "Ajouter",
            "type"  => "checkbox",
            "value" => 1
          )
        )
      ),
      "editorList" => array(
        "label"    => "Éditeurs",
        "type"     => "picklist",
        "multiple" => "multiple",
        "class"    => "editor",
        "object"   => "editors-individualList",
        "list"     => $editorList
      ),
      "groupList" => array(
        "label"    => "Groupes",
        "type"     => "picklist",
        "multiple" => "multiple",
        "class"    => "groupEditor",
        "object"   => "editors-groupList",
        "list"     => $groupList
      )
    );
  }
}
