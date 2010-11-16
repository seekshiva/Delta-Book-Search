<?php
	$set=true;
	include "functions.lib.php";
	connect();
	function checkbooks() {
		echo "The following books have been newly detected. Select the books you want to index now and click the <b>Add</b> button.<hr><table id=\"detectTable\" border='1' cellspacing='0'>";
		echo "<tr><th>File path</th><th>File Name</th></tr>";
		checkNewFiles("/home/seek/Desktop");
		echo "</table>";
	}
	if(isset($_GET['action'])) {
	switch($_GET['action']) {
	case "checknew"://die("chec");
	echo "<input type=\"button\" id=\"addFilesButton\" value=\"Add Files\">";
		checkbooks();
		break;
	}
	die();
	}
	if(isset($_POST['addfile'])) {
	echo "Adding file : ".htmlspecialchars($_POST['filepath'])."/".htmlspecialchars($_POST['filename'])."<br>";
	addFile(($_POST['filepath']),($_POST['filename']));
	die();
	}
?>
<!doctype html>
<html>
<head>
<title>Delta book search - administrator</title>
<link rel="stylesheet" href="includes/themes/google-theme.css"> 
<style>
a {color:#00e; text-decoration:none; cursor:pointer; }
a:hover {text-decoration:underline; }
</style>
<script src="includes/script/jquery-latest.js"></script>
<script src="includes/script/script.js"></script>
</head>
<body>
<div id="menu">
<a href="./">Delta book search</a>
</div>
What do you want to do ? <br />
<ul id="adminAction">
<li><a id="checkNewBooks">Check for new books</a></li>
</ul>
<div id="temp"></div>
<div id="currProcess"></div><hr>
<div id="displayResults"></div>
</body>
</html>
