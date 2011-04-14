<?php
class ui_List {
  public static function buildXml( $params, $items ) {
    Includer::add( "tag" );

    # initialize
    $attributes = array();
    $innerHtml = array();

    # mode
    if( isset( $params["mode"] ) ) {
      foreach( $params["mode"] as $key => $mode ) {
        $innerHtml[] = Tag::build( "ui.mode", array( "name" => $key ), $mode );
      }
    }

    # id
    $id = "";
    if( isset( $params["id"] ) ) {
      $id = $params["id"];
    }

    # headtitle
    if( isset( $params["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "ui.headtitle", false, $params["headtitle"] );
    }

    # primary
    $primary = false;
    if( isset( $params["primary"] ) ) {
      $primary = $params["primary"];
    }

    # columns
    foreach( $params["columns"] as $key => $column ) {
      if( !$primary ) {
        $primary = $key;
      }
      $colAttribute = $column;
      unset( $colAttribute["label"] );
      unset( $colAttribute["field"] );
      $innerHtml[] = Tag::build( "ui.headercolumn", $colAttribute, $column["label"] );
    }

    # actions
    foreach( $params["actions"] as $key => $action ) {
      $colAttribute = $action;
      $colAttribute["key"] = $key;

      unset( $colAttribute["label"] );
      $innerHtml[] = Tag::build( "ui.action", $colAttribute, ( isset( $action["label"] )? $action["label"]: false ) );
    }

    # attributes list
    foreach( $params as $key => $param ) {
      if( !in_array( $key, array( "order", "columns", "primary", "headtitle", "mode", "field", "actions" ) ) ) {
        $attributes[$key] = $param;
      }
    }

    # items
    foreach( $items as $item ) {
      $innerHtml[] = self::getItem( $id, $primary, $item );
    }

    return Tag::build( "ui.list", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getItem( $formId, $primary, $fields ) {
    $attributes["id"] = $fields[$primary];
    $innerHtml = array();
    foreach( $fields as $key => $value ) {
      $attribute = array( "key" => $key );
      if( is_numeric( $value ) ) {
        $attribute["class"] = "numeric";
      }
      $innerHtml[] = Tag::build( "ui.cell", $attribute, $value );
    }
    return Tag::build( "ui.row", $attributes, $innerHtml );
  }
}
