<?php
class ui_Dialog {
  public static function buildXml( $title, $message, $close = false ) {
    global $DIALOG;

    # attributes
    $attributes = array(
      "title" => $title,
      "close" => ( $close? $close: $DIALOG["close"][getLang()] )
    );

    return Tag::build( "ui.dialog", $attributes, $message );
  }
}
