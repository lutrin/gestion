<?php
class fn_Edit {
  /****************************************************************************/
  public static function display() {
    global $DEFAULT_LANG, $CHARSET, $APP;
    Includer::add( "uiFrame" );

    # language
    $lang = $DEFAULT_LANG; /*TODO get language from user config*/

    # title
    $title = $APP["name"][$lang] . "&nbsp;-&nbsp;" . $APP["site"];

    # params
    $params = array(
      "lang"        => $lang, 
      "charset"     => $CHARSET,
      "title"       => $title,
      "description" => $APP["desc"][$lang],
      "author"      => $APP["author"],
      "meta"        => $APP["meta"],
      "stylesheet"  => $APP["stylesheet"],
      "head_script" => $APP["head_script"],
      "body_script" => $APP["body_script"],
      "body" => self::getBody( $lang, $title )
    );

    # frame
    return ui_Frame::buildHtml( $params );
  }

  /****************************************************************************/
  protected static function getBody( $lang, $title ) {
    global $MSG_NOSCRIPT, $EDITOR, $EDIT;
    return ui_Frame::replaceFields( array(
      "lang"          => $lang,
      "title"         => $title,
      "headerButtons" => " ",
      "noscript"      => $MSG_NOSCRIPT[$lang],
      "main"          => self::getMain( $lang ),
      "copyright"     => $EDITOR["copyright"],
      "help"          => $EDITOR["help"][$lang],
      "condition"     => $EDITOR["condition"][$lang],
      "about"         => $EDITOR["about"][$lang]
    ), file_get_contents( $EDIT ) );
  }

  /****************************************************************************/
  public static function getMain( $lang ) {

    # login form
    if( !$connected = fn_Login::isConnected() ) {
      return fn_login::getForm( $lang );
    }

    # editor
    return "Afficher l'environnement d'édition";
  }
}
