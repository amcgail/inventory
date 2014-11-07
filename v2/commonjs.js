function getRandomInt (min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function objdiv( info ) {
	var loc = '/v2/object.php?id=' + info.id;

	var connector = getUrlVars()['connector'] || null;
	if( connector != null )
		loc += "&connector=" + connector;

	var $objdiv = $("<div>").html( JSON.stringify( info ) ).addClass( "result" );

	var $deleteobj = $("<div>").html( "deleteobj" ).addClass( "deleteobj" ).click( function( e ) {
		e.stopPropagation();
		if( !confirm( "ARE YOU SURE!??" ) ) return;

		$.ajax( {
			"method": "GET",
			"url": "deleteobj.php",
			"data": {"obj": info.id},
			"success": function( resp ) {
				$objdiv.html("");
			}
		} );
	} ).css( {
		"cursor": "pointer",
		"width": "100px",
		"display": "inline-block"
	} ).mouseover( function() {
		$(this).css( { "font-weight": "bold" } );
	} ).mouseout( function() {
		$(this).css( { "font-weight": "normal" } );
	} );

	$objdiv.append( $deleteobj );

	return $objdiv.click( function() {
		console.log( loc );
		window.location = loc;
	} );
}

function newobjbutton( prefill ) {
	prefill = $.extend( {
		"inboundlinks": [],
		"outboundlinks": [],
		"attrs": []
	}, prefill );

	return $("<div>").html( "NEW OBJECT ("+JSON.stringify( prefill )+")" ).css( {
		"text-align": "center",
		"text-decoration": "underline",
		"font-size": "18px",
		"cursor": "pointer"
	} ).click( function() {

		var newobjid = getRandomInt( 1, 10000000 );

		$.ajax( {
			"method": "GET",
			"url": "addinfo.php",
			"data": {
				"obj": newobjid,
				"info": JSON.stringify( prefill )
			},
			"success": function() {
				window.location = "/v2/object.php?id=" + newobjid;
				return;
			}
		} );

	} );
}
