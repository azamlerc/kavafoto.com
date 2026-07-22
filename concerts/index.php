<?php

include('../Shoebox/catalog/info.php');
include('../Shoebox/catalog/dates.php');
include('../Shoebox/shared/category.php');
include('../Shoebox/shared/layout.php');
include('concerts.php');

$theme = cookie_monster('theme', 'dark');
if ($theme != 'light') $shuffle = 'dark'; 

$docroot = '../Shoebox/';

$filter = $_GET['filter'];

$totals = array(
	'year' => array(),
	'genre' => array(),
	'where' => array(),
	'ensembles' => array(),
	'performers' => array(),
	'composers' => array(),
	'name' => array(),
	'pieces' => array(),
);

function add_to_totals($key, $value) {
	global $totals;
	
	if (is_array($value)) {
		foreach($value as $subvalue) {
			add_to_totals($key, $subvalue);
		}
	} else if ($value) {
		$value = str_replace('[', '', $value);
		$value = str_replace(']', '', $value);
		$totals[$key][$value] = $totals[$key][$value] + 1;
	}
}

$displayed_count = 0;

foreach ($concerts as $concert) {
	global $filter;
	
	if (!$filter || filter_concert_terms($concert, $filter)) { 
		add_to_totals('year', $concert['year']);
		add_to_totals('where', $concert['where']);
		add_to_totals('genre', $concert['genre']);
		add_to_totals('ensembles', $concert['ensemble']);
		add_to_totals('composers', $concert['composers']);
		add_to_totals('performers', $concert['performers']);
		add_to_totals('name', $concert['name']);
		add_to_totals('pieces', $concert['pieces']);
		
		$displayed_count++;
	}
}

if ($displayed_count == 0) {
	header("Location: ../category?search=$filter");
}

?>
<html>
<head>
	<title>KavaFoto - Concerts</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="Author" content="Andrew Zamler-Carhart">
	<meta name="Company" content="KavaFoto">
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-png" />
	<link rel="stylesheet" type="text/css" href="../<?php echo(theme('styles.css')); ?>" media="screen" />
</head>
<body>
<table border="0" width="1100" cellspacing="0" cellpadding="0">
<tr><td width="350" valign="top">
<img src="../images/spacer.gif" width="10" height="10"><br>
<span class="headline"><a href="../index.php">&nbsp;KavaFoto</a></span><br>
<img src="../images/spacer.gif" width="350" height="1200"><br>
<?php

if ($displayed_count > 4) {

	echo('<table border="0" cellspacing="20" cellpadding="0"><tr><td>');

	foreach ($totals as $key => $values) {
		echo("$key$br");
	
		$value_keys = array_keys($values);
		if ($key != 'year') sort($value_keys);
		foreach ($value_keys as $value) {
			$count = $values[$value];
			if ($count > 1 || ($filter && ($key == 'where' || $key == 'genre')) || $key == 'year' || $key == 'ensembles' || $key == 'name' || $key == 'pieces') {
				$display = $value;
			
				if ($value == 'Teodora') {
					$display = 'Teodora Stepan&#269;i&#263;';
				} else if ($value == 'Jiri') {
					$display = 'Ji&#345;&iacute; Kade&#345;&aacute;bek';
				} else if ($value == 'Den Haag Allstars') {
					$display = 'D&euro;N HAAG A&pound;&pound;$TAR$ &euro;NS&euro;MB&pound;&euro;';
				} else if ($value == 'Pepe') {
					$display = 'Pep&eacute; Garcia';
				} else if ($value == 'Thrainn') {
					$display = '&THORN;r&aacute;inn Hj&aacute;lmarsson';
				} else if ($value == 'Jasna') {
					$display = 'Jasna Veli&#269;kovi&#263;';
				} else if ($value == 'Corne') {
					$display = 'Corn&eacute; Roos';
				} else if ($value == 'Andrew') {
					$display = 'Andrew Zamler-Carhart';
				}
			
				echo(hyperlink("index.php?filter=$value", $display)); 
				if ($count > 1) echo("&nbsp;($count)");	
				echo($br);
			}
		}
		echo($br);
	}

	echo('</td></tr></table>');
}

