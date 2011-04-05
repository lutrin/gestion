<?php
class ui_Dialog {
  public static function buildXml( $title, $message ) {
    global $DIALOG;
    $lang = getLang();

    # attributes
    $attributes = array(
      "title" => $title,
      "close" => $DIALOG["close"][$lang]
    );

    return Tag::build( "ui.dialog", $attributes, $message );
  }
}
