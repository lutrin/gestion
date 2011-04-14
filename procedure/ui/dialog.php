<?php
class ui_Dialog {
  public static function buildXml( $title, $message ) {
    global $DIALOG;

    # attributes
    $attributes = array(
      "title" => $title,
      "close" => $DIALOG["close"][getLang()]
    );

    return Tag::build( "ui.dialog", $attributes, $message );
  }
}
