<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$diane = new mysqli( "localhost", "root", null, "inventory" );

$thisobject = array(
	"id" => $diane->real_escape_string( $_GET['id'] ),
	"connector" => $diane->real_escape_string( $_GET['connector'] )
);

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/v2/commonjs.js"></script>
<link href='commonstyle.css' rel='stylesheet' type='text/css'>

<style>

.objdiv {
	width: 800px;
	border: 1px solid;
	margin: 10px auto;
	padding: 20px;
	vertical-align: middle;
}

.infolist {
	margin: auto;
}

</style>

<script>

info = <?php


	$info_ret = array();
	$infos = $diane->query( "SELECT * FROM attr WHERE obj_id = {$thisobject['id']}" );
	while( $info = $infos->fetch_assoc() ) {
		array_push( $info_ret, array(
			"k" => $info['key_name'],
			"v" => $info['value_str']
		) );
	}

	echo json_encode( $info_ret );

?>;

geneology = <?php

	$igotos = $diane->query( "SELECT * FROM objlink WHERE obj_from = {$thisobject['id']} AND connector = '{$thisobject['connector']}'" );
	$icomefroms = $diane->query( "SELECT * FROM objlink WHERE obj_to = {$thisobject['id']} AND connector = '{$thisobject['connector']}'" );

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

		$id = $icomefrom['obj_from'];

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

?>;

var thisobject = <?php echo json_encode( $thisobject ); ?>;

$(document).ready( function() {

	$maindiv = $("<div>").addClass( "maindiv" );

	var $objdiv = $("<div>").addClass( "objdiv" );
	var $infolist = $("<table>").addClass( "infolist" );
	$objdiv.append( $infolist );

	$infolist.append( $("<tr><td></td><td><b>KEY</b></td><td><b>VAL</b></td></tr>") );

	for( i in info ) {
		var thisrow = $("<tr>");
		thisrow.append(
			$("<td>").append(
				$("<span>").html( "del" ).click( ( function( kv, row ) {
					return function() {
						$.ajax( {
							url: "deleteattr.php",
							data: { "obj": thisobject.id, "k": kv.k, "v": kv.v },
							success: function() { row.remove(); }
						} );
					};
				} )( info[i], thisrow ) )
			),
			$("<td>" + info[i].k + "</td>"),
			$("<td>" + info[i].v + "</td>")
		);
		$infolist.append( thisrow );
	}

	var $duplicateobjbutton = newobjbutton( {
		"attrs": info
	} );

	$objdiv.append( $duplicateobjbutton );

	$kinp = $("<input>");
	$vinp = $("<input>");

	$kinp.add( $vinp ).keypress( function( e ) {

		var k = $kinp.val();
		var v = $vinp.val();

		if( e.charCode == 13 ) {
			fill = {
				"inboundlinks": [],
				"outboundlinks": [],
				"attrs": [ {
					"k": k,
					"v": v
				} ]
			};

			$.ajax( {
				"method": "GET",
				"url": "addinfo.php",
				"data": {
					"obj": thisobject.id,
					"info": JSON.stringify( fill )
				},
				"success": function() {
					$lastrow.before(
						$("<tr>").append(
							$("<td>").html( k ),
							$("<td>").html( v )
						)
					);
				}
			} );

		}

	} );

	$lastrow = $("<tr>").append(
		$("<td>").append( $kinp ),
		$("<td>").append( $vinp )
	);
	$infolist.append( $lastrow );

	$icomefromdiv = $("<div>").addClass( "objdiv" );
	$icomefromdiv.append( newobjbutton( { 
		"outboundlinks": [ {
			"obj": thisobject.id,
			"connector": thisobject.connector
		} ]
	} ) );
	for( var i in geneology.icomefrom )
		$icomefromdiv.append( 
			objdiv( geneology.icomefrom[i] ).append(
				$("<div>").mouseover( function() { $(this).css( "font-weight", "bold" ) } )
					.mouseout( function() { $(this).css( "font-weight", "normal" ) } )
					.css( {
						"cursor": "pointer",
						"display": "inline-block",
						"width": "100px"
					} )
					.html( "removelink" )
					.click( ( function( i ) {
						return function( e ) { 
							e.stopPropagation();

							$.ajax( {
								"method": "GET",
								"url": "removelink.php",
								"data": { 
									"obj_from": geneology.icomefrom[i].id,
									"connector": thisobject.connector,
									"obj_to": thisobject.id
								},
								"success": function() { alert("link removed"); }
							} );
						};
					} )( i ) )
			)
		);

	$igotodiv = $("<div>").addClass( "objdiv" );
	for( var i in geneology.igoto )
		$igotodiv.append( 
			objdiv( geneology.igoto[i] ).append(
				$("<div>").mouseover( function() { $(this).css( "font-weight", "bold" ) } )
					.mouseout( function() { $(this).css( "font-weight", "normal" ) } )
					.css( {
						"cursor": "pointer",
						"display": "inline-block",
						"width": "100px"
					} )
					.html( "removelink" )
					.click( ( function( i ) {
						return function( e ) { 
							e.stopPropagation();

							$.ajax( {
								"method": "GET",
								"url": "removelink.php",
								"data": { 
									"obj_from": thisobject.id,
									"connector": thisobject.connector,
									"obj_to": geneology.igoto[i].id
								},
								"success": function() { alert("link removed"); }
							} );
						};
					} )( i ) )
			)
		);

	$igotodiv.append( newobjbutton( { 
		"inboundlinks": [ {
			"obj": thisobject.id,
			"connector": thisobject.connector,
		} ]
	} ) );

	$(document.body).append( $maindiv.append(
		newobjbutton(),
		$igotodiv,
		$objdiv,
		$icomefromdiv
	) );

} );

</script>

<a href='search.php'>Back to Search</a>
