<?php
# start session
if( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) ) {
  ob_start("ob_gzhandler");
} else {
  ob_start();
}
session_start();

# include
include "config.php";
include "../../library/procedure/includer.php";

echo main();
exit;

/******************************************************************************/
function main() {
  global $adminMode, $pageMode, $CONTROLLER;

  # set includer
  Includer::add( array( "typeValidator", "tokenizer", "fnLogin" ) );

  # language
  $lang = getLang();

  # set default entry
  $msg = $CONTROLLER["wrongentry"][$lang];
  $connected = fn_Login::isConnected();

  # switch action
  if( $adminMode ) {
    return displayEdit();
  } else {
    if( $pageMode ) {
      return getPage();
    } else {
      if( $connected ) {
        if( $action = isset( $_GET["action"] )? typeValidator::isAlphaNumeric( $_GET["action"] ): false ) {
          if( $action == "logout" ) {
            return logout( $CONTROLLER["disconnected"][$lang] );
          } elseif( $action == "displaySetting" ) {
            return displaySetting();
          } elseif( $action == "getContent" ) {
            return getContent();
          } elseif( $action == "save" ) {
            return save();
          }
        }
        return logout( $msg );
      } else {
        if( $action = isset( $_GET["action"] )? typeValidator::isAlphaNumeric( $_GET["action"] ): false ) {
          if( $action == "login" ) {
            return login();
          }
        }
      }
    }
  }
  return $msg;
}

/******************************************************************************/
function login() {
  global $CONTROLLER;
  if( ( $username = ( isset( $_GET["username"] )? $_GET["username"]: false ) ) &&
      ( $password = ( isset( $_GET["password"] )? $_GET["password"]: false ) ) &&
      ( $token    = ( isset( $_GET["token"] )?    $_GET["token"]:    false ) ) ) {
    setHeader( "json" );
    return json_encode( fn_Login::connect( array(
      "username" => $username,
      "password" => $password,
      "token"    => $token
    ) ) );
  }
  $lang = getLang();
  return logout( $CONTROLLER["wrongentry"][$lang] );
}

/******************************************************************************/
function displayEdit() {
  Includer::add( array( "clean", "fnEdit" ) );
  setHeader();
  return fn_Edit::display();
}

/******************************************************************************/
function logout( $msg = "" ) {
  setHeader( "json" );
  return json_encode( fn_Login::disconnect( $msg ) );
}

/******************************************************************************/
function displaySetting() {
  Includer::add( "fnSetting" );
  setHeader( "json" );
  return json_encode( fn_Setting::display() );
}

/******************************************************************************/
function getContent() {
  global $CONTROLLER;
  if( $id = ( isset( $_GET["id"] )? typeValidator::isAlphaNumeric( $_GET["id"] ): false ) ) {
    setHeader( "json" );
    if( $id == "editors" ) {
      Includer::add( "fnEditor" );
      return json_encode( fn_Editor::displayList() );
    }
  }
  $lang = getLang();
  return logout( $CONTROLLER["wrongentry"][$lang] );
}

/******************************************************************************/
function save() {
  global $CONTROLLER;
  if( ( $k = ( isset( $_GET["k"] )? typeValidator::isNumeric( $_GET["k"] ): false ) ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) &&
      ( $token =  ( isset( $_GET["token"] )?    $_GET["token"]: false ) ) ) {
    setHeader( "json" );

    # setting
    if( $object == "setting" ) {
      Includer::add( "fnSetting" );
      return json_encode( fn_Setting::save( $k, $token ) );
    }    
  }
  $lang = getLang();
  return logout( $CONTROLLER["wrongentry"][$lang] );
}

/******************************************************************************/
function callCleanAll() {
  Includer::add( "clean" );
  $clean = cleanAll();
  return displayForm();
}

/******************************************************************************/
function getNow( $format = "Y-m-d h:i:s" ) {
  return date( $format, time() );
}

/******************************************************************************/
function setHeader( $type = "html" ) {
  global $CHARSET;
  $typeList = array(
    "html" => "Content-Type: text/html; charset=$CHARSET",
    "json" => "Content-Type: application/json; charset=$CHARSET"
  );
  return header( in_array( $type, array_keys( $typeList ) )? $typeList[$type]: $typeList["html"] );
}

/******************************************************************************/
function getLang() {
  global $DEFAULT_LANG;
  return isset( $_SESSION["editor"]["lang"] )? $_SESSION["editor"]["lang"]: $DEFAULT_LANG;
}

/******************************************************************************/
function replaceFields( $fields, $content ) {
  return str_replace(
    array_map(
      function( $key ) {
        return "###$key###";
      },
      array_keys( $fields )
    ),
    array_values( $fields ),
    $content
  );
}

/******************************************************************************/
function e( $item ) {
  error_log( print_r( $item ), 1 );
}
