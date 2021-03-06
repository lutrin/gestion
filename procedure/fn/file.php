<?php
class fn_File extends fn {
  protected static $idList = "files";

  /****************************************************************************/
  public static function getContent() {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # tab list
    Includer::add( "fnMountpoint" );
    if( fn_Login::isNotAllowed() ) {

      # get mount point list
      $mountpointList = fn_Mountpoint::getList();
      if( $mountpointList ) {

        # foreach mount point, get folder tree
        $folderTree = self::getFolderTree(
          false,
          fn_Mountpoint::mapKList( $mountpointList )
        );
        return array(
          "replacement" => array(
            "query" => "#" .  self::$idList,
            "innerHtml" => $folderTree
          ),
          "hash" => true
        );
      }
      return array(
        "replacement" => array(
          "query" => "#" .  self::$idList,
          "innerHtml" => ""
        ),
        "hash" => true
      );
    }

    # admin user
    $tabList = array(
      ( self::$idList . "-folderTree" ) => array(
        "label"     => "Arborescence",
        "selected"  => true,
        "innerHtml" => self::getFolderTree()
      ),
      ( self::$idList . "-mountPoint" ) => array(
        "label"  => "Points de montage",
        "innerHtml" => fn_Mountpoint::getContent()
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
        "innerHtml" => ui_Nav::buildXml( $params, $tabList )
      ),
      "hash" => true
    );
  }

