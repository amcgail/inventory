<?php

$diane = new mysqli( 'localhost', 'root', NULL, 'inventory' );

$obj_from = $diane->real_escape_string( $_GET['obj_from'] );
$obj_to = $diane->real_escape_string( $_GET['obj_to'] );
$connector = $diane->real_escape_string( $_GET['connector'] );

$diane->query( "DELETE FROM objlink WHERE obj_from = $obj_from AND obj_to = $obj_to AND connector = '$connector'" );
