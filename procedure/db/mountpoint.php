<?php
class db_Mountpoint extends db_Abstract {
  public static $table = "mountpoint";

  /****************************************************************************/
  public static function getEmptyValues() {
    return array( "active" => 0, "canView" => 0, "canRename" => 0, "canEdit" => 0, "canDelete" => 0, "canAdd" => 0 );
  }
}
