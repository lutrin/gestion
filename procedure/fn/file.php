<?php
class fn_File {
  protected static $idList = "files";  

  /****************************************************************************/
  public static function displayList() {
    global $PERMISSION, $TOOLS;

    # language
    $lang = getLang();

    # is admin
    /*if( !$isAdmin = $_SESSION["editor"]["admin"] ) {*/
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
