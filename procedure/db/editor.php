<?php
class db_Editor {
  public static $table = "editor";

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

  /****************************************************************************/
  public static function get( $fields, $where, $orders = false ) {
    $query = array(
      "field" => $fields,
      "table" => self::$table
    );
    if( $orders ) {
      $query["order"] = $orders;
    }
    if( $where ) {
      $query["where"] = $where;
    }
    return DB::select( $query );
  }

  /****************************************************************************/
  public static function save( $values, $k = false ) {
    if( !$values ) {
      return false;
    }

    # update
    if( $k ) {
      return DB::update( array(
        "table" => self::$table,
        "set"   => $values,
        "where" => "k = $k"
      ) );
    
    # insert
    } else {
    }
  }
}
