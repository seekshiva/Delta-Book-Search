<!doctype html>
<html>
<head>
<title><?php if(isset($_GET['q'])&&($_GET['q']!="")) echo $_GET['q']." - "; ?>Delta Book Search</title>
<?php
include "functions.lib.php";
connect();
$style[]="./fbtheme.css";
$style[]="./google-theme.css";
$style[]="./bing-theme.css";
if(!isset($_COOKIE['deltasearchtheme'])) {
$_COOKIE['deltasearchtheme']=0;
}
$sId=$_COOKIE['deltasearchtheme'];
?>
<link rel="stylesheet" href="<?php echo $style[$sId] ?>">
<script language="javascript" src="includes/script/jquery-latest.js"></script>
<script language="javascript" src="includes/script/ajax.js"></script>
</head>
<body>
<?php
$set=true;
global $fileType;
global $resultsPerPage;
global $exactSearch;
$fileType="ALL";
$resultsPerPage=10;
$exactSearch=true;

if(isset($_GET['viewcache'])) {
echo "<table border=\"0\" cellpadding=\"0\" id=\"cacheMetaData\"><tr><th width=\"100px\">Book : </th><td>".getFileName($_GET['viewcache'])."</td>";
echo "<tr><th>Author : </th><td>".getAuthor($_GET['viewcache'])."</td></table>";
echo "<a style=\"float:left; margin-left:20px;\" href=\"javascript:window.back();\"> <- Back to search results</a><div style=\"padding-left:200px;\">You searched for <span class=\"cacheImportant\">{$_GET['q']}</span></div><hr>";
echo "<div style=\"margin:20px; margin-top:10px; padding:20px; border:2px solid #333; overflow:scroll; height:500px; \">".viewCache($_GET['q'],$_GET['viewcache'])."</div>";
die();
}
?>
<div id="bluebar"> </div>
<div id="wrapper">
<div id="pageHead">
<div id="menu">
<div>
<a href="http://delta.nitt.edu/books/">Home</a>
<a href="./admin.php">Admin</a>
<a id="googleMore" href="#">Settings</a>
<div id="pSettings">
You can choose between the following themes for your search results to be displayed:
<ul>
<li><a id="fb">Facebook</a></li>
<li><a id="google">Google</a></li>
<li><a id="bing">Bing</a></li>
</ul>
</div>
</div></div>
<div id="pageLogo">
<a href="http://delta.nitt.edu/"></a>
</div>
<div id="headerC">
<div id="header">
<form name="topF" action="" method="get">
<div id="topQC">
<input type="text" name="q" id="topQ" size="40" value="<?php if(isset($_GET['q'])) echo htmlspecialchars($_GET['q']); ?>">
<div id="topAutoSuggest">
<?php /*<div class="suggestion">suggestion 1</div>
<div class="suggestion">suggestion 2</div>
<div class="suggestion">suggestion 3</div>*/
?>
</div>
</div></form>
</div></div></div>
<div id="pageBody">
<?php
if(isset($_GET['q'])) {
$q=getQuery($_GET['q']);
echo search($q);
?>
</div>
<div id="bottomQC" style="text-align:left; padding-left:180px;">
<form action="" method="get">
Delta Book search : <input type="text" name="q" size="50" id="bottomQ" value="<?php if(isset($_GET['q'])) echo htmlspecialchars($_GET['q']); ?>">
</form>
</div></div>
<?php
}
else {/*
?>
<h4>Delta book search</h4>
<p>Delta book search</p>
</div>
<?php
*/}
?>
<div id="footer" style="">
<div id="aboutText">
<a href="">About</a>
<a href="">Developers</a>
<a href="">Privacy</a>
<a href="">Terms</a>
</div>
&copy; seek and delta
</div>
</div>
</body>
</html>
