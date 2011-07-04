<?php
class db_GroupEditor extends db_Abstract {
  public static $table = "groupEditor";

  /****************************************************************************/
  public static function getEmptyValues() {
    return array( "active" => 0, "toolList" => "''" );
  }

  /****************************************************************************/
  public static function remove( $kList ) {

    # get child k list
    $newKList = self::getChildKList( $kList );

    # remove editor in group
    Includer::add( "dbEditorInGroup" );
    db_EditorInGroup::removeGroup( $newKList );

    # remove editor    
    return parent::remove( $newKList );
  }

  /****************************************************************************/
  public static function getParentInfoList( $k ) {

    # get parent k list
    if( ( !$parentKList = self::getParentKList( array( $k ) ) ) ||
        ( count( $parentKList ) < 2 ) ) {
      return false;
    }

    # remove k
    array_shift( $parentKList );

    # get name
    $parentInfoList = array_fill_keys( array_reverse( $parentKList ), false );
    foreach( self::get( array( "k", "name" ), "k IN (" . join( ",", $parentKList ) . ")" ) as $item ) {
      $parentInfoList[$item["k"]] = $item["name"];
    }
    return $parentInfoList;
  }
}
