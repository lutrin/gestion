<?php
class fn_Editor {
  protected static $idList = "editors";  

  /****************************************************************************/
  public static function displayNav() {
    global $PERMISSION, $TOOLS;

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
        "label"     => "Individus"/*,
        "selected"  => true,
        "innerHtml" => "<h3>Individus</h3>"*/
      ),
      "editors-group" => array(
        "label"  => "Groupes"
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
      "mode"        => array(
        "table",
        "compact"
      ),
      "headertitle" => "Individus",
      "columnList" => array( "username", "active", "admin", "longname" )
    );

    # list
    Includer::add( array( "dbEditor", "uiList" ) );
    return array(
      "replacement" => array(
        "query" => "#editors-individual",
        "innerHtml" => ui_List::buildXml( $params, db_Editor::get( $params["columnList"] ) )
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
