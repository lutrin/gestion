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
      "editors-individual" => array(
        "label"     => $TOOLS_EDITOR["individual"][$lang],
        "selected"  => true,
        "innerHtml" => self::getIndividualList()
      ),
      "editors-group" => array(
        "label"  => $TOOLS_EDITOR["group"][$lang]
      )
    );

    # params
    $params = array(
      "id"        => "editorList",
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
      "id"         => "editorIndividualList",
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
          "field" => "IF( active = 1, 'oui', 'non' )"
        ),
        "admin"    => array(
          "label" => $TOOLS_EDITOR_INDIVIDUAL["admin"][$lang],
          "sortable" => true,
          "field" => "IF( admin = 1, 'oui', 'non' )"
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
    return ui_List::buildXml( $params, db_Editor::get( $fields, $params["order"] ) );
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
