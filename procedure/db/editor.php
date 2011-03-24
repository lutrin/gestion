<?php
class db_Editor {
  public static $table = "editor";

  /****************************************************************************/
  public static function getInfo( $username, $password ) {
    /*$result = DB::select( array(
      "table" => self::$table,
      "where" => array(
        "username LIKE '$username'",
        "password = PASSWORD( '$password' )"
      )
    ) );*/
    return false;
  }
}
