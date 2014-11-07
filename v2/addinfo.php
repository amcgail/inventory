<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$diane = new mysqli( "localhost", "root", null, "inventory" );

$id = $_GET['obj'];
$toadd = json_decode( $_GET['info'] );

$inlinks = $toadd->inboundlinks;
$outlinks = $toadd->outboundlinks;
$attrs = $toadd->attrs;

foreach( $inlinks as $inlink ) {
	$from = $inlink->obj;
	$con = $inlink->connector;
	$to = $id;
	$diane->query( "INSERT INTO objlink( obj_from, connector, obj_to ) VALUES( $from, '$con', $to )" );
}

foreach( $outlinks as $outlink ) {
	$from = $id;
	$con = $outlink->connector;
	$to = $outlink->obj;
	$diane->query( "INSERT INTO objlink( obj_from, connector, obj_to ) VALUES( $from, '$con', $to )" );
}

foreach( $attrs as $attr ) {
	$k = $attr->k;
	$v = $attr->v;
	$diane->query( "INSERT INTO attr( obj_id, key_name, value_type, value_str ) VALUES( $id, '$k', 'str', '$v' )" );
}

?>
