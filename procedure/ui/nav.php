<?php
class ui_Nav {
  public static function buildXml( $params, $items ) {

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

    # headtitle
    if( isset( $params["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "ui.headtitle", false, $params["headtitle"] );
      unset( $params["headtitle"] );
    }

    # bread crumb
    if( isset( $params["breadcrumb"] ) ) {
      $innerHtml[] = $params["breadcrumb"];
      unset( $params["breadcrumb"] );
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
    $innerHtml = false;
    if( isset( $attributes["innerHtml"] ) ) {
      $innerHtml = $attributes["innerHtml"];
      unset( $attributes["innerHtml"] );
    } else {
      $attributes["empty"] = true;
    }

    return Tag::build( "ui.item", $attributes, $innerHtml );
  }
}
