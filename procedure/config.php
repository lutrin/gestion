<?php
#error_reporting (E_ALL ^ E_NOTICE);
$CHARSET = "UTF-8";
$DEFAULT_LANG = "fr";

$SITE_NAME = "Lutrin";

$CONTROLLER = array(
  "wrongentry" => array(
    "fr" => "Mauvaise entrée",
    "en" => "Wrong entry"
  ),
  "disconnected" => array(
    "fr" => "Vous êtes maintenant déconnecté.",
    "en" => "You are disconnected."
  ),
  "delete" => array(
    "fr" => "Suppression",
    "en" => "Delete"
  ),
  "noitem" => array(
    "fr" => "Vous devez sélectionner au moins un item.",
    "en" => "You must select atleast one item."
  )
);

$APP = array(
  "site" => "Lutrin",
  "name" => array(
    "fr" => "Gestion",
    "en" => "Management"
  ),
  "desc" => array(
    "fr" => "Système de gestion de contenu",
    "en" => "Control management system"
  ),
  "author" => "Eric Barolet",
  "meta"   => array( "robots" => "noindex, nofollow" ),
  "stylesheet" => array(
    "../../external/style/boilerplate.style.css",
    "../../external/style/holmes.min.css",
    "style/edit.css"
  ),
  "head_script" => "../external/interaction/modernizr-1.7.min.js",
  "body_script" => array(
      "../external/interaction/jquery-1.6.1.min.js",
      "../external/interaction/jquery-ui-1.8.11.custom.min.js",
      "../external/interaction/jquery.rightClick.js",
      "../library/interaction/jquery.xslt.mod.js",
      "../library/interaction/common.js",
      "interaction/edit.js"
    )
);

$FOOTERLINK = array(
  "copyright" => "Lutrin&nbsp;©&nbsp;2011",
  "help"      => array(
    "fr" => "Aide",
    "en" => "Help"
  ),
  "condition" => array(
    "fr" => "Conditions&nbsp;d'utilisation",
    "en" => "Conditions"
  ),
  "about"     => array(
    "fr" => "À&nbsp;propos",
    "en" => "About"
  )
);

$LOGIN = array(
  "legend" => array(
    "fr" => "Connexion",
    "en" => "Login"
  ),
  "username" => array(
    "fr" => "Utilisateur",
    "en" => "User"
  ),
  "password" => array(
    "fr" => "Mot de passe",
    "en" => "Password"
  ),
  "connect" => array(
    "fr" => "Connecter",
    "en" => "Connect"
  )
);

$EDITOR = array(
  "active" => array(
    "fr" => "Compte activé",
    "en" => "Actived account"
  ),
  "username" => array(
    "fr" => "Utilisateur (unique)",
    "en" => "User (unique)"
  ),
  "passwordoptional" => array(
    "fr" => "Changement de mot de passe (facultatif)",
    "en" => "Password changing (optional)"
  ),
  "password" => array(
    "fr" => "Mot de passe",
    "en" => "Password"
  ),
  "confirmpassword" => array(
    "fr" => "Confirmation de mot de passe",
    "en" => "Password confirmation"
  ),
  "admin" => array(
    "fr" => "Droits administrateurs",
    "en" => "Administrator"
  )
);

$GROUPEDITOR = array(
  "active" => array(
    "fr" => "Activé",
    "en" => "Active"
  ),
  "name" => array(
    "fr" => "Groupe (unique)",
    "en" => "Group (unique)"
  )
);

$SETTING = array(
  "apply" => array(
    "fr" => "Appliquer",
    "en" => "Apply"
  ),
  "login" => array(
    "fr" => "Paramètres de connexion",
    "en" => "Login settings"
  ),
  "username" => array(
    "fr" => "Utilisateur",
    "en" => "User"
  ),
  "longname" => array(
    "fr" => "Nom complet",
    "en" => "Long name"
  ),
  "password" => array(
    "fr" => "Nouveau mot de passe",
    "en" => "New password"
  ),
  "confirmpassword" => array(
    "fr" => "Confirmation de mot de passe",
    "en" => "Password confirmation"
  ),
  "edit" => array(
    "fr" => "Paramètres d'édition",
    "en" => "Editor settings"
  ),
  "lang" => array(
    "fr" => "Langue",
    "en" => "Language"
  )
);

