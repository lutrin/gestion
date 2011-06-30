<?php

# error log
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

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
          /*} elseif( $action == "edit" ) {
            return edit();
          } elseif( $action == "rename" ) {
            return re_Name();
          } elseif( $action == "insert" ) {
            return insert();*/
          } elseif( in_array( $action, array( "edit", "insert", "activate", "unactivate", "use" ) ) ) {
            return itemAction( $action );
          } elseif( in_array( $action, array( "add", "refresh" ) ) ) {
            return listAction( $action );
          } elseif( $action == "pick" ) {
            return pick();
          } elseif( $action == "delete" ) {
            return delete();
          } elseif( $action == "setAccountStorage" ) {
            return setAccountStorage();
          } elseif( $action == "removeAccountStorage" ) {
            return removeAccountStorage();
          } elseif( $action == "upload" ) {
            return upload();
          }
        } elseif( $action = isset( $_POST["action"] )? typeValidator::isAlphaNumeric( $_POST["action"] ): false ) {
          if( $action == "save" ) {
            return save();
          }
        }
        return logout( $msg );
      } else {
        if( $action = isset( $_POST["action"] )? typeValidator::isAlphaNumeric( $_POST["action"] ): false ) {
          if( $action == "login" ) {
            return login();
          }
          return logout( $CONTROLLER["notconnected"][$lang] );
        }
      }
    }
  }
  return $msg;
}

/******************************************************************************/
function login() {
  global $CONTROLLER;
  if( ( $username = ( isset( $_POST["username"] )? $_POST["username"]: false ) ) &&
      ( $password = ( isset( $_POST["password"] )? $_POST["password"]: false ) ) &&
      ( $token    = ( isset( $_POST["token"] )?    $_POST["token"]:    false ) ) ) {
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
  if( $name = ( isset( $_GET["name"] )? $_GET["name"]: false ) ) {
    $value = ( isset( $_GET["value"] )? $_GET["value"]: false );
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
  if( ( $object = ( isset( $_POST["object"] )? typeValidator::isAlphaNumeric( $_POST["object"] ): false ) ) &&
      ( $token =  ( isset( $_POST["token"] )? $_POST["token"]: false ) ) ) {
    if( $object == "file-folder" ) {
      return switchFunction( "save", $object, array( $token ) );
    }
    if( isset( $_POST["k"] ) ) {
      return switchFunction( "save", $object, array( $_POST["k"], $_POST ) );
    }
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function upload() {
  global $CONTROLLER;
  if( ( $targetK = ( isset( $_GET["targetK"] )? typeValidator::isNumeric( $_GET["targetK"] ): false ) ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) &&
      ( $token =  ( isset( $_GET["token"] )? $_GET["token"]: false ) ) ) {
    return switchFunction( "upload", $object, array( $_GET ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function itemAction( $action ) {
  global $CONTROLLER;
  if( ( $k = ( isset( $_GET["k"] )? typeValidator::isNumeric( $_GET["k"] ): false ) ) &&
      ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( $action, $object, array( $k ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function listAction( $action ) {
  global $CONTROLLER;
  if( ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) ) {
    return switchFunction( $action, $object, array( $object ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function pick() {
  global $CONTROLLER;
  if( ( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) && 
      ( $for = ( isset( $_GET["for"] )? $_GET["for"]: false ) ) ) {
    $kList = ( isset( $_GET["k"] )? typeValidator::isNumericList( $_GET["k"] ): false );
    return switchFunction( "pick", $object, array( $kList, $for ) );
  }
  return logout( $CONTROLLER["wrongentry"][getLang()] );
}

/******************************************************************************/
function delete() {
  global $CONTROLLER;
  if( $object = ( isset( $_GET["object"] )? typeValidator::isAlphaNumeric( $_GET["object"] ): false ) ) {
    if( $kList = ( isset( $_GET["k"] )? typeValidator::isNumericList( $_GET["k"] ): false ) ) {
      return switchFunction( "delete", $object, array( $kList, $object ) );
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
  $sessionEditor = fn_Login::getSessionEditor();
  return isset( $sessionEditor["lang"] )? $sessionEditor["lang"]: $DEFAULT_LANG;
}

/******************************************************************************/
function getData( $name ) {
  global $DATAPATH;
  $filename = $DATAPATH . $name . ".js";
  if( file_exists( $filename ) ) {
    return json_decode( file_get_contents( $filename ), true );
  }
  return false;
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
  error_log( print_r( $item, 1 ) );
}
