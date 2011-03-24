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
      } elseif( isset( $field["required"] ) && ( !isset( $values[$key] ) || $values[$key] === "" ) ) {
        $errorList[] = array( "name" => $key, "msg" => "required" );
      }
    }
    return $errorList;
  }
}
