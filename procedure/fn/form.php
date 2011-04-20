<?php
class fn_Form {
  /****************************************************************************/
  public static function hasErrors( $params, $fields, $values, $k = false ) {

    # token
    $exist = Tokenizer::exists( $params["id"], $values["token"] );
    Tokenizer::delete( $params["id"] );
    if( !$exist ) {
      return array( "fatalError" => "tokenerror" );
    }

    # fields
    return array(
      "errorList" => self::validFieldList( $fields, $values, $k ),
      "values" => array( "token" => Tokenizer::create( $params["id"] ) )
    );
  }

  /****************************************************************************/
  protected static function validFieldList( $fieldList, $values, $k ) {
    $errorList = array();
    foreach( $fieldList as $key => $field ) {

      # field list
      if( isset( $field["fieldlist"] ) ) {
        $errorList = array_merge( $errorList, self::validFieldList( $field["fieldlist"], $values, $k ) );
        continue;
      }

      # required
      if( isset( $field["required"] ) && ( !isset( $values[$key] ) || $values[$key] === "" ) ) {
        $errorList[] = array( "name" => $key, "msg" => "required" );
        continue;
      }

      # equal
      if( isset( $field["equal"] ) && ( $values[$key] != $values[$field["equal"]] ) ) {
        $errorList[] = array( "name" => $key, "msg" => "notequal" );
        continue;
      }
    }
    return $errorList;
  }
}
