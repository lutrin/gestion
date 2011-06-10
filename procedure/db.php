<?php
abstract class db_Abstract {

  /****************************************************************************/
  public static function getEmptyValues() {
    return array();
  }

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
  public static function getTree( $fields, $parentK = 0, $where = false, $orders = false ) {
    # add parentK
    $whereWithParent = array( "parentK=$parentK" );
    if( $where ) {
      $whereWithParent = array_merge( $whereWithParent, DB::ensureArray( $where ) );
    }
    $result = self::get( $fields, $whereWithParent, $orders );
    foreach( $result as $key => $item ) {
      if( isset( $item["k"] ) ) {
        if( $childList = self::getTree( $fields, $item["k"], $where, $orders ) ) {
          $result[$key]["childList"] = $childList; 
        }
      }
    }
    return $result;
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
      "where" => "k IN (" . join( ",", $kList ) . ")"
    ) );
  }

  /****************************************************************************/
  public static function getChildKList( $kList ) {
    $result = DB::select( array(
      "field" => "k",
      "table" => static::$table,
      "where" => array(
        "parentK IN ( " . join( ",", $kList ) . " )",
        "NOT k IN ( " . join( ",", $kList ) . " )"
      )
    ) );
    $newKList = array();
    if( $result ) {
      $newKList = array_map( function( $item ) {
        return $item["k"];
      }, $result );
      $kList = self::getChildKList( array_merge( $kList, $newKList ) );
    }
    return $kList;
  }

  /****************************************************************************/
  public static function getParentKList( $kList ) {
    $result = DB::select( array(
      "field" => "parentK",
      "table" => static::$table,
      "where" => array(
        "NOT parentK = 0",
        "k IN ( " . join( ",", $kList ) . " )",
        "NOT parentK IN ( " . join( ",", $kList ) . " )"
      )
    ) );
    $newKList = array();
    if( $result ) {
      $newKList = array_map( function( $item ) {
        return $item["parentK"];
      }, $result );
      $kList = self::getParentKList( array_merge( $kList, $newKList ) );
    }
    return $kList;
  }
}