$HEADER_BUTTONS = array(
  "setting" => array(
    "fr" => "Configurations",
    "en" => "Setting"
  ),
  "logout" => array(
    "fr" => "Déconnecter",
    "en" => "Disconnect"
  )
);

$MSG_NOSCRIPT = array(
  "fr" => "L'activation de JavaScript est absolument nécessaire.",
  "en" => "Enabling JavaScript is absolutely necessary.",
);

$TOOLS = array(
  "headtitle" => array(
    "fr" => "Outils",
    "en" => "Tools"
  ),
  "pages" => array(
    "fr" => "Les pages",
    "en" => "Pages"
  ),
  "templates" => array(
    "fr" => "Les gabarits",
    "en" => "Templates"
  ),
  "articles" => array(
    "fr" => "Les articles",
    "en" => "Articles"
  ),
  "files" => array(
    "fr" => "Les fichiers",
    "en" => "Files"
  ),
  "editors" => array(
    "fr" => "Les éditeurs",
    "en" => "Editors"
  ),
  "visitors" => array(
    "fr" => "Les visiteurs",
    "en" => "Visitors"
  )
);

$TOOLS_EDITOR = array(
  "individual" => array(
    "fr" => "Éditeurs",
    "en" => "Editors"
  ),
  "group" => array(
    "fr" => "Groupes",
    "en" => "Groups"
  )
);

$TOOLS_EDITOR_INDIVIDUAL = array(
  "k" => array(
    "fr" => "Id",
    "en" => "Id"
  ),
  "username" => array(
    "fr" => "Éditeur",
    "en" => "Editor"
  ),
  "active" => array(
    "fr" => "État",
    "en" => "Status"
  ),
  "admin" => array(
    "fr" => "Type",
    "en" => "Type"
  ),
  "longname" => array(
    "fr" => "Nom complet",
    "en" => "Complete name"
  ),
);

$TOOLS_EDITOR_GROUP = array(
  "k" => array(
    "fr" => "Id",
    "en" => "Id"
  ),
  "name" => array(
    "fr" => "Groupe",
    "en" => "Group"
  ),
  "active" => array(
    "fr" => "État",
    "en" => "Status"
  ),
  "longname" => array(
    "fr" => "Nom complet",
    "en" => "Complete name"
  ),
);

$PERMISSION = array(
  "title" => array(
    "fr" => "Problème de permission",
    "en" => "Permission problem"
  ),
  "message" => array(
    "fr" => "Vous n'avez pas accès à cette section ou opération.",
    "en" => "This section or operation is not permit."
  )
);

$DELETE = array(
  "title" => array(
    "fr" => "Suppression",
    "en" => "Delete"
  ),
  "message" => array(
    "fr" => "Vous n'avez pas la permission de supprimer votre propre compte.",
    "en" => "You cannot delete your account."
  )
);

$DIALOG = array(
  "close" => array(
    "fr" => "Fermer",
    "en" => "Close"
  )
);

mb_internal_encoding( $CHARSET );
mb_http_output( $CHARSET );
ob_start("mb_output_handler");

$loc_de = setlocale( LC_ALL, 'fr_CA.UTF8', 'fr.UTF8' );
$DATETIMEZONE = new DateTimeZone( 'America/Montreal' );

$PAGEINDEX = "index.php";

$DBDATA = array(
  "host" => "localhost",
  "user" => "root",
  "password" => "admin",
  "database" => "gestion"
);
$CACHEPATH = "../cache/";
$TEMPPATH = "../template/";
$MERGEPATH = "../merge/";

