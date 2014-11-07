<?php

$diane = new mysqli( 'localhost', 'root', NULL, 'inventory' );
$id = $diane->real_escape_string( $_GET['obj'] );

$diane->query( "DELETE FROM objlink WHERE obj_from = $id OR obj_to = $id" );
$diane->query( "DELETE FROM attr WHERE obj_id = $id" );

return "The dirty deed is done";
