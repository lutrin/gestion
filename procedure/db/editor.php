<?php
class db_Editor extends db_Abstract {
  public static $table = "editor";

  /****************************************************************************/
  public static function getEmptyValues() {
    return array( "active" => 0, "admin" => 0, "toolList" => "''" );
  }

  /****************************************************************************/
  public static function getInfo( $username, $password = false ) {
    $where = array( "username LIKE '$username'" );
    if( $password ) {
      $where[] = "password = PASSWORD( '$password' )";
    }
    $result = DB::select( array(
      "field" => array( "k", "username", "admin", "active", "longname", "lang", "toolList" ),
      "table" => self::$table,
      "where" => $where
    ) );
    if( !$result ) {
      return false;
    }
    $editor = $result[0];
    $toolList = explode( ",", $editor["toolList"] );

    # get editor in group list
    Includer::add( array( "dbGroupEditor", "dbEditorInGroup" ) );
    $groupKList = db_EditorInGroup::get( "groupK", "editorK = {$editor['k']}" );
    if( $kList = array_map( function( $item ) {
        return $item["groupK"];
      }, $groupKList ) ) {
      $kList = db_GroupEditor::getParentKList( $kList );
      $result = db_GroupEditor::get( "toolList", "k IN ( " . join( ",", $kList ) . " ) " );
      foreach( $result as $item ) {
        if( $item["toolList"] ) {
          $toolList = array_unique( array_merge( $toolList, explode( ",", $item["toolList"] ) ) );
        }
      }
    }
    $editor["toolList"] = $toolList;

    # get
    return $editor;
  }

  /****************************************************************************/
  public static function remove( $kList ) {

    # remove editor in group
    Includer::add( "dbEditorInGroup" );
    db_EditorInGroup::removeEditor( $kList );

    # remove editor    
    return parent::remove( $kList );
  }
}
