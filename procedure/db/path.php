<?php
class db_Path extends db_Abstract {
  public static $table = "path";

  /****************************************************************************/
  public static function getK( $path ) {
    if( !$result = self::get( "k", "path='$path'" ) ) {
error_log( $path );
      $result = self::save( array( "path" => "'$path'" ) );
      return $result[0];
    }
    return $result[0]["k"];
  }

  /****************************************************************************/
  public static function getPath( $k = 0 ) {
    if( !$k || !$result = self::get( "path", "k=$k" ) ) {
      return "";
    }
    return $result[0]["path"];
  }
}
