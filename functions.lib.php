<?php
/**
 * @package deltabooks
 * @file functions.lib.php
 * @brief Contains functions which are common to many tasks and very frequently used.
 * @author Shiva Nandan <seekshiva[at]gmail.com>.
 * @copyright (c) 2010 Team Delta Force.
 */
/** To connect to the database*/
function connect() {
	include("config.inc.php");
	$con=mysql_connect($dbs_host,$dbs_dbuname,$dbs_dbpass) or die("Could not connect to server");
	mysql_select_db($dbs_dbname,$con) or die("Could not connect to database");
	return $con;
}

/** To disconnect from the database once query is over*/
function disconnect() {
	mysql_close();
}

/** To escape the database queries for avoiding SQL injection attacks */

function escape($query)
{
	if (!get_magic_quotes_gpc()) {
	    $query = addslashes($query);
	}
	return $query;
}

/** To prevent XSS attacks  */

function safe_html($html)
{
	return htmlspecialchars(strip_tags($html));
}

function search($q) {
	$q=escape($q);
	$resultsPerPage=10;
	$start=(isset($_GET['start'])&&($_GET['start']!=""))?$_GET['start']:1;
	$start=($start-1)*$resultsPerPage;
	$query1="SELECT `file_id`, 10 AS relevance FROM `documents` WHERE `file_name` LIKE '%$q%'";
	$query2="SELECT `file_id`, 8 AS relevance FROM `documents` WHERE `file_name` LIKE '%".str_replace(" ","%",$q)."%' OR `path` LIKE '%".str_replace(" ","%",$q)."%'";
	$query3="SELECT `file_id`, 5 AS relevance FROM `doc_contents` where `content` like '%$q%'";
	$query4="SELECT `file_id`, 2 AS relevance FROM `doc_contents` where content like '%".str_replace(" ","%",$q)."%'";
	$query="SELECT `file_id`,sum(relevance) from ( ".$query1." UNION ".$query2." UNION ".$query3.") results GROUP BY `file_id` ORDER BY relevance DESC;";

echo $query;
	$startTime= (time()+microtime());
	$result=mysql_query($query);
	$endTime= (time()+microtime());
	$resultCount=mysql_num_rows($result);

	mysql_query("INSERT INTO `search_list` (`search_term`) VALUES ('".escape($q)."');");
	echo "<div id=\"resultCount\">About <b>".$resultCount."</b> documents (<b>".substr($endTime-$startTime,0,7)."</b> seconds)</div>";
	$i=1;
	while($row=mysql_fetch_array($result)) {
		$arr[]=$row['file_id'];$i+=1;
	}
	$list="";
	for($i=$start;$i<min(count($arr),$start+$resultsPerPage);$i+=1)	
		$list.=$arr[$i].", ";
//foreach($arr as $arrElement)
//	$list.=$arrElement.", ";
	$list= substr($list,0,strlen($list)-2);echo "<!--".$list."-->";
	$query4 = "SELECT distinct(`file_id`),SUBSTRING(content,GREATEST(1,(LOCATE('$q',content)-150)),300) AS cont FROM `doc_contents` WHERE `file_id` IN (".$list.");";
//echo $query4;
	$result=mysql_query($query4);
	echo "<div id=\"searchResults\">";
	while($row=mysql_fetch_array($result)) {
$fid=$row['file_id'];
	$cont[$fid]=$row['cont'];
	}
	for($i=$start;$i<min(count($arr),$start+$resultsPerPage);$i+=1)	
	echo "<div class=\"resultlet\"><div class=\"resultTitle b p\"><span class=\"filetype\">[".getFileType($arr[$i])."]</span> <span class=\"resultHead\">".highlight(getFileName($arr[$i]),$q)."</span></div><div class=\"resultSnippet\"><div>...".highlight(getAptContent($arr[$i],$q),$q)."...</div><span class=\"uri\">".highlight(getUri($arr[$i]),$q)."</span> - <a href=\"./?q=".str_replace(' ','+',$q)."&viewcache=$arr[$i]\" target=\"_blank\">Cached</a> - <a href=\"http://".getUri($arr[$i])."\">Download</a></div></div>";
	echo "</div><div id=\"searchPageList\">";
	$pStart=($start/$resultsPerPage-5<0)?1:$start/$resultsPerPage-4;
	$pEnd=($start/$resultsPerPage+10<($resultCount/$resultsPerPage))?$start/$resultsPerPage+10:($resultCount/$resultsPerPage)+1;
	if(($start/$resultsPerPage)>0) echo "<a href=\"./?q=".str_replace(' ','+',$_GET['q'])."&start=".($start/$resultsPerPage)."\">Prev</a> ";
	for($i=$pStart;$i<=$pEnd;$i+=1) {
	if(($start/$resultsPerPage)+1==$i)
		echo $i;
	else
		echo " <a href=\"./?q=".str_replace(' ','+',$_GET['q'])."&start=$i\">".$i."</a> ";
	}
	if(($start/$resultsPerPage)+1<($resultCount/$resultsPerPage)) echo "<a href=\"./?q=".str_replace(' ','+',$_GET['q'])."&start=".($start/$resultsPerPage+2)."\">Next</a> ";
//	echo "The query is $q.";
}

