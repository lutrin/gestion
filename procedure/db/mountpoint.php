<?php
class db_Mountpoint extends db_Abstract {
  public static $table = "mountpoint";
  public static $fields = array( "k", "name", "pathK", "active", "canView", "canRename", "canEdit", "canDelete", "canAdd" );
  public static $emptyValues = array( "active" => 0, "canView" => 0, "canRename" => 0, "canEdit" => 0, "canDelete" => 0, "canAdd" => 0 );
}
