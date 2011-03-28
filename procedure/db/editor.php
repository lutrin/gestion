<?php
class db_Editor {
  public static $table = "editor";

  /****************************************************************************/
  public static function getInfo( $username, $password ) {
    $result = DB::select( array(
      "field" => array( "username", "admin", "active", "longname" ),
      "table" => self::$table,
      "where" => array(
        "username LIKE '$username'",
        "password = PASSWORD( '$password' )"
      )
    ) );
    return $result? $result[0]: false;
  }
}
