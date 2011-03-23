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
  global $adminMode, $pageMode;

  # set includer
  Includer::add( array( "typeValidator", "tokenizer", "fnLogin" ) );

  # set default entry
  $msg = "Mauvaise entrée";
  $connected = fn_Login::isConnected();

  # switch action
  if( $adminMode ) {
    return displayEdit();
  } else {
    if( $pageMode ) {
      return getPage();
    } else {
      if( $connected ) {
        return logout();
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
  if( ( $user     = ( isset( $_GET["username"] )? $_GET["username"]: false ) ) &&
      ( $password = ( isset( $_GET["password"] )? $_GET["password"]: false ) ) &&
      ( $token    = ( isset( $_GET["token"] )?    $_GET["token"]:    false ) ) ) {
    setHeader( "json" );
    return json_encode( fn_Login::connect( array(
      "username" => $username,
      "password" => $password,
      "token"    => $token
    ) ) );
  } else {
    return "Mauvaise entrée";
  }
}

/******************************************************************************/
function displayEdit() {
  Includer::add( array( "clean", "fnEdit" ) );
  setHeader();
  return fn_Edit::display();
}

/******************************************************************************/
function prepareEdit() {
  Includer::add( "uiEdit" );
  $edit = new UIEdit();
  return str_replace(
    array( "###main###", "###header-buttons###" ),
    array( $edit->build(), $edit->buttons() ),
    file_get_contents( "../template/edit.html" )
  );
}

/******************************************************************************/
function prepareLogin() {
  Includer::add( "uiLogin" );
  $login = new UILogin();
  return str_replace(
    array( "###main###", "###header-buttons###" ),
    array( $login->build(), "" ),
    file_get_contents( "../template/edit.html" )
  );
}

/******************************************************************************/
function logout() {
  /*$login = new Login();
  setHeader( "json" );
  return json_encode( $login->disconnect() );*/
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
