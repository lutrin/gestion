<?php
class Encode {

  /****************************************************************************/
  public static function getString( $array ) {
    global $ENCRYPTKEY;
    ksort( $array );
    $json = json_encode( $array );
    return base64_encode( $json );
  }

  /****************************************************************************/
  public static function getArray( $string ) {
    global $ENCRYPTKEY;
    $json = base64_decode( $string );
    return json_decode( $json, true );
  }
}
