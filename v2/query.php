<?php

$diane = new mysqli( "localhost", "root", null, "inventory" );
$query = $diane->real_escape_string( $_GET['query'] );

$ret = array();

$objs = $diane->query( "SELECT obj_id FROM attr WHERE value_str LIKE '%$query%' OR obj_id = ".intval( $query )." GROUP BY obj_id" );
while( $obj = $objs->fetch_assoc() ) {
	$id = $obj['obj_id'];
	$info_ret = array();
	$infos = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	while( $info = $infos->fetch_assoc() ) {
		array_push( $info_ret, array(
			"k" => $info['key_name'],
			"v" => $info['value_str']
		) );
	}

	array_push( $ret, array(
		"id" => $id,
		"info" => $info_ret
	) );
}

echo json_encode( $ret );
