<?php
/**
*	Chrome Bookmark Downloader 
*
*	v1.0.1
*
*	(c) 2018 magicmulder <developer@muldermedia.de>
*
* 	Example call in cron:
*
*	php /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/convertBookmarks.php /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/bookmarks.html /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/dl-porn.sh
*
*/


$youtubeDL = 'youtube-dl -i --download-archive archive.txt --prefer-ffmpeg --merge-output-format mkv -v -o "/volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/';

$bookmarkFile = 'bookmarks.html';
$bookmarkScript = 'dl-porn.sh';
if (isset($argv[1])) {
	$bookmarkFile = $argv[1];
}
if (isset($argv[2])) {
	$bookmarkScript = $argv[2];
}

$handle = fopen($bookmarkFile, 'r');
$handle2 = fopen($bookmarkScript, 'w');
$count = 0;
for ($i = 0; $i <= 10; $i++) {
	$level[$i] = '';
}
while (($line = fgets($handle)) !== false) {
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
		for ($i = 1; $i <= 10; $i++) {
			$level[$currLevel+$i] = '';
		}
	} else {
		preg_match_all($regExpUrl, $line, $match2);
		if (isset($match2[2][0])) {
			$count++;
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
		}
	}	
}
fclose($handle);
fclose($handle2);
chmod($bookmarkScript, '0777');
chown($bookmarkScript, 'kai2');
chgrp($bookmarkScript, 'users');

                    