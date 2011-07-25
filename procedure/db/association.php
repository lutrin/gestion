<?php
class db_Association {
  public static $table = "association";

  /****************************************************************************/
  public static function save( $params ) {
    if( count( $params ) != 2 ) {
      return false;
    }
    $keys = array_keys( $params );
    list( $table1, $table2 ) = ( $keys[1] < $keys[0] )? array( $keys[1], $keys[0] ): array( $keys[0], $keys[1] );
    $table1K = DB::ensureArray( $params[$table1] );
    $table2K = DB::ensureArray( $params[$table2] );

    # delete
    DB::delete( array(
      "table" => static::$table,
      "where" => array(
        "table1 = '$table1'",
        "table2 = '$table2'",
        "("
          . ( $table1K? "table1K IN (" . join( ",", DB::ensureArray( $table1K ) ) . ")": 1 )
          . " OR "
          . ( $table2K? "table2K IN (" . join( ",", DB::ensureArray( $table2K ) ) . ")": 1 )
        . ")"
      )
    ) );

    # insert
    if( $table1K && $table2K ) {
      $valuesList = array();
      foreach( $table1K as $fK ) {
        foreach( $table2K as $tK ) {
          $valuesList[] = array( $table1, $fK, $table2, $tK );
        }
      }
      DB::insert( array(
        "table"     => static::$table,
        "field"     => "table1, table1K, table2, table2K",
        "valuesList" => $valuesList
      ) );
    }
  }

  /****************************************************************************/
  public static function remove( $table, $kList ) {
    $k = join( ",", DB::ensureArray( $kList ) );
    return DB::delete( array(
      "table" => static::$table,
      "where" => "( table1 = '$table' AND table1K IN ( $k ) ) OR ( table2 ='$table' AND table2K IN ( $k ) )"
    ) );
  }

  /****************************************************************************/
  public static function get( $want, $from, $k ) {
#TODO ensureArray $k
    if( $from < $want ) {
      return DB::select( array(
        "field" => "table2K AS k",
        "table" => static::$table,
        "where" => array(
          "table1k=$k",
          "table2='$want'",
          "table1='$from'"
        )
      ) );
    }
    return DB::select( array(
      "field" => "table1K AS k",
      "table" => static::$table,
      "where" => array(
        "table2k=$k",
        "table1='$want'",
        "table2='$from'"
      )
    ) );
  }
}
