<?php
class ui_Form {
  public static function buildXml( $params, $fields, $values = false ) {
    Includer::add( "tag" );

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
      $innerHtml[] = Tag::build( "div", array( "class" => "buttonList" ), Tag::build( "input", array(
        "type"  => "submit",
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
  protected static function getField( $formId, $fieldId, $attributes, $values ) {

    # field set  
    if( isset( $attributes["type"] ) && $attributes["type"] == "fieldset" ) {
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

    # type text
    if( !isset( $attributes["type"] ) ) {
      $attributes["type"] = "text";
    }

    # field
    $attributes["name"] = $fieldId;
    $attributes["id"] = "$formId-$fieldId";

    # value
    $innerHtml = array();
    if( isset( $values[$fieldId] ) ) {
      $innerHtml[] = Tag::build( "ui.value", false, $values[$fieldId] );
    }

    # list
    if( isset( $attributes["list"] ) ) {
      $innerHtml[] = Tag::build( "ui.datalist", false, self::getDatalist(
        $attributes["id"],
        $attributes["name"],
        $attributes["list"]
      ) );
      unset( $attributes["list"] );
    }

    return Tag::build( "ui.field", $attributes, $innerHtml );
  }

  /****************************************************************************/
  protected static function getDatalist( $id, $name, $list ) {
    $dataList = array();
    foreach( $list as $key => $item ) {
      $dataList[] = Tag::build(
        "ui.dataitem",
        array_merge( $item, array( "listid" => $id, "listname" => $name, "key" => $key ) )
      );
    }
    return $dataList;
  }
}
