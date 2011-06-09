<?php
class ui_Form {
  public static function buildXml( $params, $fields, $values = false ) {

    # initialize
    $attributes = array();
    $innerHtml = array();

    # params
    $submit = false;
    $message = false;

    # token
    $id = "";
    if( isset( $params["id"] ) ) {
      $id = $params["id"];
      $innerHtml[] = Tag::build( "input", array(
        "id"    => "$id-token",
        "type"  => "hidden",
        "name"  => "token",
        "value" => Tokenizer::create( $id )
      ) );
    }

    # head title
    if( isset( $params["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "h2", false, $params["headtitle"] );
      unset( $params["headtitle"] );
    }

    foreach( $params as $key => $param ) {
      if( $key == "submit" ) {
        $submit = $param;
        continue;

        # message
      } elseif( $key == "message" ) {
        $message = $param;
        continue;

        # action
      } elseif( $key == "action" ) {
        $innerHtml[] = Tag::build( "input", array(
          "type"  => "hidden",
          "id"    => "$id-action",
          "name"  => "action",
          "value" => $param
        ) );
        $attributes[$key] = "?action=" . $params[$key];
        continue;
      }
      $attributes[$key] = $param;
    }

    # fields
    foreach( $fields as $key => $item ) {
      $innerHtml[] = self::getField( $id, $key, $item, $values );
    }

    # submit
    if( $submit ) {
      $innerHtml[] = Tag::build( "fieldset", array( "class" => "buttonList" ), Tag::build( "input", array(
        "type"  => "submit",
        "name"  => "submit",
        "value" => $submit
      ) ) );
    }

    # message
    if( $message ) {
      $innerHtml[] = Tag::build( "div", array(
        "class" => "formMsg"
      ), $message );
    }
    return Tag::build( "ui.form", $attributes, $innerHtml );
  }

  /****************************************************************************/
  public static function getField( $formId, $fieldId, $attributes, $values ) {
    $id = "$formId-$fieldId";

    # group field 
    if( isset( $attributes["type"] ) ) {

        # field set
        if( $attributes["type"] == "fieldset" ) {
          return self::getFieldset( $formId, $attributes, $values );
        }

        # tabs
        if( in_array( $attributes["type"], array( "tabs", "accordion", "separator" ) ) ) {
          return self::getNav( $formId, $attributes, $values, $id, $attributes["type"] );
        }
    } else {
      $attributes["type"] = "text";
    }

    # field
    $attributes["name"] = $fieldId;
    $attributes["id"] = $id;

    # value
    $innerHtml = array();
    if( isset( $values[$fieldId] ) ) {
      $innerHtml[] = Tag::build( "ui.value", false, $values[$fieldId] );
    }

    # list
    if( isset( $attributes["list"] ) ) {
      $innerHtml[] = Tag::build( "ui.datalist", false, self::getDatalist(
        $attributes["list"]
      ) );
      unset( $attributes["list"] );
    }

    return Tag::build( "ui.field", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getFieldset( $formId, $attributes, $values ) {
    unset( $attributes["type"] );

    # legend
    $legend = "";
    $innerHtml = array();
    if( isset( $attributes["legend"] ) ) {
      $innerHtml[] = Tag::build( "legend", false, $attributes["legend"] );
      unset( $attributes["legend"] );
    }

    # field list
    if( isset( $attributes["fieldlist"] ) ) {
      foreach( $attributes["fieldlist"] as $key => $item ) {
        $innerHtml[] = self::getField( $formId, $key, $item, $values );
      }
      unset( $attributes["fieldlist"] );
    }
    return Tag::build( "fieldset", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getNav( $formId, $attributes, $values, $id, $mode ) {
    unset( $attributes["type"] );
    $params = array(
      "id"   => "$id-nav",
      "mode" => $mode
    );
    if( isset( $attributes["headtitle"] ) ) {
      $innerHtml[] = Tag::build( "h3", false, $attributes["headtitle"] );
      unset( $attributes["headtitle"] );
    }

    # item list
    $itemList = array();
    if( isset( $attributes["itemlist"] ) ) {
      $i = 0;
      foreach( $attributes["itemlist"] as $key => $item ) {
        $innerHtml = array();
        foreach( $item["content"] as $contentKey => $content ) {
          $innerHtml[] = self::getField( $formId, $contentKey, $content, $values );
        }
        $itemList[$key] = array(
          "label"     => $item["label"],
          "innerHtml" => $innerHtml
        );
        if( !$i++ ) {
          $itemList[$key]["selected"] = true;
        }
      }
    }
    Includer::add( "uiNav" );
    return ui_Nav::buildXml( $params, $itemList );
  }

  /****************************************************************************/
  protected static function getDatalist( $list ) {
    $dataList = array();
    foreach( $list as $key => $item ) {
      $dataList[] = Tag::build(
        "ui.dataitem",
        array_merge( $item, array( "key" => $key ) )
      );
    }
    return $dataList;
  }
}
