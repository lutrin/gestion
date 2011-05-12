<?php
abstract class db_Abstract {

  /****************************************************************************/
  public static function get( $fields, $where = false, $orders = false ) {
    $query = array(
      "field" => $fields,
      "table" => static::$table
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
      "table" => static::$table
    );
    if( $where ) {
      $query["where"] = $where;
    }
    return DB::count( $query );
  }

  /****************************************************************************/
  public static function defaults() {
    $fields = DB::getInfo( static::$table );
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
        "table"   => static::$table,
        "set"     => $values,
        "noquote" => true,
        "where"   => "k=$k"
      ) );
    
    # insert
    } else {
      return DB::insert( array(
        "table"   => static::$table,
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
      "table" => static::$table,
      "where" => "k IN (". join( ",", $kList ) . ")"
    ) );
  }
}
