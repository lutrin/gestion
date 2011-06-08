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
      "selectable" => true,
      "refreshable" => true,
      "columns"     => array(
        "k"    => array(
          "hidden" => true
        ),
        "name" => array(
          "label"    => "Nom de fichier"
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
        "view"   => array(
          "title"      => "Visualiser",
          "individual" => true
        ),
        "explore"   => array(
          "title"      => "Explorer",
          "individual" => true
        ),
        "rename"   => array(
          "title"      => "Renommer",
          "individual" => true
        ),
        "edit"   => array(
          "title"      => "Modifier",
          "individual" => true
        ),
        "insert"   => array(
          "title"      => "Insérer",
          "individual" => true
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true
        )
      )
    );

    $list = Dir::getExplore( $k );
    foreach( $list as $key => $item ) {
      $class = self::getClass( $item["name"], $item["mimetype"] );
      $list[$key]["class"] = $class;
      $list[$key]["action"] = self::getAction( $class );
      $list[$key]["size"] = self::getHumanFileSize( $item["size"] );
    }
    return array(
      "details" => ui_List::buildXml( $params, $list )
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

  /****************************************************************************/
  protected static function getClass( $file, $type ) {

    # class list
    $classList = array(
      "folder" => array( "directory" ),
      "gif"    => array( "image/gif" ),
      "jpg"    => array( "image/jpeg" ),
      "png"    => array( "image/png" ),
      "svg"    => array( "image/svg+xml" ),
      "html"   => array( "text/html" ),
      "php"    => array( "text/x-php" ),
      "text"   => array( "text/x-c++", "text/plain", "text/x-c" ),
      "xml"    => array( "application/xml" )
    );

    # default
    $class = "file";

    # get simple class
    foreach( $classList as $key => $item ) {
      if( in_array( $type, $item ) ) {
        $class = $key;
        break;
      }
    }

    # get text class
    if( $class != "text") {
      return $class;
    }

    # text list
    $textList = array(
      "js"  => "javascript",
      "sql" => "sql",
      "css" => "css"
    );
    $decomposed = explode( ".", $file );
    $last = array_pop( $decomposed );
e( $last );
    return isset( $textList[$last] )? $textList[$last]: $class;
  }

  /****************************************************************************/
  protected static function getAction( $class ) {

    # action list
    $actionList = array(
      "rename"  => array( "file", "folder", "gif", "svg", "jpg", "png" ),
      "explore" => array( "folder" ),
      "insert"  => array( "folder" ),
      "view"    => array( "gif", "svg", "jpg", "png" ),
      "edit"    => array( "text", "html", "php", "svg", "xml", "javascript", "sql", "css" )
    );

    # get action
    $actions = array();
    foreach( $actionList as $key => $action ) {
      if( in_array( $class, $action ) ) {
        $actions[] = $key;
      }
    }
    return $actions;
  }

  /****************************************************************************/
  protected static function getHumanFileSize( $size ) {
    if( is_numeric( $size ) ) {
      $decr = 1024;
      $step = 0;
      $prefix = array( 'Octets', 'Ko', 'Mo', 'Go', 'To', 'Po' );
      while( ( $size / $decr ) > 0.9 ) {
        $size = $size / $decr;
        $step++;
      }
      return round( $size, 1 ) . '&nbsp;' . $prefix[$step];
    } else { 
      return '-';
    }
  }
}
