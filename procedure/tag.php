<?php

class Tag {
  public static function build( $tagName, $attributes = false, $innerHtml = false ) {
    return "<$tagName"
         . self::getAttributes( $attributes )
         . self::getInnerHtml( $tagName, $innerHtml )
         . ">";
  }

  /****************************************************************************/
  protected static function getAttributes( $attributes ) {
    if( !$attributes ) {
      return "";
    }
    $attributeList = array();
    foreach( $attributes as $key => $value ) {
      $attributeList[] = "$key='$value'";
    }
    return " " . join( " ", $attributeList );
  }

  /****************************************************************************/
  protected static function getInnerHtml( $tagName, $innerHtml ) {
    if( $innerHtml === false ) {
      return "/";
    }
    return ">" . ( is_array( $innerHtml )? join( "", $innerHtml ): $innerHtml ) . "</$tagName";    
  }
}
