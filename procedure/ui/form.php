<?php
class ui_Form {
  public static function buildXml( $params, $fields, $values = false ) {
    Includer::add( "tag" );

    # initialize
    $attributes = array();
    $innerHtml = array();

    # params
    $submit = false;

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

    foreach( $params as $key => $item ) {
      if( $key == "submit" ) {
        $submit = $params[$key];
        continue;

        # action
      } elseif( $key == "action" ) {
        $innerHtml[] = Tag::build( "input", array(
          "type"  => "hidden",
          "id"    => "$id-action",
          "name"  => "action",
          "value" => $params[$key]
        ) );
        $attributes[$key] = "?action=" . $params[$key];
        continue;
      }
      $attributes[$key] = $params[$key];
    }

    # fields
    foreach( $fields as $key => $item ) {
      $innerHtml[] = self::getField( $id, $key, $item, $values );
    }

    # submit
    if( $submit ) {
      $innerHtml[] = Tag::build( "input", array(
        "type"  => "submit",
        "value" => $submit
      ) );
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
    return Tag::build( "ui.field", $attributes, ( isset( $values[$fieldId] )? $values[$fieldId]: false ) );
  }
}
