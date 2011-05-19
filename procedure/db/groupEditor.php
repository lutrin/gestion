<?php
class db_GroupEditor extends db_Abstract {
  public static $table = "groupEditor";

  /****************************************************************************/
  public static function getEmptyValues() {
    return array( "toolList" => "" );
  }
}
