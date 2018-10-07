<?php
/**
*	Chrome Bookmark Downloader 
*
*	v1.0.2
*
*	(c) 2018 magicmulder <developer@muldermedia.de>
*
* 	Call:
*
*	php convertBookmarks.php bookmarks.html dl-bookmarks.sh && dl-bookmarks.sh
*
* 	Example call in cron:
*
*	php /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/convertBookmarks.php /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/bookmarks.html /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/dl-bookmarks.sh && /volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/dl-bookmarks.sh
*
*/

$MAXDEPTH = 10;
$youtubeDL = 'youtube-dl -i --download-archive archive.txt --prefer-ffmpeg --merge-output-format mkv -v -o "/volume1/AdorableIllusion/Pix/XXX_Bookmarked_Videos/';
$youtubeDLFilename = '/%(title)s [%(resolution)s] [%(id)s].%(ext)s"';

$bookmarkFile = 'bookmarks.html';
$bookmarkScript = 'dl-bookmarks.sh';
if (isset($argv[1])) {
	$bookmarkFile = $argv[1];
}
if (isset($argv[2])) {
	$bookmarkScript = $argv[2];
}

$handle = fopen($bookmarkFile, 'r');
$handle2 = fopen($bookmarkScript, 'w');
$count = 0;
for ($i = 0; $i <= $MAXDEPTH; $i++) {
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
		for ($i = 1; $i <= $MAXDEPTH; $i++) {
			$level[$currLevel+$i] = '';
		}
	} else {
		preg_match_all($regExpUrl, $line, $match2);
		if (isset($match2[2][0])) {
			$count++;
			$link = $match2[2][0];
			$currPath = $level[0];
			for ($i = 1; $i <= $MAXDEPTH; $i++) {
				if ($level[$i] != '') $currPath .= '/' . $level[$i];
			}
			$generatedCall = $youtubeDL . $currPath . $youtubeDLFilename . ' ' . $link;
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

                    