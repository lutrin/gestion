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
          "innerHtml" => fn_edit::getMain( $lang ) 
        )
      );
    }

    $editorsTabList = array(
      "editors-individual" => array(
        "label"     => $TOOLS_EDITOR["individual"][$lang]/*,
        "selected"  => true,
        "innerHtml" => "<h3>Individus</h3>"*/
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
    global $TOOLS_EDITOR, $TOOLS_EDITOR_INDIVIDUAL;
  
    # language
    $lang = getLang();

    # is admin
    if( !$isAdmin = $_SESSION["editor"]["admin"] ) {
      Includer::add( array( "tag", "fnEdit", "uiDialog" ) );
      return array(
        "dialog" => ui_Dialog::buildXml( $PERMISSION["title"][$lang], $PERMISSION["message"][$lang] ),
        "replacement" => array(
          "query" => "#main",
          "innerHtml" => fn_edit::getMain( $lang ) 
        )
      );
    }

    # params
    $params = array(
      "id"          => "editorIndividualList",
      "mode"      => array(
        "table"   => "Tableau",
        "compact" => "Compacte",
        "tree"    => "Arbre",
        "gallery" => "Galerie"
      ),
      "headtitle" => $TOOLS_EDITOR["individual"][$lang],
      "primary"   => "k",
      "order"     => "k",
      "columns"   => array(
        "k"        => array(
          "label"    => $TOOLS_EDITOR_INDIVIDUAL["k"][$lang],
          "hidden" => true
        ),
        "username" => array(
          "label"    => $TOOLS_EDITOR_INDIVIDUAL["username"][$lang],
          "sortable" => true,
          "filtrable" => true
        ),
        "active"   => array(
          "label" => $TOOLS_EDITOR_INDIVIDUAL["active"][$lang]
        ),
        "admin"    => array(
          "label" => $TOOLS_EDITOR_INDIVIDUAL["admin"][$lang],
          "sortable" => true
        ),
        "longname" => array(
          "label"  => $TOOLS_EDITOR_INDIVIDUAL["longname"][$lang],
          "sortable" => true,
          "filtrable" => true
        )
      )
    );

    # list
    Includer::add( array( "dbEditor", "uiList" ) );
    return array(
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => ui_List::buildXml( $params, db_Editor::get( array_keys( $params["columns"] ), $params["order"] ) )
      ),
    );
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
          "innerHtml" => fn_edit::getMain( $lang ) 
        )
      );
  }
}
