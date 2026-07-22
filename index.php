<?php

include('Shoebox/catalog/info.php');
include('Shoebox/catalog/favorites.php');
include('Shoebox/shared/category.php');
include('Shoebox/shared/layout.php');

$theme = cookie_monster('theme', 'dark');
if ($theme != 'light') $shuffle = 'dark'; 

$docroot = 'Shoebox/';

$catid = $_GET['id'];

shuffle($home_page);

if ($catid) {
	$index = array_search($catid, $home_page);
	if ($index !== false) {
		unset($home_page[$index]);
		$home_page = array_values($home_page);
	}
} else {
	$catid = $home_page[0];
}

$category = new Category($catid);
$path = $_GET['photo'];
$link = $_GET['link'];
if (!$path) $path = $link ? $category->random_photo() : $category->favorite_photo();
if ($path == $link) $path = $category->random_photo();
$photo = new Photo($path);

$page = $_GET['page'];
$page++;

$next_page = $page + 1;
$next_link = "index.php?id=$catid&link=$path";
$photo_index = array_search($photo->path, $category->file_paths());
$results_count = $results_columns * $results_rows;
$browse_page = ceil(($photo_index + 1) / $results_count);
$browse_link = "Shoebox/index.php?id=$catid";
if ($browse_page > 1) $browse_link .= "&page=$browse_page";
$shuffle_link = "index.php?page=$next_page";


?>
<html>
<head>
	<title>KavaFoto</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="Author" content="Andrew Zamler-Carhart">
	<meta name="Company" content="KavaFoto">
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-png" />
	<!-- <meta http-equiv="refresh" content="30"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo(theme('styles.css')); ?>" media="screen" />
</head>
<body>
<table border="0" width="1100" cellspacing="0" cellpadding="0">
<tr><td valign="top" width="350">
<img src="images/spacer.gif" width="10" height="10"><br>
<span class="headline">&nbsp;KavaFoto</span><br>
<img src="images/spacer.gif" width="350" height="1075">
<!--	<span class="sidebar"><a href="#Concerts">Concerts</a><br>
	<a href="#Fashion">Fashion</a><br>
	<a href="#Artistic">Artistic</a><br>
	<a href="#Collection">Collection</a><br>
	<a href="#Websites">Websites</a><br>
	<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Contact</a><br>
</span> -->

</td><td width="750" valign="top">
<img src="images/spacer.gif" width="10" height="31"><br>

<?php 	

function section($section, $catids, $cols) {
	global $br;
	if ($section) echo("<a name=\"$section\"></a>");
	if ($section == 'Concerts') $section = hyperlink('concerts/', 'Concerts');
	if ($section) echo("<span class=\"section\">$section</span>$br");
	$width = 750;
	if ($cols == 3) $width = 684;
	echo("<center><table border=\"0d\" cellspacing=\"5\" cellpadding=\"5\" width=\"$width\"><tr>");
	$i = 0;
	foreach ($catids as $i => $catid) {
		$category = new Category($catid);
		$path = $category->random_photo_with_rating(5);
		$photo = new Photo($path);

		$name = $category->display_name();
		if ($name == 'Kick the Can posters') $name = 'Posters';
		if ($name == 'Kick the Can concert') $name = 'Concert';

		$link = "category.php?id=$catid&photo=$path";

		$scale = $cols == 2 ? 1.5 : 1;
		if ($cols == 1) {
			$width = $photo->medium_width();
			$height = $photo->medium_height();
		} else if ($cols == 2) {
			$width = $photo->small_width() * 1.5;
			$height = $photo->small_height() * 1.5;
		} else if ($cols == 3) {
			$width = $photo->small_width();
			$height = $photo->small_height();
		}
		
		$small_image = img($photo->medium_path(), $name, $width, $height, 'class="shadow"');
		if ($cols > 1) $small_image .= $br . spacer_img(10, 10) . "$br$name";
		$small_image = hyperlink($link, $small_image);
		echo "<td align=\"center\" valign=\"middle\" width=\"375\">$small_image</td>";

		if ($i % $cols == $cols - 1 && $i < count($catids) - 1) echo("</tr><tr>");
	}
	echo("</tr></table></center>");	
}

// echo("<span class=\"section\">Latest</span><br>");

$concerts = array(
	'1658869099-295857131', // margriet
	'1348809296-312689907', // benjamin rhodes
	'595347466-1178765257', // amsterdam composers' festival
	// '1680845070-2095023258', // Yedo & Jeremiah's exam
	// '557327799-943197343', // new york the hague 2010
	// '1084367523-2144873778', // nicole kidman
	'1841320009-1416989116', // portrait disorientation
	// '693029811-1094383512', // allstars
);

$kickthecan = array(
	'215370494-221690909', // Posters
	'514084999-762964394', // Kicking
	'1771701915-222471455', // Concert
);

$fashion = array(
	'240833070-943625323', // Anna
	'490242361-1522585276', // Arianne
	'1704398340-1772829567', // Arianne Collection
	'2123343009-264534740', // Alexandrina
);

$artistic = array(
	'364499225-1374627123', // red
	'995400704-1915918360', // paint trail
	'1499366148-194332165', // memorial sports centre
	'178870077-158339569', // sculpture festival
	'1143661188-207070533', // new mexico
	'2020110464-465069183', // portfolio
);

shuffle($fashion);
shuffle($artistic);

echo('<table border="0" width="100%" cellspacing="0" cellpadding="0">');
echo('<form action="category.php" method="get"><tr><td align="right" valign="middle">');
echo("<input type=\"search\" name=\"search\" autosave=\"kavafoto\" results=\"5\" size=\"25\" value=\"$filter\"/>");
echo('</td></tr></form></table>');
echo($br);

