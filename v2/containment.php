<?php

$diane = new mysqli( "localhost", "root", null, "inventory" );
$id = $diane->real_escape_string( $_GET['id'] );
$connector = $diane->real_escape_string( $_GET['connector'] );

$igotos = $diane->query( "SELECT * FROM objlink WHERE obj_from = $id AND connector = '$connector'" );
$icomefroms = $diane->query( "SELECT * FROM objlink WHERE obj_to = $id AND connector = '$connector'" );

$igoto_ret = array();
$icomefrom_ret = array();

while( $igoto = $igotos->fetch_assoc() ) {

	$id = $igoto['obj_to'];

	$info_ret = array();
	$infos = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	while( $info = $infos->fetch_assoc() ) {
		array_push( $info_ret, array(
			"k" => $info['key_name'],
			"v" => $info['value_str']
		) );
	}

	array_push( $igoto_ret, array(
		"id" => $id,
		"info" => $info_ret
	) );

}

while( $icomefrom = $icomefroms->fetch_assoc() ) {

	$id = $icomefrom['obj_to'];

	$info_ret = array();
	$infos = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	while( $info = $infos->fetch_assoc() ) {
		array_push( $info_ret, array(
			"k" => $info['key_name'],
			"v" => $info['value_str']
		) );
	}

	array_push( $icomefrom_ret, array(
		"id" => $id,
		"info" => $info_ret
	) );

}

$ret = array(
	"icomefrom" => $icomefrom_ret,
	"igoto" => $igoto_ret
);

echo json_encode( $ret );

?>