echo('</td><td width="750" valign="top">');
echo('<img src="../images/spacer.gif" width="10" height="31"><br>');
echo('<table border="0" width="100%" cellspacing="0" cellpadding="0">');
echo('<form action="index.php" method="get"><tr><td align="left">');
echo('<span class="section">' . hyperlink('index.php', 'Concerts'));
if ($filter) echo(": " . ucwords($filter)); 
echo('</span><br><br></td>');
echo('<td align="right" valign="middle"><input type="search" name="filter" autosave="kavafoto" results="5" size="25" value="'); echo($filter);
echo('"/></td></tr></form></table>');

function linkify($text, $catid = null) {
	if ($text == 'Teodora') {
		$text = 'Teodora Stepan&#269;i&#263;';
		$catid = '1145850166-2107764653';
	} else if ($text == 'Jiri') {
		$text = 'Ji&#345;&iacute; Kade&#345;&aacute;bek';
		$catid = '611284601-1416102853';
	} else if ($text == 'Den Haag Allstars') {
		$text = 'D&euro;N HAAG A&pound;&pound;$TAR$ &euro;NS&euro;MB&pound;&euro;';
		$catid = '693029811-1094383512';
	} else if ($text == 'Pepe') {
		$text = 'Pep&eacute; Garcia';
		$catid = '176411966-1789919726';
	} else if ($text == 'Thrainn') {
		$text = '&THORN;r&aacute;inn Hj&aacute;lmarsson';
		$catid = '837786759-907469509';
	} else if ($text == 'Jasna') {
		$text = 'Jasna Veli&#269;kovi&#263;';
		$catid = '1554544375-1969972323';
	} else if ($text == 'Corne') {
		$text = 'Corn&eacute; Roos';
		$catid = '362846885-2081687946';
	} else if ($text == 'Andrew') {
		$text = 'Andrew Zamler-Carhart';
		$catid = '238962600-776532036';
	}
	
	if ($catid) {
		return "<a href=\"../category.php?id=$catid\">$text</a>";
	}
	
	$open = strpos($text, '[');
	$close = strpos($text, ']');
	
	if ($open === false) return $text;
	
	$prefix = substr($text, 0, $open);
	$link = substr($text, $open + 1, $close - $open - 1);
	$suffix = substr($text, $close + 1, strlen($text) - $close - 1);

	return "<a href=\"../category.php?search=$link\">$prefix$link$suffix</a>";
}

function arrayify($text, $catid = null) {
	$output = '';
	if (is_array($text)) {
		foreach ($text as $part) {
			$output .= linkify($part, $catid) . "<br>";
		}
	} else {
		if (strpos($text, '[') !== false) {
			$output .= linkify($text, $catid);
		} else if ($catid) {
			$output = "<a href=\"../category.php?id=$catid\">$text</a>";
		} else {
			$output = $text;
		}
	}
	return $output;
}

function info_table_row($label, $stuff) {
	if (!$stuff) return;
	$row = "<tr>\n\t<td align=\"right\" valign=\"top\">$label:&nbsp;&nbsp;</td>\n\t<td>\n\t\t";
	$row .= $stuff; 
	$row .= "\n\t</td>\n</tr>\n"; 
	return $row;
}

$spacer = '<img src="../images/spacer.gif" width="10" height="10">';

