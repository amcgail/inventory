<?php
$diane = new mysqli( 'localhost', 'root', NULL, 'inventory' );

$id = $diane->real_escape_string( $_GET['obj'] );
$k = $diane->real_escape_string( $_GET['k'] );
$v = $diane->real_escape_string( $_GET['v'] );

$diane->query( "DELETE FROM attr WHERE obj_id = $id AND key_name = '$k' AND value_str = '$v'" );
