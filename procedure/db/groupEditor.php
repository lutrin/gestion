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
}
