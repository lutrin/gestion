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
  global $adminMode, $pageMode, $CONTROLLER, $DEFAULT_LANG;

  # set includer
  Includer::add( array( "typeValidator", "tokenizer", "fnLogin" ) );

  # set default entry
  $msg = $CONTROLLER["wrongentry"][$DEFAULT_LANG];
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
            return logout( $CONTROLLER["disconnected"][$DEFAULT_LANG] );
          } elseif( $action == "displaySetting" ) {
            return displaySetting();
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
  global $CONTROLLER, $DEFAULT_LANG;
  if( ( $username = ( isset( $_GET["username"] )? $_GET["username"]: false ) ) &&
      ( $password = ( isset( $_GET["password"] )? $_GET["password"]: false ) ) &&
      ( $token    = ( isset( $_GET["token"] )?    $_GET["token"]:    false ) ) ) {
    setHeader( "json" );
    return json_encode( fn_Login::connect( array(
      "username" => $username,
      "password" => $password,
      "token"    => $token
    ) ) );
  } else {
    return fn_logout::disconnect( $CONTROLLER["wrongentry"][$DEFAULT_LANG] );
  }
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
function e( $item ) {
  error_log( print_r( $item ), 1 );
}
