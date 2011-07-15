<?php

class Tag {
  public static function build( $tagName, $attributes = false, $innerHtml = false ) {
    return "<$tagName"
         . self::getAttributes( $attributes )
         . self::getInnerHtml( $tagName, $innerHtml )
         . ">";
  }

  /****************************************************************************/
  public static function escapeQuot( $str ) {
    return preg_replace( '/\'/', "&apos;", $str );
  }

  /****************************************************************************/
  protected static function getAttributes( $attributes ) {
    if( !$attributes ) {
      return "";
    }
    $attributeList = array();
    foreach( $attributes as $key => $value ) {
      
      $attributeList[] = "$key='" . self::escapeQuot( $value ) . "'";
    }
    return " " . join( " ", $attributeList );
  }

  /****************************************************************************/
  protected static function getInnerHtml( $tagName, $innerHtml ) {
    if( $innerHtml === false ) {
      return "/";
    }
    $content = ( is_array( $innerHtml )? join( "", $innerHtml ): $innerHtml );
    if( $content == strip_tags( $content ) ) {
      $content = self::escapeQuot( $content );
    }    
    return ">$content</$tagName";    
  }
}
