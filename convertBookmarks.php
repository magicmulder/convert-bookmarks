<?php
set_time_limit(900);
ini_set('max_execution_time', 300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$youtubeDL = 'youtube-dl -i --download-archive archive.txt --prefer-ffmpeg --merge-output-format mkv -v -o "/volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/';

$handle = fopen('bookmarks.html', 'r');
$handle2 = fopen('output.txt', 'a+');
$depth = 0;
$count = 0;
$offset = (isset($_REQUEST['offset'])?$_REQUEST['offset']:1);
$level[0] = '';
$level[1] = '';
$level[2] = '';
$level[3] = '';
#$line = '                 <DT><H3 ADD_DATE="1463170500" LAST_MODIFIED="1533323796">Yay</H3>';
while (($line = fgets($handle)) !== false) {
	$count++;
#	if ($count > 20) break;
	$regExpLabel = '/(.+)<DT><H3 ADD_DATE="(.+)" LAST_MODIFIED="(.+)">(.+)<\/H3>/iU';
	$regExpUrl = '/(.+)<A HREF="(.+)"/iU';
	preg_match_all($regExpLabel, $line, $match);
	if (isset($match[4][0])) {
		$indent = $match[1][0];
		$label = $match[4][0];
		$label = trim(preg_replace('/[^0-9A-Za-z _]/i', '', $label));
		$label = str_replace('quot', '', $label);
		$currLevel = (strlen($indent)-20)/4;
		$level[$currLevel] = $label;
		$level[$currLevel+1] = '';
		$level[$currLevel+2] = '';
		$level[$currLevel+3] = '';
		$level[$currLevel+4] = '';
		$level[$currLevel+5] = '';
		$level[$currLevel+6] = '';
#		echo  $currLevel. ": " . $label . "<br>";
#fwrite($handle2, $currLevel. ": " . $label . chr(10).chr(13));
	} else {
		if ($count < $offset) continue;
		preg_match_all($regExpUrl, $line, $match2);
		if (isset($match2[2][0])) {
			$link = $match2[2][0];
			$currPath = $level[0];
			if ($level[1] != '') $currPath .= '/' . $level[1];
			if ($level[2] != '') $currPath .= '/' . $level[2];
			if ($level[3] != '') $currPath .= '/' . $level[3];
			if ($level[4] != '') $currPath .= '/' . $level[4];
			if ($level[5] != '') $currPath .= '/' . $level[5];
			if ($level[6] != '') $currPath .= '/' . $level[6];
			$fileName = $link;
			$generatedCall = $youtubeDL . $currPath . '/%(title)s [%(resolution)s] [%(id)s].%(ext)s" ' . $link;
			fwrite($handle2, '#' . $count . chr(10));
			fwrite($handle2, $generatedCall . chr(10));
echo $generatedCall . "<br>";			
#			$arrStructure[$level[0]][$level[1]][$level[2]][$level[3]][] = $link;
		}
	}	
}
print_r($arrStructure);
fclose($handle);
fclose($handle2);
                    