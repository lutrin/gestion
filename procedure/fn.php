<?php
abstract class fn {
  protected static $idList = "";  

  /****************************************************************************/
  public static function getContent() {
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
  public static function save( $k ) {
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
