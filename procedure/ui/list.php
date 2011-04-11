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
      unset( $params["mode"] );
    }

    # id
    $id = "";
    if( isset( $params["id"] ) ) {
      $id = $params["id"];
    }

    # headtitle
    if( isset( $params["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "ui.headtitle", false, $params["headtitle"] );
      unset( $params["headtitle"] );
    }

    # columns
    foreach( $params["columns"] as $key => $column ) {
      $colAttribute = $column;
      unset( $colAttribute["label" ] );
      $colAttribute["key"] = $key;
      $innerHtml[] = Tag::build( "ui.headercolumn", $colAttribute, $column["label"] );
    }
    unset( $params["columns"] );


    # primary
    $primary = $params["primary"];
    unset( $params["primary"] );

    # attributes list
    foreach( $params as $key => $param ) {
      $attributes[$key] = $param;
    }

    # items
    foreach( $items as $item ) {
      $innerHtml[] = self::getItem( $id, $primary, $item );
    }

    return Tag::build( "ui.list", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getItem( $formId, $primary, $fields ) {
    $rowId = "$formId-" . $fields[$primary];
    $attributes["id"] = $rowId;
    $innerHtml = array();
    foreach( $fields as $key => $value ) {
      $attribute = array( "key" => $key, "id" => "$rowId-$key" );
      /*if( is_numeric( $value ) ) {
        $attribute["class"] = "numeric";
      }*/
      $innerHtml[] = Tag::build( "ui.cell", $attribute, $value );
    }
    return Tag::build( "ui.row", $attributes, $innerHtml );
  }
}