  /****************************************************************************/
  public static function getContent_imageFolder( $k, $for ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }
    $id = "imageFolder-$k";
    $options = array(
      "mode" => array(
        "gallery" => "Galerie"
      ),
      "insertable"  => false,
      "importable"  => false,
      "selectable"  => false,
      "refreshable" => false,
      "mainAction"  => false,
      "rowAction"   => false,
      "mainHref"    => $for,
      "mainTrigger" => "add",
      "actions"     => array(),
      "filter"      => array(
        "mimetype" => array( "image/gif", "image/jpeg", "image/png" )
      )
    );
    return array(
      "replacement" => array(
        "query"     => "#file-$id",
        "innerHtml" => self::getExploreFolder( $id, $k, $options )
      )
    );
  }

  /****************************************************************************/
  public static function refresh_folder( $id, $object ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    return array(
      "replacement" => array(
        "query"     => "#$object",
        "innerHtml" =>  self::getFolderTree()
      )
    );
  }

  /****************************************************************************/
  public static function refresh_explore( $id, $object ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    $idExploded = explode( "-", $object );
    $k = array_pop( $idExploded );

    return array(
      "replacement" => array(
        "query" => "#$object",
        "innerHtml" => self::getExploreFolder( $object, $k )
      )
    );
  }

  /****************************************************************************/
  public static function pick_folder( $excludedKList, $for ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # params
    $params = array(
      "id"          => "files-folder-pick",
      "mode"        => array(
        "tree" => "Arbre"
      ),
      "class"       => "listToPick",
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
          "class"    => "folder"
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );
    Includer::add( array( "uiDialog" ) );

    return array(
      "dialog" => ui_Dialog::buildXml( "Répertoire", self::getFolderTree( $params ) ),
    );
  }

  /****************************************************************************/
  public static function pick_image( $excludedKList, $for ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # params
    /*$params = array(
      "id"          => "files-image-pick",
      "mode"        => array(
        "tree" => "gallery"
      ),
      "class"       => "listToPick",
      "primary"     => "k",
      "main"        => "name",
      "mainTrigger" => "add",
      "mainHref"    => $for,
      "expandable"  => true,
      "accept"      => "gif,jpg,png",
      "columns"     => array(
        "k"        => array(
          "hidden" => true
        ),
        "name" => array(
          "class"    => "image"
        )
      )
    );*/
    $params = array(
      "id"   => "pick_image",
      "mode" => "accordion",
      "noAnchor" => true
    );

    # filter
    $filter = array(
      "mimetype" => array( "image/gif", "image/jpeg", "image/png" )
    );

    # tab list
    Includer::add( array( "fnMountpoint", "uiDialog", "uiNav" ) );
    $mountpointList = fn_Mountpoint::getList();
    if( $mountpointList ) {

      # foreach mount point, get folder tree
      $groupList = self::getGroupList(
        fn_Mountpoint::mapKList( $mountpointList ),
        $for,
        $filter
      );
    } else {
      $groupList = self::getGroupList( false, $for, $filter );
    }
    return array(
      "dialog" => ui_Dialog::buildXml(
        "Liste d'images",
        ui_Nav::buildXml( $params, $groupList )
      )
    );
  }

  /****************************************************************************/
  public static function add_folder() {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # get params
    $params = self::getFormParamsFolder( "folder" );
    $params["headtitle"] = "Nouveau&nbsp;dossier";
    $params["closable"] = true;
    $fields = self::getFormFieldsFolder( "folder" );

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        array( "k" => 0, "type" => "folder" )
      )
    );
  }

  /****************************************************************************/
  public static function insert_folder( $parentK ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # section list
    Includer::add( array( "dir", "uiNav", "uiForm" ) );
    $sectionList = array();
    $id = "file-$parentK";

    # get folder params
    $params = self::getFormParamsFolder( "file" );
    $fields = self::getFormFieldsFolder();
    $fields["content"] = self::getFormFieldsContent( true );

    # get info
    $info = Dir::getInfo( $parentK );

    $values = array(
      "k" => 0,
      "parentK" => $parentK,
      "url"  => Tag::build( "a", array( "href" => $info["url"], "target" => "_blank" ), $info["path"] )
    );
    $sectionList["$id-sectionCreation"] = array(
      "label"     => "Création",
      "innerHtml" => ui_Form::buildXml( $params, $fields, $values ),
      "selected"  => true
    );

    # tabs params
    $navParams = array(
      "id"        => $id,
      "mode"      => "separator",
      "class"     => "file",
      "headtitle" => "Nouveau&nbsp;-&nbsp;Dossier/Fichier",
      "closable"  => true
    );

    # breadcrumb
    if( $parentInfoList = Dir::getParentInfoList( $parentK ) ) {
      Includer::add( "uiBreadcrumb" );
      $navParams["breadcrumb"] = ui_Breadcrumb::buildXml( $parentInfoList, "files-explore", true );
    }

    return array(
      "details" => ui_Nav::buildXml( $navParams, $sectionList )
    );
  }

  /****************************************************************************/
  public static function import_folder( $parentK ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }
    Includer::add( array( "dir", "uiNav", "uiForm" ) );
    $params = self::getFormParamsUpload( $parentK );
    $fields = self::getFormFieldsUpload();
    $id = "file-$parentK";
    $sectionList = array(
      "$id-sectionUpload" => array(
        "label"     => "Importation",
        "innerHtml" => ui_Form::buildXml( $params, $fields, array( "targetK" => $parentK ) )
      )
    );

    # separator params
    $navParams = array(
      "id"        => $id,
      "mode"      => "separator",
      "class"     => "file",
      "headtitle" => "Importation de fichier",
      "closable"  => true
    );

    # breadcrumb
    if( $parentInfoList = Dir::getParentInfoList( $parentK ) ) {
      Includer::add( "uiBreadcrumb" );
      $navParams["breadcrumb"] = ui_Breadcrumb::buildXml( $parentInfoList, "files-explore", true );
    }
    return array(
      "details" => ui_Nav::buildXml( $navParams, $sectionList )
    );
  }

  /****************************************************************************/
  public static function insert_explore( $parentK ) {
    return self::insert_folder( $parentK );
  }

  /****************************************************************************/
  public static function import_explore( $parentK ) {
    return self::import_folder( $parentK );
  }

  /****************************************************************************/
  public static function upload( $values ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }
    Includer::add( "dir" );

    # get form
    $params = self::getFormParamsUpload( $values["targetK"] );
    $fields = self::getFormFieldsUpload();

    # valid token
    if( !Tokenizer::exists( $params["id"], $values["token"] ) ) {
      return array( "fatalError" => "tokenerror" );
    }

    # valid size
    /*if( !Dir::validFileSize() ) {
      return array( "errorFile" => "toobig" );
    }*/

    # store file
    if( !Dir::putFile( $values["targetK"], $values["filename"] ) ) {
      return array( "errorFile" => "nostore" );
    }
    //file put contents
    return array( "success" => true );
  }

  /****************************************************************************/
  public static function save( $k, $values ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # valid form
    Includer::add( "fnForm" );
    $type = isset( $values["type"] )? $values["type"]: false;
    $result = fn_Form::hasErrors(
      self::getFormParamsFolder( $type, $k ),
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
    if( $newK != $k ) {
      if( Dir::exists( $newK ) ) {
        $result["errorList"][] = array( "name" => "name", "msg" => "alreadyexists" );
        return $result;
      }

      # rename
      if( $k ) {
        $modified = Dir::rename( $k, $newK );

      # make
      } else {
        if( $type == "folder" ) {
          $created = Dir::mkdir( $newK );
        }
      }
      $k = $newK;
    }

    # content
    if( isset( $values["content"] ) && $type != "folder" ) {
      Dir::write( $k, $values["content"] );
    }

    # list
    $parentContent = array( "details" => " " );
    if( isset( $values["parentK"] ) && $values["parentK"] ) {
      $parentContent = self::edit_folder( $values["parentK"] );
    }
    return array_merge(
      array(
        "replacement" => array(
          "query" => "#" . self::$idList,
          "innerHtml" => self::getFolderTree()
        )
      ),
      $parentContent
    );
  }

  /****************************************************************************/
  public static function edit_folder( $k ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    # get
    Includer::add( array( "dir", "uiNav", "uiList", "uiForm" ) );
    $info = Dir::getInfo( $k );
    $infoClass = self::getClass( $info["name"], $info["mimetype"] );
    $tabKeyList = self::getTabList( $infoClass );
    $tabList = array();
    $id = "files-$k";

    # explore tab
    if( in_array( "explore", $tabKeyList ) ) {
      $tabList["$id-tabExplore"] = array(
        "label"     => "Contenu",
        "innerHtml" => self::getExploreFolder( $id, $k )
      );
    }

    # edit
    $formParams = self::getFormParamsFolder( $infoClass, $k );
    $fields = self::getFormFieldsFolder( $infoClass );

    # set values
    $values = array(
      "k"       => $k,
      "parentK" => Dir::getParentK( $k ),
      "name"    => $info["name"],
      "type"    => $infoClass . " ({$info["mimetype"]})",
      "size"    => self::getHumanFileSize( $info["size"] ),
      "url"     => Tag::build( "a", array( "href" => $info["url"], "target" => "_blank" ), $info["path"] )
    );

    # content
    if( in_array( "edit", $tabKeyList ) ) {

      # get form
      //$formParams = array_merge( $formParams, self::getFormParamsContent( $infoClass, $k ) );
      $fields["content"] = self::getFormFieldsContent();
      $values["content"] = "<![CDATA[" . Dir::getContent( $info["path"] ) . "]]>";
    } elseif( $infoClass == "music" ) {
      Includer::add( "uiAudio" );
      $fields["read"] = array(
        "type" => "fieldset",
        "fieldlist" => array(
          "audio" => array(
            "type" => "info",
            "label" => "Lecture"
          )
        )
      );
      $values["audio"] = ui_Audio::buildXml( $info, "Impossible de lire le fichier" );
    } elseif( $infoClass == "clip" ) {
      Includer::add( "uiVideo" );
      $fields["read"] = array(
        "type" => "fieldset",
        "fieldlist" => array(
          "video" => array(
            "type" => "info",
            "label" => "Lecture"
          )
        )
      );
      $values["video"] = ui_Video::buildXml( $info, "Impossible de lire le fichier" );
    } elseif( self::isImage( $infoClass, $info["name"] ) ) {
      Includer::add( "encode" );
      $fields["view"] = array(
        "type" => "fieldset",
        "fieldlist" => array(
          "image" => array(
            "type" => "info",
            "label" => "Aperçu"
          )
        )
      );
      $values["image"] = Tag::build(
        "img",
        array(
          "data-src" => "procedure/controller.php?encode="
                      . Encode::getString(
                         array(
                           "image" => $info["path"],
                           "width" => 320,
                           "height" => 320
                         )
                       )
        )
      );
    }
    $tabList["$id-tabEdit"] = array(
      "label" => "Édition",
      "innerHtml" => ui_Form::buildXml( $formParams, $fields, $values )
    );

    # select first
    foreach( $tabList as $key => $tab ) {
      $tabList[$key]["selected"] = true;
      break;
    }

    # tabs params
    $navParams = array(
      "id"        => "tabs-$k",
      "mode"      => "tabs",
      "class"     => $infoClass,
      "headtitle" => $info["name"] . "&nbsp;-&nbsp;Dossier",
      "closable"  => true
    );

    # breadcrumb
    if( $parentInfoList = Dir::getParentInfoList( $k ) ) {
      Includer::add( "uiBreadcrumb" );
      $navParams["breadcrumb"] = ui_Breadcrumb::buildXml( $parentInfoList, "files-explore" );
    }

    return array(
      "details" => ui_Nav::buildXml( $navParams, $tabList )
    );
  }

  /****************************************************************************/
  public static function edit_explore( $k ) {
    return self::edit_folder( $k );
  }

  /****************************************************************************/
  public static function delete_folder( $kList ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
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
  public static function delete_explore( $kList, $id ) {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }

    foreach( $kList as $k ) {

      # valid exists
      Includer::add( "dir" );
      if( Dir::exists( $k ) ) {
        Dir::delete( $k );
      }
    }

    $idExploded = explode( "-", $id );
    $k = array_pop( $idExploded );

    return array(
      "replacement" => array(
        "query" => "#$id",
        "innerHtml" => self::getExploreFolder( $id, $k )
      )
    );

  }

  /****************************************************************************/
  protected static function getExploreFolder( $id, $k, $options = false ) {
    global $PUBLICPATH;

    $exploreParams = array(
      "id" => "files-explore-$k",
      "mode" => array(
        "gallery" => "Galerie",
        "compact" => "Compact",
        "table"   => "Tableau",
        "resume"  => "Résumé",
      ),
      "main" => "name",
      "mainAction" => "edit",
      "rowAction"  => "edit",
      "insertable" => true,
      "importable" => true,
      "selectable" => true,
      "refreshable" => true,
      "key"        => $k,
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
        "import"   => array(
          "title"      => "Importer",
          "individual" => true
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true
        )
      )
    );

    # options
    if( $options ) {
      $exploreParams = array_merge( $exploreParams, $options );
    }

    # get filter list
    Includer::add( array( "dir", "uiList" ) );
    $oldList = Dir::getExplore( $k );
    $list = array();
    foreach( $oldList as $key => $item ) {
error_log( print_r( $item, 1 ) );
      $class = self::getClass( $item["name"], $item["mimetype"] );
      if( $class == "php" ) {
        continue;
      }
      $new = array_merge( $item, array(
        "class"     => $class,
        "indAction" => self::getAction( $class ),
        "size"      => self::getHumanFileSize( $item["size"] )
      ) );
      if( self::isImage( $class, $item["name"] ) ) {
        Includer::add( "encode" );
        $new["icon"] = "procedure/controller.php?encode="
                     . Encode::getString(
                        array(
                          "image" => $item["path"],
                          "width" => 58,
                          "height" => 58,
                          "mode" => "thumb"
                        )
                      );
#        $new["icon"] = "procedure/controller.php?image=" . $item["path"] . "&width=64&mode=thumb";
      }
      unset( $new["path"] );
      $list[] = $new;
    }

    return ui_List::buildXml( $exploreParams, $list );
  }

  /****************************************************************************/
  protected static function isImage( $class, $name ) {
    return in_array( $class, array( "jpg", "png", "gif" ) ) &&
           preg_match( '/\.(jpg|jpe|png|gif)$/i', $name );
  }

  /****************************************************************************/
  protected static function getFolderTree( $params = false, $pathKList = false ) {

    # get tree
    Includer::add( array( "dir", "uiList" ) );

    # params
    if( !$params ) {
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
          "import"   => array(
            "title" => "Importer"
          ),
          "delete" => array(
            "title"    => "Supprimer",
            "individual" => true
          )
        )
      );
    }

    if( $pathKList ) {
      return ui_List::buildXml( $params, Dir::getTreeList( $pathKList ) );
    }
    return ui_List::buildXml( $params, Dir::getTree() );
  }

  /****************************************************************************/
  protected static function getGroupList( $pathKList, $for, $filter = false ) {
Includer::add( "dir" );
    $treeList = $pathKList? Dir::getTreeList( $pathKList, $filter ): Dir::getTree( "", $filter );

    $subGroupList = self::getSubgroupList( $treeList );
    $groupList = array();
    foreach( $subGroupList as $group ) {
      $k = Dir::getK( $group );

      # filter
      $accept = true;
      if( $filter ) {
        $accept = false;
        $fileList = Dir::getExplore( $k );
        foreach( $fileList as $fileItem ) {
          foreach( $filter as $key => $filterItem ) {
            if( in_array( $fileItem[$key], $filterItem ) ) {
              $accept = true;
              break;
            }
          }
          if( $accept ) {
            break;
          }
        }
      }

      if( $accept ) {
        $groupList["file-imageFolder-$k"] = array(
          "label" => $group,
          "class" => "empty",
          "params" => "for=$for"
        );
      }
    }
    return $groupList;
print json_encode( $groupList );
exit;
e( $groupList );
  }

  /****************************************************************************/
  protected static function getSubgroupList( $treeList, $name = false ) {
    $groupList = array();
    $prefix = $name? "$name/": false;
    foreach( $treeList as $tree ) {
      $groupList[] = $prefix . $tree["name"];
      if( isset( $tree["childList"] ) ) {
          $groupList = array_merge( $groupList, self::getSubgroupList( $tree["childList"],  $prefix . $tree["name"] ) );
      }
    }
    return $groupList;
  }

  /****************************************************************************/
  protected static function getFormParamsFolder( $infoClass = "folder", $k = 0 ) {
    return array(
      "id"       => "file-$k",
      "action"   => "save",
      "submit"   => "Enregistrer",
      "method"   => "post",
      "class"    => "folder"
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsFolder( $type = false ) {
    $fieldlist = array(
      "k"     => array(
        "type" => "hidden"
      ),
      "parentK" => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "file"
      ),
      "edition" => array(
        "type" => "fieldset",
        "legend" => "Générales",
        "class" => $type,
        "fieldlist" => array(
          "name" => array(
            "label"     => "Nom",
            "required"  => "required",
            "pattern"   => "[\w\s\(\)\-\!]+",
            "size"      => 30,
            "maxlength" => 255
          ),
          "type" => array(
            "label" => "Type",
            "type"  => "info"
          ),
          "size" => array(
            "label" => "Taille",
            "type"  => "info"
          ),
          "url" => array(
            "label" => "Emplacement",
            "type"  => "info",
            "format" => "url"
          )
        )
      )
    );

    # type
    if( $type ) {
      $fieldlist["type"] = array(
        "type" => "hidden",
        "value" => $type
      );
    } else {
      $fieldlist["type"] = array(
        "label" => "Type",
        "type" => "radiolist",
        "required" => "required",
        "list" => array(
          "folder" => array(
            "label" => "Dossier",
            "value" => "folder"
          ),
          "file" => array(
            "label" => "Fichier texte",
            "value" => "file"
          )
        )
      );
    }

    return $fieldlist;
  }

  /****************************************************************************/
  protected static function getFormFieldsContent( $hide = false) {
    $field = array(
      "label" => "Contenu",
      "type"  => "textarea",
      "cols"  => 80,
      "rows"  => 24,
      "spellcheck" => "false"
    );
    if( $hide ) {
      $field["display"] = "type = file";
    }
    return $field;
  }

  /****************************************************************************/
  protected static function getFormParamsUpload( $parentK ) {
    return array(
      "id"       => "fileupload-$parentK",
      "action"   => "upload",
      "method"   => "post"
    );
  }

  /****************************************************************************/
  protected static function getFormFieldsUpload() {
    return array(
      "targetK"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "file"
      ),
      "fileUpload"    => array(
        "type"        => "fileUpload",
        "label"       => "Parcourir",
        "maxFileSize" => Dir::calculateMaxFileSize( "post" ),
        "required"    => "required",
        "multiple"    => "multiple"
      )
    );
  }

  /****************************************************************************/
  protected static function getClass( $file, $type ) {

    # class list
    $classList = getData( "filetype" );

    # default
    $class = "file";

    # get simple class
    foreach( $classList as $key => $item ) {
      if( in_array( $type, $item ) ) {
        $class = $key;
        break;
      }
    }

    # other
    if( !in_array( $class, array( "file", "ogg", "text" ) ) ) {
      return $class;
    }
    $decomposed = explode( ".", $file );
    $last = strtolower( array_pop( $decomposed ) );

    $ext = getData( "exttype" );
    foreach( $ext[$class] as $key => $item ) {
      if( in_array( $last, $item ) ) {
        $class = $key;
        break;
      }
    }
    return $class;
  }

  /****************************************************************************/
  protected static function getAction( $class ) {

    # action list
    $actionList = array(
      "insert"  => array( "folder" ),
      "import"  => array( "folder" )
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
