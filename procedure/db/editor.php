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
  public static function get( $fields, $where = false, $orders = false ) {
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
  public static function count( $fields, $where = false ) {
    $query = array(
      "field" => $fields,
      "table" => self::$table
    );
    if( $where ) {
      $query["where"] = $where;
    }
    return DB::count( $query );
  }

  /****************************************************************************/
  public static function defaults() {
    $fields = DB::getInfo( self::$table );
    $list = array();
    foreach( $fields as $field ) {
      if( isset( $field["Default"] ) ) {
        $key = $field["Field"];
        $list[$field["Field"]] = $field["Default"];
      }
    }
    return $list;
  }

  /****************************************************************************/
  public static function save( $values, $k = false ) {
    if( !$values ) {
      return false;
    }

    # update
    if( $k ) {
      return DB::update( array(
        "table"   => self::$table,
        "set"     => $values,
        "noquote" => true,
        "where"   => "k=$k"
      ) );
    
    # insert
    } else {
      return DB::insert( array(
        "table"   => self::$table,
        "field"   => array_keys( $values ),
        "noquote" => true,
        "values"  => array_values( $values ),
        "return"  => "id"
      ) );
    }
  }

  /****************************************************************************/
  public static function remove( $kList ) {
    return DB::delete( array(
      "table" => self::$table,
      "where" => "k IN (". join( ",", $kList ) . ")"
    ) );
  }
}