$FRAME = "../../library/template/index.html";
$EDIT = $TEMPPATH . "edit.html";

$COMPRESS_LIST = array(
  "html" => array( $TEMPPATH ),
  "css"  => array( "../style/" ),
  "js"   => array( "../data/", "../interaction/" )
);

$INCLUDE_LIST = array(

  # external
  "htmlmin" => array(
    "path" => "../../external/procedure/htmlmin.php"
  ),
  "cssmin" => array(
    "path" => "../../external/procedure/cssmin.php"
  ),
  "jsmin" => array(
    "path" => "../../external/procedure/jsmin.php"
  ),

  # library
  "typeValidator" => array(
    "path" => "../../library/procedure/typeValidator.php"
  ),
  "tokenizer" => array(
    "path" => "../../library/procedure/tokenizer.php"
  ),
  "clean" => array(
    "path" => "../../library/procedure/clean.php",
    "depend" => array( "htmlmin", "cssmin", "jsmin" )
  ),
  "cache" => array(
    "path" => "../../library/procedure/cache.php"
  ),
  "dbConnect" => array(
    "path" => "../../library/procedure/dbConnect.php"
  ),

  "tag" => array(
    "path" => "tag.php"
  ),
  "fn" => array(
    "path" => "fn.php"
  ),

  # function
  "fnLogin" => array(
    "path" => "fn/login.php",
    "class" => "fn_Login"
  ),
  "fnEdit" => array(
    "path" => "fn/edit.php",
    "class" => "fn_Edit"
  ),
  "fnSetting" => array(
    "path" => "fn/setting.php",
    "class" => "fn_Setting",
    "entries" => array( "setting" )
  ),
  "fnForm" => array(
    "path" => "fn/form.php",
    "class" => "fn_Form"
  ),
  "fnPage" => array(
    "path" => "fn/page.php",
    "depend" => "fn",
    "class" => "fn_Page",
    "entries" => array( "pages" )
  ),
  "fnTemplate" => array(
    "path" => "fn/template.php",
    "depend" => "fn",
    "class" => "fn_Template",
    "entries" => array( "templates" )
  ),
  "fnArticle" => array(
    "path" => "fn/article.php",
    "depend" => "fn",
    "class" => "fn_Article",
    "entries" => array( "articles" )
  ),
  "fnFile" => array(
    "path" => "fn/file.php",
    "depend" => "fn",
    "class" => "fn_File",
    "entries" => array( "files" )
  ),
  "fnEditor" => array(
    "path" => "fn/editor.php",
    "depend" => "fn",
    "class" => "fn_Editor",
    "entries" => array( "editors", "editor" )
  ),
  "fnVisitor" => array(
    "path" => "fn/visitor.php",
    "depend" => "fn",
    "class" => "fn_Visitor",
    "entries" => array( "visitors" )
  ),

  # user interface
  "uiFrame" => array(
    "path" => "ui/frame.php",
    "depend" => "tag"
  ),
  "uiForm" => array(
    "path" => "ui/form.php",
    "depend" => "tag"
  ),
  "uiNav" => array(
    "path" => "ui/nav.php",
    "depend" => "tag"
  ),
  "uiList" => array(
    "path" => "ui/list.php",
    "depend" => "tag"
  ),
  "uiDialog" => array(
    "path" => "ui/dialog.php",
    "depend" => "tag"
  ),

  # data base table
  "dbAbstract" => array(
    "path" => "db.php",
    "depend" => "dbConnect"
  ),
  "dbEditor" => array(
    "path" => "db/editor.php",
    "depend" => "dbAbstract"
  ),
  "dbGroupEditor" => array(
    "path" => "db/groupEditor.php",
    "depend" => "dbAbstract"
  ),
  "dbEditorInGroup" => array(
    "path" => "db/editorInGroup.php",
    "depend" => "dbAbstract"
  )
);