$home = array('1872109496-2002930091');

include('Shoebox/catalog/categories.php');
shuffle($catids);
$catids = array_slice($catids, 0, 9);

section(null, $home, 1);
echo($br);
section('Concerts', $concerts, 2);

include('concerts/concerts.php');
$concert_count = count($concerts);
echo('<center>' . hyperlink('concerts/', "See all $concert_count concerts&hellip;") . '</center>');
section('Kick the Can', $kickthecan, 3);
echo($br);
section('Style', $fashion, 2);
echo($br);
section('Artistic', $artistic, 3);
echo($br);
section('Collection', $catids, 3);
echo($br);

echo("<a name=\"Websites\"></a>");
echo("<span class=\"section\">Websites</span><br>");

include('websites/websites.php');
echo('<table border="0" cellspacing="5" cellpadding="5" width="750"><tr>');

$i = 0;
foreach ($websites as $key => $website) {
	$name = $website['name'];
	echo("<td width=\"375\" align=\"center\"><a href=\"websites/index.php?site=$key\"><img class=\"shadow\" src=\"websites/images/$key-large.jpg\" width=\"320\"><br>" . spacer_img(10, 10) . "$br$name</a></td>");
	if ($i % 2 == 1) echo("</tr><tr>");
	$i++;
}
echo("</tr></table>");
?>

<span class="section"><a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Contact</a></span><br>



<?php
/*
echo('<tr>');

echo('<td height="65" align="left" valign="middle"><span class="header">KavaFoto</span>');
echo('&nbsp;&nbsp;&nbsp;');
echo('</td><td width="170" align="right" valign="middle">');

echo(hyperlink($next_link, rollover_img('images/next2.png', 'images/next.png', 'nextArrow', 'Next', 40, 40), null, 'Next photo'));
echo('&nbsp;&nbsp;');
echo(hyperlink($shuffle_link, rollover_img('images/shuffle2.png', 'images/shuffle.png', 'shuffle', 'Shuffle', 40, 40), null, 'Random photo'));
echo('&nbsp;&nbsp;');
echo(hyperlink($browse_link, rollover_img('images/index2.png', 'images/index.png', 'browser', 'Browser', 40, 40), null, 'Show all photos'));
// echo('<br><br>');
// echo(hyperlink("javascript:self.moveTo(0,0);self.resizeTo(screen.availWidth,screen.availHeight);", rollover_img('images/fullscreen.png', 'images/fullscreen2.png', 'fullscreen', 'Fullscreen', 40, 40), null, 'Enlarge window'));

echo('</td>');
echo('</tr>');

echo('<tr>');
echo('<td width="1100" height="731" background="images/background.jpg" colspan="2" align="right" valign="middle"><table border="0" width="750" height="700" cellspacing="0" cellpadding="0">');
$first = rand(0, 1);


function medium_cell($category, $photo) {
	global $br;
	global $results_count;
	$name = $category->name;
	$catid = $category->catid;
	$path = $photo->path;
	$link = "index.php?id=$catid&link=$path";
	// $link = $page > 1 ? "javascript:category_page('$catid',$page)" : "javascript:category('$catid')";
	$hovertext = "Another photo in this category"; // $category->path();
	$medium_image = hyperlink($link, img($photo->medium_path(), $name, $photo->medium_width(), $photo->medium_height(), 'class="shadow"') . 
		$br . spacer_img(10, 10) . $br . $name, null, $hovertext);
	$size = $photo->aspect_ratio > 1 ? 'colspan="3"' : 'rowspan="3"';
	return "<td $size align=\"center\" valign=\"middle\">$medium_image</td>";
}

function small_cell($catid, $portrait) {
	global $br;
	global $results_count;
	$category = new Category($catid);
	$path = $category->favorite_photo();
	$photo = new Photo($path);
	$name = $category->name;
	$photo_index = array_search($path, $category->file_paths());
	$page = ceil(($photo_index + 1) / $results_count);
	$link = "index.php?id=$catid&photo=$path";
	// $link = $page > 1 ? "javascript:category_page('$catid',$page)" : "javascript:category('$catid')";
	$hovertext = 'Enlarge this photo'; // $category->path(); 
	$small_image = hyperlink($link, img($photo->small_path(), $name, $photo->small_width(), $photo->small_height(), 'class="shadow"') . 
		$br . spacer_img(10, 10) . $br . $name, null, $hovertext);
	$size = $portrait ? 'height="210"' : 'width="240"';
	return "<td $size align=\"center\" valign=\"middle\">$small_image</td>";
}

if ($photo->aspect_ratio > 1) { // landscape
	$big_row = "<tr>" . medium_cell($category, $photo) . "</tr>";
	if ($first) echo($big_row);
	echo("<tr>");
	echo(small_cell($home_page[1], false));
	echo(small_cell($home_page[2], false));
	echo(small_cell($home_page[3], false));
	echo("</tr>");
	if (!$first) echo($big_row);
} else { // portrait
	$big_col = medium_cell($category, $photo);
	echo("<tr>");
	if ($first) echo($big_col);
	echo(small_cell($home_page[1], true));
	if (!$first) echo($big_col);
	echo("</tr>");
	echo("<tr>" . small_cell($home_page[2], true) . "</tr>");
	echo("<tr>" . small_cell($home_page[3], true) . "</tr>");
}

echo('</table></td>');
*/

?>

<br>&copy; 1998-2010 <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Andrew Zamler-Carhart</a>. All rights reserved.<br><br>

</td>
</tr>
</table>

</body>
</html>
