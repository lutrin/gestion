<?php
class ui_List {
  public static function buildXml( $params, $items, $partOnly = false ) {

    # initialize
    $attributes = array();
    $innerHtml = array();

    # id
    $id = "";
    if( isset( $params["id"] ) ) {
      $id = $params["id"];
    }

    # mode
    if( isset( $params["mode"] ) ) {
      Includer::add( array( "fnSetting", "uiForm" ) );
      $modeList = array();
      foreach( $params["mode"] as $key => $mode ) {
        $modeList[$key] = array(
          "label" => $mode,
          "value" => $key
        );
      }
      $storedValue = fn_Setting::getAccountStorage( "$id-mode" );
      $modeKeyList = array_keys( $params["mode"] );
      $storedValue = $storedValue? $storedValue: $modeKeyList[0];
      $innerHtml[] = Tag::build(
        "ui.option",
        array( "value" => $storedValue, "count" => count( $params["mode"] ) ),
        ui_Form::getField(
          $id,
          "mode",
          array(
            "class" => "mode",
            "label" => "Mode",
            "type"  => "select",
            "list"  => $modeList
          ),
          array( "mode" => $storedValue )
        )
      );
    }

    # primary
    $primary = false;
    if( isset( $params["primary"] ) ) {
      $primary = $params["primary"];
    }

    # columns
    $sortableList = array();
    foreach( $params["columns"] as $key => $column ) {
      if( !$primary ) {
        $primary = $key;
      }
      $colAttribute = $column;
      unset( $colAttribute["label"] );
      unset( $colAttribute["field"] );
      $colAttribute["id"] = $key;
      $innerHtml[] = Tag::build( "ui.headercolumn", $colAttribute, $column["label"] );

      # sortable
      if( isset( $column["sortable"] ) && $column["sortable"] ) {
        $sortableList[$key] = array(
          "label" => $column["label"],
          "value" => $key
        );
      }
    }

    # sort
    if( $sortableList ) {
      $storedValue = fn_Setting::getAccountStorage( "$id-sort" );
      $sortKeyList = array_keys( $sortableList );
      $storedValue = $storedValue? $storedValue: $sortKeyList[0];
      $innerHtml[] = Tag::build(
        "ui.option",
        array( "value" => $storedValue, "count" => count( $sortableList ) ),
        ui_Form::getField(
          $id,
          "sort",
          array(
            "class" => "sort",
            "label" => "Ordre",
            "type"  => "select",
            "list"  => $sortableList
          ),
          array( "sort" => $storedValue )
        )
      );
    }

    # headtitle
    if( isset( $params["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "ui.headtitle", false, $params["headtitle"] );
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
      $innerHtml[] = self::getRow( $primary, $item );
    }

    # tag
    return Tag::build( ( $partOnly? "ui.listpart": "ui.list" ), $attributes, $innerHtml );
  }

  /****************************************************************************/
  public static function getRow( $primary, $fields ) {
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
