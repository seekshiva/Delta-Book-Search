<?php
$set=1;
$set=true;
include "functions.lib.php";
connect();
//die("hi")
$query="SELECT `search_term`,COUNT(*) FROM `search_list` WHERE `search_term` LIKE '".$_REQUEST['q']."%' GROUP BY `search_term` ORDER BY COUNT(*) DESC LIMIT 0,5;";
$result=mysql_query($query);
echo "<div id=\"selectedSuggestion\" class=\"suggestion\">".$_REQUEST['q']."</div>";
while($row=mysql_fetch_array($result)) {
if($row['search_term']!==$_REQUEST['q'])
echo "<div class=\"suggestion\">".htmlspecialchars(substr($row['search_term'],0,70))."</div>";
$set=2;
}
?>
