<?php
class db_Editor extends db_Abstract {
  public static $table = "editor";

  /****************************************************************************/
  public static function getEmptyValues() {
    return array( "active" => 0, "admin" => 0, "toolList" => "" );
  }

  /****************************************************************************/
  public static function getInfo( $username, $password = false ) {
    $where = array( "username LIKE '$username'" );
    if( $password ) {
      $where[] = "password = PASSWORD( '$password' )";
    }
    $result = DB::select( array(
      "field" => array( "k", "username", "admin", "active", "longname", "lang" ),
      "table" => self::$table,
      "where" => $where
    ) );
    return $result? $result[0]: false;
  }
}
