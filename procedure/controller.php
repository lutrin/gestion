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
          } elseif( $action == "edit" ) {
            return edit();
          } elseif( $action == "insert" ) {
            return insert();
          } elseif( in_array( $action, array( "add", "refresh", "dip" ) ) ) {
            return listAction( $action );
          } elseif( $action == "delete" ) {
            return delete();
          } elseif( $action == "setAccountStorage" ) {
            return setAccountStorage();
          } elseif( $action == "removeAccountStorage" ) {
            return removeAccountStorage();
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
  return logout( $CONTROLLER["wrongentry"][getLang()] );
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
function setAccountStorage() {
  global $CONTROLLER;
  if( ( $name = ( isset( $_GET["name"] )? $_GET["name"]: false ) ) &&
      ( $value = ( isset( $_GET["value"] )? $_GET["value"]: false ) ) ) {
    Includer::add( "fnSetting" );
    return json_encode( fn_Setting::setAccountStorage( $name, $value ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function removeAccountStorage() {
  global $CONTROLLER;
  if( $name = ( isset( $_GET["name"] )? $_GET["name"]: false ) ) {
    Includer::add( "fnSetting" );
    return json_encode( fn_Setting::removeAccountStorage( $name ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function getContent() {
  global $CONTROLLER, $INCLUDE_LIST;
  if( $id = ( isset( $_GET["id"] )? typeValidator::isAlphaNumeric( $_GET["id"] ): false ) ) {
    $idList = explode( "-", $id );
    setHeader( "json" );

    foreach( $INCLUDE_LIST as $key => $include ) {
      if( isset( $include["entries"] ) && in_array( $idList[0], $include["entries"] ) ) {
        Includer::add( $key );
        $function = "getContent" . ( isset( $idList[1] )? ( "_" . $idList[1] ) : "" );
        return json_encode( call_user_func( array( $include["class"], $function ) ) );
        break;
      }
    }
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function save() {
  global $CONTROLLER;
  if( isset( $_GET["k"] ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) &&
      ( $token =  ( isset( $_GET["token"] )? $_GET["token"]: false ) ) ) {
    $k = $_GET["k"];
    return switchFunction( "save", $object, array( $k, $token ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function edit() {
  global $CONTROLLER;
  if( ( $k = ( isset( $_GET["k"] )? typeValidator::isNumeric( $_GET["k"] ): false ) ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( "edit", $object, array( $k ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function insert() {
  global $CONTROLLER;
  if( ( $k = ( isset( $_GET["k"] )? typeValidator::isNumeric( $_GET["k"] ): false ) ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( "insert", $object, array( $k ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function listAction( $action ) {
  global $CONTROLLER;
  if( ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( $action, $object );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
/*function refresh() {
  global $CONTROLLER;
  if( ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( "refresh", $object );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}
*/
/******************************************************************************/
function delete() {
  global $CONTROLLER;
  if( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) {
    if( $kList = ( isset( $_GET["k"] )? typeValidator::isNumericList( $_GET["k"] ): false ) ) {
      return switchFunction( "delete", $object, array( $kList ) );
    }
    setHeader( "json" );
    Includer::add( "uiDialog" );
    return json_encode( array(
      "dialog" => ui_Dialog::buildXml( $CONTROLLER["delete"][getLang()], $CONTROLLER["noitem"][getLang()] )
    ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function switchFunction( $action, $object, $params = false ) {
  global $CONTROLLER, $INCLUDE_LIST;

  # object list
  $objList = explode( "-", $object );
  $obj = array_shift( $objList );

  # find class
  foreach( $INCLUDE_LIST as $key => $include ) {
    if( isset( $include["entries"] ) && in_array( $obj, $include["entries"] ) ) {

      # include class
      Includer::add( $key );
      $function = $action . ( $objList? ( "_" . $objList[0] ): "" );
      setHeader( "json" );

      # with params
      if( $params ) {
        return json_encode( call_user_func_array(
          array( $include["class"], $function ),
          $params
        ) );
      }

      # without params
      return json_encode( call_user_func( array( $include["class"], $function ) ) );
    }
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
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
