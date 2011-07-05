<?php
class ui_Breadcrumb {
  public static function buildXml( $list, $object, $includePresent = false ) {
    return Tag::build(
      "ui.breadcrumb",
      array( "object" => $object, "includePresent" => $includePresent ),
      array_map( function( $item ) {
        return Tag::build( "ui.breaditem", array( "k" => $item["k"] ), $item["name"] );
      }, $list )
    );
  }
}
