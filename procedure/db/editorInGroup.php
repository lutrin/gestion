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
error_log( print_r( $valueList, 1 ) );
      DB::insert( array(
        "table"     => static::$table,
        "field"     => "editorK, groupK",
        "valuesList" => $valueList
      ) );
    }
  }
}
