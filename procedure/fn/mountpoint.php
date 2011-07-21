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
          ),
          "pathK" => array(
            "label"  => "Répertoire",
            "type"   => "picklist",
            "class"  => "folder",
            "object" => "files-folder",
            "list"   => array()
          )
        )
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
          "view" => array(
            "label" => "Visualiser",
            "type"  => "checkbox",
            "value" => 1
          ),
          "rename" => array(
            "label" => "Renommer",
            "type"  => "checkbox",
            "value" => 1
          ),
          "edit" => array(
            "label" => "Modifier le contenu",
            "type"  => "checkbox",
            "value" => 1
          ),
          "delete" => array(
            "label" => "Supprimer",
            "type"  => "checkbox",
            "value" => 1
          ),
          "add" => array(
            "label" => "Ajouter",
            "type"  => "checkbox",
            "value" => 1
          )
        )
      )
    );
  }
}
