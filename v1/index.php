 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script>

$(document).ready( function() {

	$.ajax( { 
		"url": "http://cloud.nimbits.com/service/v2/value", 
		"data": { "id": "amcgail2@gmail.com/mysensor", "email": "amcgail2@gmail.com", "json": '{"d":10.5}', "key": "mama" }, 
		"method": "post" 
	} );

	$(".object").each( function() {

		$(this).click( function() {
			console.log( "YES" );
			$("#scan").attr( 'src', '/v1/scan.php?id=' + $(this).attr( 'obj_id' ) );
		} );

	} );

	$(".object").click( function() {
		$(this).next().toggle();
	} );

	$(".obj_children").toggle( false );

} );

</script>

<title>Alec's Awesome Inventory System V1</title>

<style>

	td{
		vertical-align: top;
	}

	#scan {
		width: 700px;
		height: 1000px;
	}
	
	.obj_attr {
		display: inline-block;
		padding: 3px;
		margin-right: 5px;
		border-right: 1px solid;
	}

	.object {
		height: 66px;
		overflow: hidden;
		width: 100%;
		background-color: #EEE;
		border-bottom: 1px solid;
	}

	.obj_id {
		font-weight: bold;
		text-align: center;
	}

	.object_container {
		width: 500px;
	}

	.mode_container {
		width: 100%;
	}

	.mode {
		width: 50px;
		border: 1px solid;
		font-weight: bold;
		text-align: center;
		padding: 10px;
		margin-right: 5px;
		display: inline-block;
		cursor: pointer;
	}

	.mode.selected {
		background-color: #DDD;
	}

	.obj_children {
		margin-left: 30px;
	}
</style>

<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$diane = new mysqli( "localhost", "root", null, "inventory" );

if( !empty( $_GET['mode'] ) ) $mode = $_GET['mode'];
else $mode = 'list';

$listselected = '';
$containselected = '';
if( $mode == 'list' ) $listselected = 'selected';
else $containselected = 'selected';

echo "<div class='mode_container'>";
echo "<div class='mode $listselected' onclick='window.location=\"/v1/?mode=list\"'>list</div>";
echo "<div class='mode $containselected' onclick='window.location=\"/v1/?mode=contain\"'>contain</div>";
echo "</div>";

echo "<table><tr><td>";

echo "<div class='object_container'>";

if( $mode == 'list' ) {

	$objs = $diane->query( "SELECT obj_id FROM attr GROUP BY obj_id" );

	while( $obj = $objs->fetch_assoc() ) {

		echo obj_div( $obj['obj_id'], $diane );

	}

} else {

	//get objects which are not contained in anything
	$objs = $diane->query( "SELECT obj_id FROM attr WHERE obj_id NOT in (SELECT obj_from FROM objlink WHERE connector = 'in') GROUP BY obj_id" );

	while( $obj = $objs->fetch_assoc() ) {

		echo contain( $obj['obj_id'], $diane );

	}

}

echo "</div>";

echo "</td><td>";

echo "<iframe id='scan'>";
echo "</iframe>";

echo "</td>";
echo "</tr>";
echo "</table>";

function contain( $id, $diane ) {

	$ret = '';
	$ret .= obj_div( $id, $diane );

	$ret .= "<div class='obj_children'>";

	$mychildren = $diane->query( "SELECT * FROM objlink WHERE connector = 'in' AND obj_to = $id" );
	while( $child = $mychildren->fetch_assoc() ) {

		$ret .= contain( $child['obj_from'], $diane );

	}

	$ret .= "</div>";

	return $ret;

}

function obj_div( $id, $diane ) {
	$ret = '';
	$ret .= "<div class='object' obj_id=$id>";

	$ret .= "<div class='obj_id'>$id</div>";

	$attrs = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	while( $attr = $attrs->fetch_assoc() ) {
		$ret .= "<div class='obj_attr'>";
		$ret .= "<div class='obj_attr_key'>";
			$ret .= $attr['key_name'];
		$ret .= "</div>";
		$ret .= "<div class='obj_attr_val'>";
			$ret .= $attr['value_str'];
		$ret .= "</div>";
		$ret .= "</div>";
	}

	$ret .= "</div>";

	return $ret;
}

?>
