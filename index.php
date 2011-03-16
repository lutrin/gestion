<?php

# force ssl
if( $_SERVER["HTTPS"] != "on" ) {
   header( "HTTP/1.1 301 Moved Permanently" );
   header( "Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] );
   exit();
}

# set header
header( 'Content-Type: text/html; charset=utf-8' );

# set mode
$adminMode = true;

# call controller
chdir( "procedure" );
include "controller.php";

exit;