function getQuery($q) {
	global $fileType;
	if(strpos($q,"filetype:")) {
		$fileType=substr($q,strpos($q,"filetype:")+9);
		if(strpos($fileType," ")) $fileType=substr($fileType,0,strpos($fileType," "));
		$q=str_replace('filetype:'.$fileType,' ',$q);
		$fileType=strtoupper($fileType);
	}
	return $q;
}


/**
 * Checks recursively from the root directory if any new file has been added
 * and adds it to the database, whenever any new file is encountered
 */
function checkNewFiles($path) {
if(is_dir($path)) {
	//echo "<br>inside folder : ".$path;
	$dh=opendir($path);
	while(($file=readdir($dh))!==false)
	if($file!="."&&$file!="..") {
	$x=str_replace(' ','\ ',$file);
		if(is_dir($path."/".$file)) {
			//if($path!="/home/seek/Desktop/dm/Technical")
			checkNewFiles($path."/".$file);
		}
		if(ereg(".pdf",$file)||ereg(".txt",$file)||ereg(".htm",$file)) {
			$query="SELECT count(*) FROM `documents` WHERE `file_name`='".htmlspecialchars(escape($file))."' AND `path`='".htmlspecialchars(escape($path))."';";
			$result=mysql_fetch_array(mysql_query($query));
			if($result['count(*)']==0) {
				//echo $query."<br>";
				echo "<tr><td>".$path."</td><td>".$file."</td></tr>";
			}
		}
	}
	closedir($dh);
}
}

function highlight($str,$searchstr) {
	return str_ireplace($searchstr,'<span class="searchstr">'.$searchstr.'</span>',$str);
}

