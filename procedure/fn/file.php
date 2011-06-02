<?php
class fn_File extends fn {
  protected static $idList = "files";

  /****************************************************************************/
  public static function getContent() {
    global $PERMISSION, $TOOLS, $TOOLS_EDITOR;

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
        "innerHtml" => "en développement..."
      ),
      "hash" => true
    );
  }
}
