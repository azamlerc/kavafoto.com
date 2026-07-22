<?php
include('../Shoebox/shared/layout.php');
include('../Shoebox/catalog/info.php');
include('../Shoebox/shared/category.php');
include('../Shoebox/catalog/dates.php');

$docroot = '../Shoebox/';

echo("<html>\n");
echo("<head>\n");
echo("\t<title>KavaFoto - Concerts</title>\n");
echo("\t<meta name=\"generator\" content=\"" . $GLOBALS['generator'] . "\" />\n");
echo("\t<meta name=\"author\" content=\"" . $GLOBALS['author'] . "\" />\n");
echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"../Shoebox/browser/styles.css\" media=\"screen\" />");
echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"../Shoebox/browser/" . theme('colors.css') . "\" media=\"screen\" />\n");
echo("\t<link rel=\"shortcut icon\" href=\"browser/images/favicon.png\" type=\"image/x-png\" />\n");
echo("</head>\n");

?>
<body class="index">
<table border="1"> 
<tr>
	<td>Date</td>
	<td>Name</td>
	<td>Location</td>
	<td>Performers</td>
	<td>Composers</td>
	<td>Pieces</td>
</tr>
<?php

include('concerts.php');

function linkify($text) {
	if ($text == 'Jiri') {
		$text = 'Ji&#345;&iacute; Kade&#345;&aacute;bek';
		$catid = '611284601-1416102853';
	}
	if ($text == 'Den Haag Allstars') {
		$text = 'D&euro;N HAAG<br>A&pound;&pound;$TAR$ &euro;NS&euro;MB&pound;&euro;';
		$catid = '693029811-1094383512';
	}
	if ($text == 'Pepe') {
		$text = 'Pep&eacute; Garcia';
		$catid = '176411966-1789919726';
	}
	
	if ($catid) {
		return "<a href=\"../Shoebox/index.php?id=$catid\">$text</a>";
	}
	
	$open = strpos($text, '[');
	$close = strpos($text, ']');
	
	if ($open === false) return $text;
	
	$prefix = substr($text, 0, $open);
	$link = substr($text, $open + 1, $close - $open - 1);
	$suffix = substr($text, $close + 1, strlen($text) - $close - 1);

	return "<a href=\"../Shoebox/index.php?search=$link\">$prefix$link$suffix</a>";
}

function arrayify($text, $catid = null) {
	$output = '';
	if (is_array($text)) {
		foreach ($text as $part) {
			$output .= linkify($part) . "<br>";
		}
	} else {
		if (strpos($text, '[') !== false) {
			$output .= linkify($text);
		} else if ($catid) {
			$output = "<a href=\"../Shoebox/index.php?id=$catid\">$text</a>";
		} else {
			$output = $text;
		}
	}
	return $output;
}

foreach ($concerts as $concert) {
	echo('<tr>');
	
	$date = sprintf("%d.%d.%d", $concert['day'], $concert['month'], $concert['year']);
	$date_path = sprintf("%d/%02d/%02d", $concert['year'], $concert['month'], $concert['day']);
	
	$date_catid = $dates[$date_path];
	$catid = $concert['id'];
	// if (!$catid) $catid = $date_catid;
	
	echo("<td valign=\"top\" align=\"right\"><a href=\"../Shoebox/index.php?id=$date_catid\">$date</a></td>");
	echo('<td valign="top" nowrap>' . arrayify($concert['name'], $catid) . '</td>');
	echo('<td valign="top" nowrap>' . arrayify($concert['where']) . '</td>');
	echo('<td valign="top" nowrap>' . arrayify($concert['performers']) . '</td>');
	echo('<td valign="top" nowrap>' . arrayify($concert['composers']) . '</td>');
	echo('<td valign="top">' . arrayify($concert['pieces']) . '</td>');
	echo('</tr>');
}

?>
</body>
</html>