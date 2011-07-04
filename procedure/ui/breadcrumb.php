<?php
class ui_Breadcrumb {
  public static function buildXml( $list, $object ) {
    $breadList = array();
    foreach( $list as $key => $item ) {
      $breadList[] = Tag::build( "ui.breaditem", array( "k" => $key ), $item );
    }
    return Tag::build( "ui.breadcrumb", array( "object" => $object ), join( "", $breadList ) );
  }
}
