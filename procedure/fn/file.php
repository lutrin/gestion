<?php
class fn_File extends fn {
  protected static $idList = "files";

  /****************************************************************************/
  public static function getContent() {
    global $PERMISSION;

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

    return array(
      "replacement" => array(
        "query"     => "#" . self::$idList,
        "innerHtml" =>  self::getFolderTree()
      ),
      "hash" => true
    );
  }

  /****************************************************************************/
  public static function refresh_folder() {
    global $PERMISSION;
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

    return array(
      "replacement" => array(
        "query"     => "#" . self::$idList,
        "innerHtml" =>  self::getFolderTree()
      ),
      "details" => " "
    );
  }

  /****************************************************************************/
  public static function add_folder() {
    global $PERMISSION;
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

    # get params
    $params = self::getFormParamsFolder();
    $params["headtitle"] = "Nouveau&nbsp;dossier";
    $fields = self::getFormFieldsFolder( 0 );

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields
      )
    );
  }

  /****************************************************************************/
  public static function save_folder() {
    global $PERMISSION, $PUBLICPATH;
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

    # valid form
    $values = $_GET;
    Includer::add( "fnForm" );
    $result = fn_Form::hasErrors(
      self::getFormParamsFolder(),
      self::getFormFieldsFolder(),
      $values
    );

    # fatal error or error list
    if( isset( $result["fatalError"] ) || ( isset( $result["errorList"] ) && $result["errorList"] ) ) {
      return $result;
    }

    # folder name unique
    $name = $values["name"];
    Includer::add( "dir" );
    $newPath = "$PUBLICPATH/" . ( $values["path"]? "{$values['path']}/": "" ) . $name;
    if( Dir::exists( $newPath ) ) {
      $result["errorList"][] = array( "name" => "name", "msg" => "alreadyexists" );
      return $result;
    }

    # permit
    if( !Dir::isPermitted( "$PUBLICPATH/" . ( $values["path"]? "{$values['path']}/": "" ) ) ) {
      $result["errorList"][] = array( "name" => "name", "msg" => "notwritepermission" );
      return $result;
    }

    # add
    $created = Dir::mkdir( $newPath );

    # list
    return array(
      "replacement" => array(
        "query" => "#" . self::$idList,
        "innerHtml" => self::getFolderTree()
      ),
      "details" => " "
    );
  }


  /****************************************************************************/
  public static function explore_folder( $k ) {
    global $PERMISSION;
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

    # get
    Includer::add( array( "dir", "uiList" ) );
    

    $params = array(
      "id" => "folder-$k",
      "headtitle" => db_Path::getPath( $k ) . "&nbsp;-&nbsp;Dossier",
      "mode" => array(
        "gallery" => "Galerie",
        "table"   => "Tableau",
        "compact" => "Compact"
      ),
      "main" => "name",
      "class" => "folder",
      "addable" => true,
      "refreshable" => true,
      "columns"     => array(
        "k"    => array(
          "hidden" => true
        ),
        "name" => array(
          "label"    => "Nom de fichier",
          "class"    => "file"
        ),
        "type" => array(
          "label"    => "Type"
        ),
        "encoding" => array(
          "label"    => "Encodage"
        ),
        "size" => array(
          "label" => "Taille"
        )
      ),
      "actions"     => array(
        "rename"   => array(
          "title" => "Renommer"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true
        )
      )
    );

    return array(
      "details" => ui_List::buildXml( $params, Dir::getExplore( $k ) )
    );
  }

  /****************************************************************************/
  public static function rename_folder() {
  }

  /****************************************************************************/
  protected static function getFolderTree() {
    global $PUBLICPATH;

    # get tree
    Includer::add( array( "dir", "uiList" ) );

    # params
    $params = array(
      "id"          => "files-folder",
      "mode"        => array(
        "tree" => "Arbre"
      ),

      "main"        => "name",
      "mainAction"  => "explore",
      "rowAction"   => "rename",
      "addable"     => true,
      "refreshable" => true,
      "expandable"  => true,
      "columns"     => array(
        "k"    => array(
          "hidden" => true
        ),
        "name" => array(
          "label"    => "Nom de dossier",
          "class"    => "folder"
        ),
        "path" => array(
          "label"    => "Chemin"
        )
      ),
      "actions"     => array(
        "explore"   => array(
          "title" => "Explorer"
        ),
        "insert"   => array(
          "title" => "Insérer"
        ),
        "rename"   => array(
          "title" => "Renommer"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true
        )
      )
    );

    return ui_List::buildXml( $params, Dir::getTree() );
  }

  /****************************************************************************/
  protected static function getFormParamsFolder() {
    return array(
      "id"       => "folder-new",
      "action"   => "save",
      "submit"   => "Enregistrer",
      "method"   => "post",
      "class"    => "folder",
      "closable" => true
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsFolder( $path = "" ) {
    return array(
      "object"     => array(
        "type" => "hidden",
        "value" => "file-folder"
      ),
      "path"     => array(
        "type" => "hidden",
        "value" => $path
      ),
      "name" => array(
        "label"     => "Nom du dossier",
        "required"  => "required",
        "pattern"   => "[\w\s\(\)\-\!]+",
        "maxlength" => 255
      )
    );
  }
}
