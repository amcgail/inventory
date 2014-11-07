<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/v2/commonjs.js"></script>
<link href='commonstyle.css' rel='stylesheet' type='text/css'>

<script type='text/javascript'>

$(document).ready( function() {

	searchbar = $("<input>").css( "width", "100%" )
		.attr( "placeholder", "Search" )
		.blur( function() {
			search( $(this).val() );
		} );

	$results = $("<div>")

	$(document.body).append( 
		searchbar,
		$results
	);

} );

function search( query ) {
	$.ajax( {
		"url": "/v2/query.php",
		"method": "GET",
		"data": { "query": query },
		"success": function( resp ) {
			$results.html( "" );

			items = $.parseJSON( resp );
			for( i in items )
				$( $results ).append( objdiv( items[i] ) );
		}
	} );
}

</script>
