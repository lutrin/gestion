<?php
class fn_Mountpoint extends fn {
  protected static $idList = "mountpoint";

  /****************************************************************************/
  public static function getContent() {
    Includer::add( array( "uiList", "dbMountpoint" ) );
    return ui_List::buildXml( self::getListParams(), db_Mountpoint::get( "k,name" ) );
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
    $valuesToSave = db_Mountpoint::getEmptyValues();
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
  protected static function getFormFields() {
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
        "list"     => array()
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
        "list"     => array()
      ),
      "groupList" => array(
        "label"    => "Groupes",
        "type"     => "picklist",
        "multiple" => "multiple",
        "class"    => "groupEditor",
        "object"   => "editors-groupList",
        "list"     => array()
      )
    );
  }
}
