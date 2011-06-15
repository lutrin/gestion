<?php
abstract class fn {
  protected static $idList = "";  

  /****************************************************************************/
  public static function getContent() {
    return fn_Login::isNotImplanted();
  }

  /****************************************************************************/
  public static function edit( $k ) {
    return fn_Login::isNotImplanted();
  }

  /****************************************************************************/
  public static function save( $k, $values ) {
    return fn_Login::isNotImplanted();
  }

  /****************************************************************************/
  public static function delete( $kList ) {
    return fn_Login::isNotImplanted();
  }
}
