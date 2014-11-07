<style>

b {
	font-size: 30px;
}

button {
	font-size: 25px;
	width: 150px;
	height: 150px;
}

td {
font-size: 22px;
padding: 7px;
border-width: 0 1px 1px 0;
border-style: solid;
}

</style>

<title>Scannnnn</title>

<center>

<?php

$diane = new mysqli( "localhost", "root", null, "inventory" );

if( !empty( $_GET['id'] ) ) {
	$id = $_GET['id'];
	echo "<b>id = $id</b>";

	$attrs = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	if( $attrs->num_rows > 0 ) {
		echo " (existing object)";
	} else {
		echo " (this is a new object)";
	}
}

if( empty( $_GET['id'] ) ) {
	echo "<form action='' method='GET'>";
	echo "<input name='id' id='idinput'><button type='submit'>Open Object</button>";
	echo "<button type='button' onclick='document.getElementById(\"idinput\").value=Math.floor(Math.random()*1000001)'>Generate New</button>";
	echo "</form>";
	die( "You must provide an id. Scan something :)" );
}
$id = $_GET['id'];

if( isset( $_POST['check_in'] ) || isset( $_POST['check_out'] ) ) {

	$dir = 'out';
	if( !empty( $_POST['check_in'] ) ) $dir = 'in';

?>

	Add a message
	<form action='' method='post'>
		<input name='id' type='hidden' value='<?php echo $_POST['id']?>'>
		<input name='action' type='hidden' value='<?php echo $dir?>'>
		<textarea name='message'></textarea>
		<input type='submit'>
	</form>

<?php

} else if( !empty( $_POST['message'] ) ) {

	$message = $diane->real_escape_string( $_POST['message'] );
	if( $message == '' ) $message = 'No Message';
	$action = $_POST['action'] . ": " . $message;

	$diane->query( "INSERT INTO obj_actions( obj, action_desc, dt ) VALUES( $id, '$action', NOW() )" );

	echo "Tis done";

	echo "<button onclick='window.location=\"/v1/scan.php?id=$id\"'>Go Back</button>";

} else if( isset( $_POST['add_attr'] ) ) {

	$id = $_POST['id'];

	if( !empty( $_POST['key'] ) && !empty( $_POST['val'] ) ) {
		$key = $diane->real_escape_string( $_POST['key'] );
		$val = $diane->real_escape_string( $_POST['val'] );
		$diane->query( "INSERT INTO attr( obj_id, key_name, value_type, value_str ) VALUES ( $id, '$key', 'str', '$val' )" );
		echo "<b>(inserted '$key' = '$val')</b>";
	}

?>
	<form action='' method='post'>
		<input name='id' type='hidden' value='<?php echo $id;?>'>
		<input name='key'><input name='val'><button name='add_attr'>Add Attr</button>
		<button onclick='window.location="/v1/scan.php?id=<?php echo $id;?>"' name='goback'>Go Back</button>
	</form>
<?php
} else if( isset( $_POST['view_link'] ) ) {

	if( !empty( $_POST['delete'] ) ) {
		$attrs = $diane->query( "DELETE FROM objlink WHERE id = ".$_POST['delete'] );
		echo "DELETED link ".$_POST['delete'];
	}

	$attrs = $diane->query( "SELECT * FROM objlink WHERE obj_from = $id OR obj_to = $id" );
	if( $attrs->num_rows == 0 )
		echo "No links";
	else {
		echo "<form action='' method='POST'>";
		echo "<input type='hidden' name='id' value=$id/>";
		echo "<input type='hidden' name='view_link' value=$id/>";
		echo "<table>";
		echo "<tr><td></td><td><b>Obj_From</b></td><td><b>Connector</b></td><td><b>Obj_To</b></td></tr>";
		while( $attr = $attrs->fetch_assoc() ) {
			echo "<tr>";
			echo "<td>";
			echo "<button name='delete' value='".$attr['id']."' style='height:35px'>Delete</button>";
			echo "</td>";
			echo "<td>";
			echo "<a href='/v1/scan.php?id=".$attr['obj_from']."'>".$attr['obj_from']."</a>";
			echo "</td>";
			echo "<td>";
			echo $attr['connector'];
			echo "</td>";
			echo "<td>";
			echo "<a href='/v1/scan.php?id=".$attr['obj_to']."'>".$attr['obj_to']."</a>";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";
	}

	echo "<button onclick='window.location=\"/v1/scan.php?id=$id\"'>Go Back</button>";

} else if( isset( $_POST['view_attr'] ) ) {

	if( !empty( $_POST['delete'] ) ) {
		$attrs = $diane->query( "DELETE FROM attr WHERE id = ".$_POST['delete'] );
		echo "DELETED attribute ".$_POST['delete'];
	}

	$attrs = $diane->query( "SELECT * FROM attr WHERE obj_id = $id" );
	if( $attrs->num_rows == 0 )
		echo "No attrs";
	else {
		echo "<form action='' method='POST'>";
		echo "<input type='hidden' name='id' value=$id/>";
		echo "<input type='hidden' name='view_attr' value=$id/>";
		echo "<table>";
		echo "<tr><td></td><td><b>Key</b></td><td><b>Val</b></td></tr>";
		while( $attr = $attrs->fetch_assoc() ) {
			echo "<tr>";
			echo "<td>";
			echo "<button name='delete' value='".$attr['id']."' style='height:35px'>Delete</button>";
			echo "</td>";
			echo "<td>";
			echo $attr['key_name'];
			echo "</td>";
			echo "<td>";
			echo $attr['value_str'];
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</form>";

	}

	echo "<button onclick='window.location=\"/v1/scan.php?id=$id\"'>Go Back</button>";

} else if( isset( $_POST['view_actions'] ) ) {

	$obj_actions = $diane->query( "SELECT * FROM obj_actions WHERE obj = $id" );
	if( $obj_actions->num_rows == 0 )
		echo "No obj_actions";
	else {
		echo "<table>";
		echo "<tr><td><b>Action</b></td><td><b>When</b></td></tr>";
		while( $obj_action = $obj_actions->fetch_assoc() ) {
			echo "<tr>";
			echo "<td>";
			echo $obj_action['action_desc'];
			echo "</td>";
			echo "<td>";
			echo $obj_action['dt'];
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";

		echo "<button onclick='window.location=\"/v1/scan.php?id=$id\"'>Go Back</button>";
	}

} else if( isset( $_POST['add_link'] ) ) {

	$id = $_POST['id'];

	if( !empty( $_POST['connector'] ) && !empty( $_POST['obj_to'] ) ) {
		$connector = $diane->real_escape_string( $_POST['connector'] );
		$obj_to = intval( $_POST['obj_to'] );
		$diane->query( "INSERT INTO objlink( obj_from, connector, obj_to ) VALUES ( $id, '$connector', $obj_to )" );
		echo "<b>(inserted $id .. '$connector' .. $obj_to)</b>";
	}

?>
	<form action='' method='post'>
		<input name='id' type='hidden' value='<?php echo $id;?>'>
		<input name='connector'><input name='obj_to'><button name='add_link'>Add Link</button>
		<button onclick='window.location="/v1/scan.php?id=<?php echo $id;?>"' name='goback'>Go Back</button>
	</form>
<?php

} else {
?>

		<form action='' method='post'>
			<input name='id' type='hidden' value='<?php echo $id?>'>
			<button name='check_in'>Check in</button>
			<button name='check_out'>Check out</button><br>
			<button name='add_attr'>Add Attr</button>
			<button name='add_link'>Add Link</button><br>
			<button name='view_attr'>View Attrs</button>
			<button name='view_link'>View Links</button>
			<button name='view_actions'>View Acts</button><br>
			<button type='button' onclick='window.location = "/v1/scan.php"'>Enter a new ID</button>
		</form>


<?php
}
?>

</center>
