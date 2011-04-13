<?php
class fn_Edit {
  /****************************************************************************/
  public static function display() {
    global $CHARSET, $APP;
    Includer::add( "uiFrame" );

    # language
    $lang = getLang();

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
    return replaceFields( array(
      "lang"          => $lang, 
      "title"         => $title,
      "headerButtons" => self::getHeaderButton( $lang ),
      "noscript"      => $MSG_NOSCRIPT[$lang],
      "main"          => self::getMain( $lang ),
      "copyright"     => $EDITOR["copyright"],
      "help"          => $EDITOR["help"][$lang],
      "condition"     => $EDITOR["condition"][$lang],
      "about"         => $EDITOR["about"][$lang]
    ), file_get_contents( $EDIT ) );
  }

  /****************************************************************************/
  public static function getMain( $lang, $msg = "" ) {
    global $TOOLS;

    # login form
    if( !$connected = fn_Login::isConnected() ) {
      return fn_login::buildForm( $lang, $msg );
    }

    # tool list
    $toolList = array(
      "pages" => array(
        "label" => $TOOLS["pages"][$lang]
      ),
      "templates" => array(
        "label" => $TOOLS["templates"][$lang]
      ),   
      "articles" => array(
        "label" => $TOOLS["articles"][$lang]
      ),   
      "files" => array(
        "label" => $TOOLS["files"][$lang]
      ),
      "editors" => array(
        "label"  => $TOOLS["editors"][$lang]
      )
    );

    # is admin
    $isAdmin = $_SESSION["editor"]["admin"];

    # build list
    $allowedToolList = array();
    foreach( $toolList as $key => $tool ) {
      if( $isAdmin ) {
        $allowedToolList[$key] = $tool;
      }
    }

    # params
    $params = array(
      "id"   => "toolList",
      "mode" => "dock",
      "headtitle" => $TOOLS["headtitle"][$lang]
    );

    Includer::add( "uiNav" );
    return ui_Nav::buildXml( $params, $allowedToolList );
  }

  /****************************************************************************/
  public static function getHeaderButton( $lang ) {
    global $HEADER_BUTTONS;

    # empty
    if( !$connected = fn_Login::isConnected() ) {
      return " ";
    }

    # editor
    return "<span id='currentUser'>" . $_SESSION["editor"]["longname"] . "</span>"
         . "<button class='iconic' title='" . $HEADER_BUTTONS["setting"][$lang] . "' data-action='displaySetting'>w</button>"
         . "<button class='iconic' title='" . $HEADER_BUTTONS["logout"][$lang] . "' data-action='logout'>x</button>";
  }
}