echo("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">");

$photos = array();
$tables = array();

function filter_item_term($item, $term) {
	if (is_array($item)) {
		foreach($item as $subitem) {
			if (strpos(strtolower($subitem), $term) !== false) return true;
		}
		
		return false;
	} else {
		return strpos(strtolower($item), $term) !== false;
	}
}

function filter_concert_term($concert, $term) {
	if (filter_item_term($concert['year'], $term)) return true;
	if (filter_item_term($concert['name'], $term)) return true;
	if (filter_item_term($concert['where'], $term)) return true;
	if (filter_item_term($concert['composers'], $term)) return true;
	if (filter_item_term($concert['performers'], $term)) return true;
	if (filter_item_term($concert['ensemble'], $term)) return true;
	if (filter_item_term($concert['pieces'], $term)) return true;
	if (filter_item_term($concert['genre'], $term)) return true;
	
	return false;
}

function filter_concert_terms($concert, $filter) {
	$filter = strtolower($filter);
	
	foreach (explode(' ', $filter) as $term) {
		if (!filter_concert_term($concert, $term)) return false;
	}
	
	return true;
}


$info_table = "</tr></table>";

$phpversion = phpversion();
if ($phpversion[0] == '5') date_default_timezone_set('Europe/Amsterdam');

$coming_soon_count = 0;
foreach ($concerts as $i => $concert) {
	if ($concert['comingsoon']) {
		$date = date('F j', strtotime(sprintf("%d/%d/%d", $concert['month'], $concert['day'], $concert['year'])));
		$name = $concert['name'][0];
		$info_table = info_table_row($date, $name) . $info_table;
		$coming_soon_count++;
	}
}

$info_table = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>$info_table";

$photos = array();
$tables = array();

if (!$filter && $coming_soon_count > 0) {
	$photos[] = img('coming_soon.jpg', 'Coming soon', 320, 213, 'class="shadow"');
	$tables[] = $info_table; 
}

foreach ($concerts as $i => $concert) {
	
	$date = sprintf("%d.%d.%d", $concert['day'], $concert['month'], $concert['year']);
	$date_path = sprintf("%d/%02d/%02d", $concert['year'], $concert['month'], $concert['day']);
	
	$date_catid = $dates[$date_path];
	$date_category = new Category($date_catid);
	$formatted_date = $date_category->display_name();
	$catid = $concert['id'];
	$category = null;
	$small_image = null;
	if ($catid) {
		$category = new Category($catid);
	} else {
		$catid = $date_catid;
		$category = $date_category;
	}
	
	if (!$concert['comingsoon'] && (!$filter || filter_concert_terms($concert, $filter))) {
		$path = $category->random_photo_with_rating(5);
		$photo = new Photo($path);
		$name = $category->display_name();
		$link = "../category.php?id=$catid&photo=$path";

		$scale = 2;
		$small_image = hyperlink($link, img($photo->medium_path(), $name, 
			$photo->small_width() * $scale, $photo->small_height() * $scale, 'class="shadow"'));
		
		$info_table = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>";
		$info_table .= info_table_row('when', "<a href=\"../category.php?id=$date_catid\">$formatted_date</a>");
		$info_table .= info_table_row('name', arrayify($concert['name'], $catid));
		$info_table .= info_table_row('where', arrayify($concert['where']));
		$info_table .= info_table_row('ensemble', arrayify($concert['ensemble']));
		$info_table .= info_table_row('performers', arrayify($concert['performers']));
		$info_table .= info_table_row('composers', arrayify($concert['composers']));
		$info_table .= info_table_row('pieces', arrayify($concert['pieces']));
		$info_table .= info_table_row('genres', arrayify($concert['genre']));
		$photo_count = $category->photo_count;
		$preview = $concert['preview'] ? ' (preview)' : '';
		if ($photo_count) $info_table .= info_table_row('photos', $photo_count . $preview);
		$info_table .= "</tr></table>";
		
		$photos[] = $small_image;
		$tables[] = $info_table;

		if (count($tables) == 2) {
			echo('<tr>');
			foreach($photos as $photo) echo("<td width=\"33%\" align=\"center\" valign=\"bottom\">$photo$br$br</td>");
			echo('</tr>');

			echo('<tr>');
			foreach($tables as $table) echo("<td width=\"33%\" align=\"center\" valign=\"top\">$table$br<img src=\"../images/spacer.gif\" width=\"10\" height=\"7\"><br></td>");
			echo('</tr>');
			
			$photos = array();
			$tables = array();
		}
	} else {
		// echo("Skipping: " . $concert['name'] . $br);
	}
}

if (count($tables)) {
	echo('<tr>');
	foreach($photos as $photo) echo("<td width=\"33%\" align=\"center\" valign=\"bottom\">$photo$br$br</td>");
	echo('</tr>');

	echo('<tr>');
	foreach($tables as $table) echo("<td width=\"33%\" align=\"center\" valign=\"top\">$table$br$br</td>");
	echo('</tr>');
}

echo("</table>");

?>

&copy; 1998-2010 <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Andrew Zamler-Carhart</a>. All rights reserved.<br><br>

</td>
</tr>
</table>

</body>
</html>