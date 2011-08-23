<?php
class fn_Page extends fn {
  protected static $idList = "pages";

  /****************************************************************************/
  public static function getContent() {
    if( $allowResult = fn_Login::isNotAllowed( self::$idList ) ) {
      return $allowResult;
    }
    return array(
      "replacement" => array(
        "query" => "#" .  self::$idList,
        "innerHtml" => self::getTree()
      ),
      "hash" => true
    );
  }

  /****************************************************************************/
  public static function insert( $parentK ) {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }

    # get default values
    Includer::add( "dbPage" );
    $defaults = db_Page::defaults();
    $defaults["k"] = 0;
    $defaults["parentK"] = $parentK;

    # get params
    $params = self::getFormParams();
    $params["headtitle"] = "Nouvelle&nbsp;page";
    $fields = self::getFormFields();

    Includer::add( array( "uiForm" ) );
    return array(
      "details" => ui_Form::buildXml(
        $params,
        $fields,
        $defaults
      )
    );
  }

  /****************************************************************************/
  public static function edit( $k ) {
    if( $allowResult = fn_Login::isNotAllowed() ) {
      return $allowResult;
    }

    return array(
      "details" => "soon..."
    );
  }

  /****************************************************************************/
  protected static function getTree() {

    # get tree
    Includer::add( array( "dbPage", "uiList" ) );

    # params
    $params = array(
      "id"          => self::$idList,
      "mode"        => array(
        "tree"   => "Arbre"
      ),
      "primary"     => "k",
      "main"        => "name",
      "mainAction"  => "edit",
      "rowAction"   => "expand",
      "refreshable" => true,
      "expandable"  => true,
      "columns"     => self::getColumns(),
      "actions"     => array(
        "edit"   => array(
          "title" => "Modifier"
        ),
        "insert"   => array(
          "title" => "Insérer"
        ),
        "delete" => array(
          "title"    => "Supprimer",
          "multiple" => true,
          "individual" => true
        )
      )
    );

    # field
    $fields = self::prepareFields( $params["columns"] );

    return ui_List::buildXml( $params, db_Page::getTree( $fields, 0, false, "rank" ) );
  }

  /****************************************************************************/
  protected static function getColumns() {
    return array(
      "k"        => array(
        "label"  => "id",
        "hidden" => true
      ),
      "name" => array(
        "label"    => "Nom",
        "class"    => "page"
      ),
      "class"    => array(
        "field" => "type"
      )
    );        
  }

  /****************************************************************************/
  protected static function getFormParams( $k = 0, $class = "page" ) {
    return array(
      "id"       => "page-$k",
      "action"   => "save",
      "submit"   => "Enregistrer",
      "method"   => "post",
      "class"    => $class,
      "closable" => true
    );
  }

  /****************************************************************************/
  protected static function getFormFields() {
    return array(
      "k"     => array(
        "type" => "hidden"
      ),
      "parentK"     => array(
        "type" => "hidden"
      ),
      "object"     => array(
        "type" => "hidden",
        "value" => "page"
      ),
      "name" => array(
        "label" => "Nom",
        "required" => "required"
      ),
      "description" => array(
        "label" => "Description",
        "type" => "contentEditable",
        "allowed" => "p,ul,ol,li,em,sub,sup,br",
        "menu" => array(
          array(
            "title" => "Édition",
            "list" => array(
              "undo" => array(
                "title" => "Annuler"
              ),
              "redo" => array(
                "title" => "Rétablir"
              ),
              "cut" => array(
                "title" => "Couper"
              ),
              "copy" => array(
                "title" => "Copier"
              ),
              "paste" => array(
                "title" => "Coller"
              ),
              "delete" => array(
                "title" => "Supprimer"
              ),
              "selectAll" => array(
                "title" => "Tout sélectionner"
              ),
              "stripTags" => array(
                "title" => "Nettoyage",
                "trigger" => true
              )
            )
          ),
          array(
            "title" => "Affichage",
            "list" => array(
              "toggleTag" => array(
                "title"   => "Balises",
                "trigger" => true
              )
            )
          ),
          array(
            "title" => "Format",
            "list" => array(
              "italic" => array(
                "title" => "Italique"
              ),
              "subscript" => array(
                "title" => "Indice"
              ),
              "superscript" => array(
                "title" => "Exposant"
              ),
              "removeFormat" => array(
                "title" => "Formattage par défaut"
              )
            )
          ),
          array(
            "title" => "Balisage",
            "list" => array(
              "insertParagraph" => array(
                "title" => "Paragraphe"
              ),
              "insertOrderedList" => array(
                "title" => "Liste numérotée"
              ),
              "insertUnorderedList" => array(
                "title" => "Liste non-numérotée"
              )
            )
          )
        )
      ),
      "imageK" => array(
        "label"  => "Image",
        "type"   => "picklist",
        "class"  => "image",
        "object" => "files-image",
        "list"   => array()
      ),
      "active" => array(
        "label" => "Page activée",
        "type" => "checkbox",
        "value" => 1
      ),
      "type" => array(
        "label" => "type",
        "type" => "select",
        "list" => array(
          "domain" => array(
            "label" => "Domaine",
            "value" => "domain"
          ),
          "normal" => array(
            "label" => "Normal",
            "value" => "normal"
          ),
          "shortcut" => array(
            "label" => "Raccourci",
            "value" => "shortcut"
          ),
          "document" => array(
            "label" => "Lien vers un fichier",
            "value" => "document"
          ),
          "external" => array(
            "label" => "Lien externe",
            "value" => "external"
          )
        )
      )
    );
#TODO droits de visite, droits d'édition
  }
}
