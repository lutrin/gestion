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
    $params["closable"] = true;
    $fields = self::getFormFieldsFolder();

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields
      )
    );
  }

  /****************************************************************************/
  public static function insert_folder( $parentK ) {
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
    $params["closable"] = true;
    $fields = self::getFormFieldsFolder();
    $values = array( "parentK" => $parentK );

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        $values
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
    Includer::add( "dir" );
    $parentPath = db_Path::getPath( $values["parentK"] );
    $newPath = Dir::getNewPath( $parentPath, $values["name"] );
    $newK = db_Path::getK( $newPath );
    if( $newK != $values["k"] ) {
      if( Dir::exists( $newK ) ) {
        $result["errorList"][] = array( "name" => "name", "msg" => "alreadyexists" );
        return $result;
      }

      # rename
      if( $values["k"] ) {
        $modified = Dir::rename( $values["k"], $newK );

      # make
      } else {
        $created = Dir::mkdir( $newK );
      }
    }

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
  public static function edit_folder( $k ) {
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
    Includer::add( array( "dir", "uiNav", "uiList", "uiForm" ) );
    $info = Dir::getInfo( $k );
    $infoClass = self::getClass( $info["name"], $info["mimetype"] );
    $tabKeyList = self::getTabList( $infoClass );
    $tabList = array();
    $id = "folder-$k";

    # explore tab
    if( in_array( "explore", $tabKeyList ) ) {

      # params
      $exploreParams = array(
        "id" => "files-folder-$k",
        "mode" => array(
          "gallery" => "Galerie",
          "compact" => "Compact",
          "table"   => "Tableau",
          "resume"  => "Résumé",
        ),
        "main" => "name",
        "mainAction" => "edit",
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
          "edit"   => array(
            "title" => "Ouvrir"
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

      # get filter list
      $oldList = Dir::getExplore( $k );
      $list = array();
      foreach( $oldList as $key => $item ) {
        $class = self::getClass( $item["name"], $item["mimetype"] );
        if( $class == "php" ) {
          continue;
        }
        $list[] = array_merge( $item, array(
          "class"     => $class,
          "indAction" => self::getAction( $class ),
          "size"      => self::getHumanFileSize( $item["size"] )
        ) );
      }

      $tabList["$id-tabExplore"] = array(
        "label"     => "Contenu",
        "innerHtml" => ui_List::buildXml( $exploreParams, $list )
      );
    }

    # view tab

    # edit tab
    if( in_array( "edit", $tabKeyList ) ) {

      # TODO get form
      $formParams = array(
        "id"       => "$infoClass-new",
        "action"   => "save",
        "submit"   => "Enregistrer",
        "method"   => "post",
        "class"    => $infoClass
      );
      $fields = array(
        "k"     => array(
          "type" => "hidden"
        ),
        "object"     => array(
          "type" => "hidden",
          "value" => "file-content"
        ),
        "content" => array(
          "label" => "Contenu",
          "type"  => "textarea",
          "cols"  => 80,
          "rows"  => 24,
          "spellcheck" => "false"
        )
      );
      $values = array(
        "k" => $k,
        "content" => "<![CDATA[" . Dir::getContent( $info["path"] ) . "]]>"
      );
      $tabList["$id-tabEdit"] = array(
        "label" => "Édition",
        "innerHtml" => ui_Form::buildXml( $formParams, $fields, $values )
      );
    }

    # rename tab
    $formParams = self::getFormParamsFolder();
    $fields = self::getFormFieldsFolder( $k );
    $values = array(
      "k"       => $k,
      "parentK" => Dir::getParentK( $k ),
      "name"    => $info["name"]
    );
    $tabList["$id-tabRename"] = array(
      "label" => "Propriétés",
      "innerHtml" => ui_Form::buildXml( $formParams, $fields, $values )
    );
    foreach( $tabList as $key => $tab ) {
      $tabList[$key]["selected"] = true;
      break;
    }

    # tabs params
    $navParams = array(
      "id"        => "folder-$k",
      "mode"      => "tabs",
      "class"     => $infoClass,
      "headtitle" => $info["path"] . "&nbsp;-&nbsp;Dossier",
      "closable"  => true
    );

    return array(
      "details" => ui_Nav::buildXml( $navParams, $tabList )
    );
  }

  /****************************************************************************/
  public static function delete_folder( $kList ) {
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

    foreach( $kList as $k ) {

      # valid exists
      Includer::add( "dir" );
      if( Dir::exists( $k ) ) {
        Dir::delete( $k );
      }
    }

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
      "mainAction"  => "edit",
      "rowAction"   => "edit",
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
        )
      ),
      "actions"     => array(
        "edit"   => array(
          "title" => "Ouvrir"
        ),
        "insert"   => array(
          "title" => "Insérer"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "individual" => true
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
      "class"    => "folder"
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsFolder() {
    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "parentK" => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "file-folder"
      ),
      "edition" => array(
        "type" => "fieldset",
        "legend" => "Générales",
        "fieldlist" => array(
          "name" => array(
            "label"     => "Nom du dossier",
            "required"  => "required",
            "pattern"   => "[\w\s\(\)\-\!]+",
            "maxlength" => 255
          )
        )
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
    return isset( $textList[$last] )? $textList[$last]: $class;
  }

  /****************************************************************************/
  protected static function getAction( $class ) {

    # action list
    $actionList = array(
      "insert"  => array( "folder" )
    );

    # get action
    $actions = array();
    foreach( $actionList as $key => $action ) {
      if( in_array( $class, $action ) ) {
        $actions[] = $key;
      }
    }
    return join( ",", $actions );
  }

  /****************************************************************************/
  protected static function getTabList( $class ) {

    # tabList
    $tabList = array(
      "explore" => array( "folder" ),
      "view"    => array( "gif", "jpg", "png", "svg" ),
      "edit"    => array( "svg", "html", "php", "text", "xml", "javascript", "sql", "css" )
    );

    # get tab
    $tabs = array();
    foreach( $tabList as $key => $tab ) {
      if( in_array( $class, $tab ) ) {
        $tabs[] = $key;
      }
    }
    return $tabs;
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