/**add file if not present*/
function addFile($path,$file) {
	//yet to add
	$file=escape($file);
	$path=escape($path);
	echo "<br>Path : ".$path."<br>File : ".$file."<br>";
	$query="INSERT INTO `documents`
		(`file_id`,`file_type`,`file_name`,`path`)
		VALUES
		(NULL,'".getFileType($file)."','".$file."','".$path."');";
	mysql_query($query) or die("couldn't add book to database : ".$query."<br>".mysql_error());
	$row=mysql_fetch_array(mysql_query("SELECT `file_id` FROM `documents` WHERE file_name='$file' AND path='$path';"));
	$fileId=$row['file_id'];

	if(ereg(".pdf",$file)) {
		exec("touch /tmp/temp-".$fileId.".txt");
		$str= "pdftotext ".ereg_replace(' ','\ ',$path)."/".ereg_replace(' ','\ ',$file)." /tmp/temp-".$fileId.".txt";
		file_put_contents('/tmp/temp-'.$fileId.'.txt','');
		exec($str);
		$contents=file_get_contents('/tmp/temp-'.$fileId.'.txt');
		exec("rm /tmp/temp-".$fileId.".txt");

		$contents=ereg_replace('â€™',"'",$contents);
	}
	else if(ereg(".htm",$file)) {
		$xml = new DOMDocument();
		$xml->loadHTMLFile($path."/".$file);
		/**script to remove contents within script tag */
		foreach($xml->getElementsByTagName('script') as $script)
		$script->nodeValue="";
		/**script to remove contents within style tag */
		foreach($xml->getElementsByTagName('style') as $style)
		$style->nodeValue="";
		$contents=$xml->getElementsByTagName('body')->item(0)->nodeValue;
	}
	else if(ereg(".chm",$file)) {
		//use the "extract_chmLib <chmfile> <outdir>" command in the terminal
	}
	else {
		$contents=file_get_contents($path."/".$file);
	}
	//echo "<br>wc : ".substr($contents,0,1100)."<br>";
	if(strlen($contents)==0)
	$contents="no searchable text found in this book.";
	//addContent($fileId,$contents);
	$contents=htmlspecialchars(escape($contents));
	for($i=0;$i<strlen($contents);$i+=50000)
		addContent($fileId,substr($contents,$i,50100));
	return true;
}
/**
 * Adds contents of a document into the database... it adds the contents to the 
 * `doc_contents` table in the database
 */
function addContent($fileId,$content) {
$query="INSERT INTO `doc_contents` 
	(`content_id`,`file_id`,`content`)
	 VALUES 
	(NULL,'$fileId','$content');";
mysql_query($query) or die("failed to add : <hr>".$content."<hr>".mysql_error());
}

/**
 * used to delete a document from the database. this function is called when a
 * document that was previously indexed is now missing
 */
function deleteDoc($fileId) {
$query="DELETE FROM `doc_contents` WHERE `file_id`=".$fileId.";";
mysql_query($query) or die("failed to delete file #".$fileId);
}

function getAptContent($fileId,$q) {
	//$query="SELECT `file_name` FROM `documents` WHERE `file_id`='$fileId';";
	$query = "SELECT SUBSTRING(content,GREATEST(1,(LOCATE('$q',content)-150)),300) AS cont FROM `doc_contents` WHERE `file_id`='$fileId' LIMIT 1;";
	$row=mysql_fetch_array(mysql_query($query));
	$content= $row['cont'];
	return $content;
}

function getFileName($fileId) {
	$query="SELECT `file_name` FROM `documents` WHERE `file_id`='$fileId';";
	$row=mysql_fetch_array(mysql_query($query));
	$fileName= $row['file_name'];
	return stripslashes(substr($fileName,0,strripos($fileName,'.')));
}

function getUri($fileId) {
	$query="SELECT `path`,`file_name` FROM `documents` WHERE `file_id`='$fileId';";
	$row=mysql_fetch_array(mysql_query($query));
	return "delta.nitt.edu/books".substr($row['path'],18)."/".$row['file_name'];
}

function getAuthor($fileId) {
	return "not available";
}

function getFileType($fileId) {
	if(strpos($fileId,'.')) { $type= strtoupper(substr($fileId,strripos($fileId,'.')+1));}
	else {
		$query="SELECT `file_type` FROM `documents` WHERE `file_id`=$fileId;";
		$row=mysql_fetch_array(mysql_query($query));
		$type= $row['file_type'];
	}
	return ($type=="HTM")?"HTML":$type;
}

function viewCache($q,$fileId) {
	$cache="";
	$query="SELECT * FROM `documents` WHERE `file_id`='$fileId';";
	if(mysql_num_rows(mysql_query($query))==0) 
	return "file not present";

	$query="SELECT * FROM `doc_contents` WHERE `file_id`='$fileId' ORDER BY `content_id`;";
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)) {
	$cache .= substr($row['content'],0,50000);
	}
	$cache=str_replace('/n','<br>',$cache);
	//$cache=htmlspecialchars($cache);
	$cache=str_replace($q,'<span class="cacheImportant">'.$q.'</span>',$cache);
	return "<pre>".($cache)."</pre>";
}
?>
