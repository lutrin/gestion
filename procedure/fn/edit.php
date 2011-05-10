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
      "body" => self::getBody( $title )
    );

    # frame
    return ui_Frame::buildHtml( $params );
  }

  /****************************************************************************/
  protected static function getBody( $title ) {
    global $MSG_NOSCRIPT, $FOOTERLINK, $EDIT;
    $lang = getLang();
    return replaceFields( array(
      "lang"          => $lang, 
      "title"         => $title,
      "headerButtons" => self::getHeaderButton(),
      "noscript"      => $MSG_NOSCRIPT[$lang],
      "main"          => self::getMain(),
      "copyright"     => $FOOTERLINK["copyright"],
      "help"          => $FOOTERLINK["help"][$lang],
      "condition"     => $FOOTERLINK["condition"][$lang],
      "about"         => $FOOTERLINK["about"][$lang]
    ), file_get_contents( $EDIT ) );
  }

  /****************************************************************************/
  public static function getMain( $msg = "" ) {
    global $TOOLS;

    # login form
    if( !$connected = fn_Login::isConnected() ) {
      return fn_login::buildForm( $msg );
    }

    $lang = getLang();

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
      ),
      "visitors" => array(
        "label"  => $TOOLS["visitors"][$lang]
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
    Includer::add( "uiNav" );
    $params = array(
      "id"   => "toolList",
      "mode" => "dock",
      "headtitle" => $TOOLS["headtitle"][$lang]
    );
    return ui_Nav::buildXml( $params, $allowedToolList )
         . Tag::build( "div", array( "id" => "details" ), " " );
  }

  /****************************************************************************/
  public static function getHeaderButton() {
    global $HEADER_BUTTONS;

    # empty
    if( !$connected = fn_Login::isConnected() ) {
      return " ";
    }

    $lang = getLang();

    # editor
    Includer::add( "tag" );
    return Tag::build( "span", array( "id" => "currentUser" ), $_SESSION["editor"]["longname"] )
         . Tag::build(
            "button",
            array(
              "id"          => "displaySetting",
              "title"       =>  $HEADER_BUTTONS["setting"][$lang],
              "data-action" => "displaySetting"
            ),
            Tag::build( "span", array( "class" => "hidden" ), $HEADER_BUTTONS["setting"][$lang] )
          )
         . Tag::build(
            "button",
            array(
              "id"          => "logout",
              "title"       =>  $HEADER_BUTTONS["logout"][$lang],
              "data-action" => "logout"
            ),
            Tag::build( "span", array( "class" => "hidden" ), $HEADER_BUTTONS["logout"][$lang] )
          );
  }
}
