<?php
class fn_Editor {
  protected static $idList = "editors";  

  /****************************************************************************/
  public static function displayList() {
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
          "innerHtml" => fn_edit::getMain( getLang() ) 
        )
      );
    }

    $editorsTabList = array(
      "editors-individual" => array(
        "label" => "Individus",
        "class" => "target",
        "innerHtml" => "<h3>Individus</h3>"
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
}
