<?php
class ui_Nav {
  public static function buildXml( $params, $items ) {
    Includer::add( "tag" );

    # initialize
    $attributes = array();
    $innerHtml = array();

    # mode
    $mode = "anchors";
    if( isset( $params["mode"] ) ) {
      $mode = $params["mode"];
      unset( $params["mode"] );
    }

    # id
    $id = "";
    if( isset( $params["id"] ) ) {
      $id = $params["id"];
    }

    # attributes list
    foreach( $params as $key => $param ) {
      $attributes[$key] = $param;
    }

    # items
    foreach( $items as $key => $item ) {
      $innerHtml[] = self::getItem( $id, $key, $item );
    }

    return Tag::build( "ui.$mode", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getItem( $formId, $fieldId, $attributes ) {
    $attributes["id"] = "$formId-$fieldId";
    $attributes["href"] = $fieldId;

    # inner html
    $innerHtml = "";
    if( isset( $attributes["innerHtml"] ) ) {
      $innerHtml = $attributes["innerHtml"];
      unset( $attributes["innerHtml"] );
    }

    return Tag::build( "ui.item", $attributes, $innerHtml );
  }
}
