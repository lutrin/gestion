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
    "style/edit.css"
  ),
  "head_script" => "../external/interaction/modernizr-1.7.min.js",
  "body_script" => array(
      "../external/interaction/jquery-1.3.2.min.js",
      /*"../external/interaction/jquery-ui-1.8.custom.min.js",*/
      "../external/interaction/jquery.xslt.js",
      "../library/interaction/common.js",
      "interaction/edit.js"
    )
);

$EDITOR = array(
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
    "fr" => "Langage",
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
  "pages" => array(
    "fr" => "Les pages",
    "en" => "Pages"
  ),
  "editors" => array(
    "fr" => "Les éditeurs",
    "en" => "Editors"
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

$FRAME = $TEMPPATH . "index.html";
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

  # function
  "fnLogin" => array(
    "path" => "fn/login.php"
  ),
  "fnEdit" => array(
    "path" => "fn/edit.php"
  ),
  "fnSetting" => array(
    "path" => "fn/setting.php"
  ),
  "fnForm" => array(
    "path" => "fn/form.php"
  ),

  # user interface
  "uiFrame" => array(
    "path" => "ui/frame.php"
  ),
  "uiForm" => array(
    "path" => "ui/form.php"
  ),
  "uiNav" => array(
    "path" => "ui/nav.php"
  ),

  # data base table
  "dbEditor" => array(
    "path" => "db/editor.php",
    "depend" => "dbConnect"
  )
);
