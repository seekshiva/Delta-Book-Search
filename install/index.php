<?php
$len=strlen(file_get_contents("../config.inc.php"));
if($len>0)
//header('Location:../');
//else
$path=substr(getcwd(),0,strripos(getcwd(),'/'));
$perms=substr(sprintf("%o",(fileperms($path."/config.inc.php"))),-4);
if($perms!=="0777") {
echo "<style>.imp {font-weight:bold; font-size:80%; color:#7387a9;}</style>";
echo "<h2>File permissions need to be changed</h2>The <span class=\"imp\">config.inc.php</span> file does not have the proper write permissions. Use <span class=\"imp\">chmod 777 ".$path."/config.inc.php</span> and then <a href=\"\">click here</a> to refresh the page. You can change the file permissions to its original state after installation.";
die();
}
//echo $str;
?>
<h2>Delta Book search installation</h2>
<div>

</div>
