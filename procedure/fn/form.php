<?php
class fn_Form {
  /****************************************************************************/
  public static function hasErrors( $params, $fields, $values ) {

    # token
    $exist = Tokenizer::exists( $params["id"], $values["token"] );
    Tokenizer::delete( $params["id"] );
    if( !$exist ) {
      return array( "fatalError" => "tokenerror" );
    }

    # fields
    return array(
      "errorList" => self::validFieldList( $fields, $values ),
      "values" => array( "token" => Tokenizer::create( $params["id"] ) )
    );
  }

  /****************************************************************************/
  protected static function validFieldList( $fieldList, $values ) {
    $errorList = array();
    foreach( $fieldList as $key => $field ) {

      # field list
      if( isset( $field["fieldlist"] ) ) {
        $errorList = array_merge( $errorList, self::validFieldList( $field["fieldlist"], $values ) );
        continue;
      }

      # required
      if( isset( $field["required"] ) && $field["required"] && ( !isset( $values[$key] ) || $values[$key] === "" ) ) {
        $errorList[] = array( "name" => $key, "msg" => "required" );
        continue;
      }

      # pattern
      if( isset( $field["pattern"] ) && $field["pattern"] && !preg_match( "/" . $field["pattern"] . "/", $values[$key] ) ) {
        $errorList[] = array( "name" => $key, "msg" => "required" );
        continue;
      }

      # equal
      if( isset( $field["equal"] ) && ( $values[$key] != $values[$field["equal"]] ) ) {
        $errorList[] = array( "name" => $key, "msg" => "notequal" );
        continue;
      }

      # maxlength
      if( isset( $field["maxlength"] ) && ( strlen( $values[$key] ) > $field["maxlength"] ) ) {
        $errorList[] = array( "name" => $key, "msg" => "toolong" );
        continue;
      }
    }
    return $errorList;
  }
}
