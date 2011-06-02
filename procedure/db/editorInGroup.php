<?php
class db_EditorInGroup extends db_Abstract {
  public static $table = "editorInGroup";

  /****************************************************************************/
  public static function saveEditorList( $editorKList, $groupK ) {
    DB::delete( array(
      "table" => static::$table,
      "where" => array(
        "groupK = $groupK"
      )
    ) );

    if( $editorKList ) {
      $valueList = array();
      foreach( $editorKList as $editorK ) {
        $valueList[] = array( $editorK, $groupK );
      }
      DB::insert( array(
        "table"     => static::$table,
        "field"     => "editorK, groupK",
        "valuesList" => $valueList
      ) );
    }
  }

  /****************************************************************************/
  public static function removeEditor( $kList ) {
    return DB::delete( array(
      "table" => static::$table,
      "where" => "editorK IN (" . join( ",", $kList ) . ")"
    ) );
  }

  /****************************************************************************/
  public static function removeGroup( $kList ) {
    return DB::delete( array(
      "table" => static::$table,
      "where" => "groupK IN (" . join( ",", $kList ) . ")"
    ) );
  }
}
