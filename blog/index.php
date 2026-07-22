<?php

include('../Shoebox/catalog/info.php');
include('../Shoebox/catalog/favorites.php');
include('../Shoebox/catalog/dates.php');
include('../Shoebox/shared/category.php');
include('../Shoebox/shared/layout.php');

$shuffle = cookie_monster('shuffle', 'on') == 'on';
$theme = cookie_monster('theme', 'dark');
$best = cookie_monster('best', 'off') == 'on';

$docroot = '../Shoebox/';

function random_element($array) {
    if (count($array) === 0) return null;
    $rand = mt_rand(0, count($array) - 1);
    $array_keys = array_keys($array);
    return $array[$array_keys[$rand]];
}


?>
<html>
<head>
	<title>KavaFoto - Blog</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="Author" content="Andrew Zamler-Carhart">
	<meta name="Company" content="KavaFoto">
	<link rel="shortcut icon" href="../images/favicon.png" type="image/x-png" />
	<!-- <meta http-equiv="refresh" content="30"> -->
	<link rel="stylesheet" type="text/css" href="../<?php echo(theme('styles.css')); ?>" media="screen" />
</head>
<body>
<table border="0" width="1100" cellspacing="0" cellpadding="0">
<tr><td width="350" valign="top">
<img src="../images/spacer.gif" width="10" height="10"><br>
<span class="headline"><a href="index.php">&nbsp;KavaFoto</a></span><br>
</td><td width="750" valign="top">

<?php 

echo('<img src="../images/spacer.gif" width="10" height="31"><br>');
echo('<table border="0" width="100%" cellspacing="0" cellpadding="0">');
echo('<form action="../category.php" method="get">');
echo('<td valign="top">');
echo("<span class=\"section\">Blog</span><br><br>");
echo('<td align="right" valign="middle">');

echo("<input type=\"search\" name=\"search\" autosave=\"kavafoto\" results=\"5\" size=\"25\" value=\"$filter\"/>");
echo('</form>');
echo('</td></tr></table>');

function is_day($day) {
	return $day[0] == '2' && strlen($day) == 10;
}

$date_keys = array_keys($dates);
$date_keys = array_reverse($date_keys);
$date_keys = array_filter($date_keys, "is_day");
$date_keys = array_values($date_keys);

$days_per_page = 5;
$page = $_GET['page'];
if (!$page) $page = 1;

$start = ($page - 1) * $days_per_page;
$end = $page * $days_per_page;

function add_to_totals(&$totals, $photo_info, $key) {
	$catids = $photo_info[$key];
	if ($catids) {
		if (is_array($catids)) {
			foreach ($catids as $catid) {
				$totals[$catid]++;
			}
		} else {
			$totals[$catids]++;
		}
	}
}

function display_total($key, $totals, $max) {
	global $br;
	arsort($totals);

	$categories = array();
	echo($last_key);
	foreach($totals as $catid => $count) {
		if ($count > 1) {
			$category = new Category($catid);
			$display = hyperlink("../category.php?id=$catid", $category->display_name());
			// if ($count > 1 && $count < $max) $display .=' (' . $count . ')';
			$categories[] = $display;
		}
	}

	if (count($categories)) echo($key . ": " . implode($categories, ', ') . $br);
}

for ($i = $start; $i < $end; $i++) {
	$date_key = $date_keys[$i];
	$catid = $dates[$date_key];
	$category = new Category($catid);
	echo(hyperlink("../category.php?id=$catid", $category->display_name()) . $br);
	echo($category->photo_count . " photos$br");
	
	$day_path = "../Shoebox/photos/$date_key/index.php";
	if (file_exists($day_path)) {
		include($day_path);

		$wheres = array();
		$whos = array();
		$whats = array();
		$etcs = array();

		foreach($info as $photo_info) {
			add_to_totals($wheres, $photo_info, 'where');
			add_to_totals($whos, $photo_info, 'who');
			add_to_totals($whats, $photo_info, 'what');
			add_to_totals($etcs, $photo_info, 'etc');
		}
		
		display_total('where', $wheres, $category->photo_count);
		display_total('who', $whos, $category->photo_count);
		display_total('what', $whats, $category->photo_count);
		display_total('etc', $etcs, $category->photo_count);
	}
	
	echo($br);
}

?>

&copy; 1998-2010 <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Andrew Zamler-Carhart</a>. All rights reserved.<br><br>
</center>
</td>
</tr>
</table>

</body>
</html>
