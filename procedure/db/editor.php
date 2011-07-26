<?php
class db_Editor extends db_Abstract {
  public static $table = "editor";
  public static $emptyValues = array( "active" => 0, "admin" => 0, "toolList" => "''" );
  protected static $infoField = array( "k", "username", "admin", "active", "longname", "lang", "toolList" );


  /****************************************************************************/
  public static function getInfo( $username, $password = false ) {
    $where = array( "username LIKE '$username'" );
    if( $password ) {
      $where[] = "password = PASSWORD( '$password' )";
    }
    $result = DB::select( array(
      "field" => self::$infoField,
      "table" => self::$table,
      "where" => $where
    ) );
    if( !$result ) {
      return false;
    }
    $editor = $result[0];

    # get editor in group list
    $editor["toolList"] = self::getGroupToolList( $editor["k"], explode( ",", $editor["toolList"] ) );

    # get
    return $editor;
  }

  /****************************************************************************/
  public static function getInfoByK( $k ) {
    $result = DB::select( array(
      "field" => self::$infoField,
      "table" => self::$table,
      "where" => "k = $k"
    ) );
    if( !$result ) {
      return false;
    }
    $editor = $result[0];

    # get editor in group list
    $editor["toolList"] = self::getGroupToolList( $editor["k"], explode( ",", $editor["toolList"] ) );

    # get
    return $editor;
  }

  /****************************************************************************/
  public static function getGroupKList( $k ) {
    $activeKList = array();
    Includer::add( array( "dbGroupEditor", "dbAssociation" ) );
    $groupKList = db_Association::get( "groupEditor", "editor", $k );
    if( $kList = array_map( function( $item ) {
        return $item["k"];
      }, $groupKList ) ) {
      $kList = db_GroupEditor::getParentKList( $kList );
      $result = db_GroupEditor::get( "k", array( "k IN ( " . join( ",", $kList ) . " )", "active=1") );
      $activeKList = array_map( function( $item ) {
        return $item["k"];
      }, $result );
    }
    return $activeKList;
  }
#TODO active
  /****************************************************************************/
  protected static function getGroupToolList( $k, $toolList ) {
    Includer::add( array( "dbGroupEditor", "dbAssociation" ) );
    $groupKList = db_Association::get( "groupEditor", "editor", $k );
    if( $kList = array_map( function( $item ) {
        return $item["k"];
      }, $groupKList ) ) {
      $kList = db_GroupEditor::getParentKList( $kList );
      $result = db_GroupEditor::get( "toolList", array( "k IN ( " . join( ",", $kList ) . " )", "active=1") );
      foreach( $result as $item ) {
        if( $item["toolList"] ) {
          $toolList = array_unique( array_merge( $toolList, explode( ",", $item["toolList"] ) ) );
        }
      }
    }
    return $toolList;
  }
}
