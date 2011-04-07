<?php
class ui_List {
  public static function buildXml( $params, $items ) {
    Includer::add( "tag" );
    return Tag::build( "pre", false, print_r( $items, 1 ) );
  }
}
