<?php

include('Shoebox/catalog/info.php');
include('Shoebox/catalog/favorites.php');
include('Shoebox/shared/category.php');
include('Shoebox/shared/layout.php');

$shuffle = cookie_monster('shuffle', 'off') == 'on';
$theme = cookie_monster('theme', 'dark');
$best = cookie_monster('best', 'off') == 'on';

$docroot = 'Shoebox/';

$catid = $_GET['id'];
if (!$catid) {
	$search = $_GET['search'];
	if ($search) $catid = Category::id_for_name($search);
}

if ($catid) $category = new Category($catid);
$file_paths = Photo::photos_for_request();

if ($best) {
	include('Shoebox/catalog/toprated.php');
	$top_paths = array_intersect($file_paths, $toprated);
	if (count($top_paths) > 3) {
		// echo(sprintf("filtered %d to %d", count($file_paths), count($top_paths)));
		$file_paths = array_values($top_paths);
	} else {
		// echo(sprintf("didn't filter %d to %d", count($file_paths), count($top_paths)));
	}
}

$file_count = count($file_paths);

function random_element($array) {
    if (count($array) === 0) return null;
    $rand = mt_rand(0, count($array) - 1);
    $array_keys = array_keys($array);
    return $array[$array_keys[$rand]];
}

$path = $_GET['photo'];
if ($shuffle) {
	$link = $_GET['link'];
	if ($category) {
		if (!$path || array_search($path, $file_paths) === false) 
			$path = $best ? $category->random_photo_with_rating(5) : $category->random_photo();
		if ($path == $link) 
			$path = $category->random_photo();
	} else {
		if (!$path || array_search($path, $file_paths) === false) 
			$path = random_element($file_paths);
	}
} else {
	if (!$path || array_search($path, $file_paths) === false) 
		$path = $file_paths[0];
}
$photo = new Photo($path);
$args = $search ? "search=$search" : "id=$catid";

$page = $_GET['page'];
$page++;

$next_page = $page + 1;
$next_link = "category.php?$args&link=$path";
$photo_index = array_search($photo->path, $file_paths);
$results_count = $results_columns * $results_rows;
$browse_page = ceil(($photo_index + 1) / $results_count);
$browse_link = "Shoebox/index.php?$args";
if ($browse_page > 1) $browse_link .= "&page=$browse_page";
$shuffle_link = "category.php?page=$next_page";
$name = $category ? $category->display_name() : ucwords($search);

if ($shuffle) {
	$random_paths = $file_paths; // array_splice($file_paths, $photo_index, 1);
	unset($random_paths[$photo_index]);
	shuffle($random_paths);
	if (count($random_paths) > 0) $small3 = $random_paths[0];
	if (count($random_paths) > 1) $small1 = $random_paths[1];
	if (count($random_paths) > 2) $small2 = $random_paths[2];
} else {
	$prev_index = $photo_index - 1;
	if ($prev_index < 0) $prev_index += $file_count;
	if ($file_count > 3) $small1 = $file_paths[$prev_index];
	if ($file_count > 1) $small2 = $file_paths[($photo_index + 1) % $file_count];
	if ($file_count > 2) $small3 = $file_paths[($photo_index + 2) % $file_count];
}

?>
<html>
<head>
	<title>KavaFoto - <?php echo($name); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="Author" content="Andrew Zamler-Carhart">
	<meta name="Company" content="KavaFoto">
	<link rel="shortcut icon" href="images/favicon.png" type="image/x-png" />
	<!-- <meta http-equiv="refresh" content="30"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo(theme('styles.css')); ?>" media="screen" />
</head>
<body>
<script src="Shoebox/shared/shortcut.js" type="text/javascript" charset="utf-8"></script>
<table border="0" width="1100" cellspacing="0" cellpadding="0">
<tr><td width="350" valign="top">
<img src="images/spacer.gif" width="10" height="10"><br>
<span class="headline"><a href="index.php">&nbsp;KavaFoto</a></span><br>
</td><td width="750" valign="top">

<?php 

echo('<img src="images/spacer.gif" width="10" height="31"><br>');
echo('<table border="0" width="100%" cellspacing="0" cellpadding="0">');
echo('<form action="category.php" method="get">');
echo('<td valign="top">');
echo("<span class=\"section\">$name</span><br><br>");
echo('<td align="right" valign="middle">');

echo("<input type=\"search\" name=\"search\" autosave=\"kavafoto\" results=\"5\" size=\"25\" value=\"$filter\"/>");
echo('</form>');
echo('</td></tr></table>');

echo('<table border="0" width="729" cellspacing="0" cellpadding="0">');
$first = rand(0, 1);
$landscape = $photo->aspect_ratio > 1;
$used = array($path);

function medium_cell($category, $photo) {
	global $br;
	global $results_count;
	global $args;
	global $shuffle;
	global $small2;
	$name = $category->name;
	$catid = $category->catid;
	$path = $photo->path;
	$link = $shuffle ? "category.php?$args&link=$path" : "category.php?$args&photo=$small2";
	$random = rand(1, 4);
	$orientation = $photo->aspect_ratio > 1 ? 'h' : 'v';
	$background = "tape/large$random$orientation.png";

	$medium_image = hyperlink($link, img($photo->medium_path(), $name, $photo->medium_width(), $photo->medium_height(), 'class="shadow"'));
	$size = $photo->aspect_ratio > 1 ? 'colspan="3"' : 'rowspan="3"';
	return "<td $size align=\"center\" valign=\"middle\" background=\"$background\">$medium_image$br$br</td>";
}

function small_cell($category, $path = null) {
	if (!$path) return;
	
	global $br;
	global $file_paths;
	global $results_count;
	global $args;
	global $used;
	global $landscape;
	$category;
	$catid = $category->catid;
	
	$photo = new Photo($path);
	$name = $category->name;
	$photo_index = array_search($path, $file_paths);
	$page = ceil(($photo_index + 1) / $results_count);
	$link = "category.php?$args&photo=$path";
	$random = rand(1, 4);
	$orientation = $photo->aspect_ratio > 1 ? 'h' : 'v';
	$background = "tape/small$random$orientation.png";
	$cell_height = $landscape ? '' : ' height="213"';
	$small_image = hyperlink($link, img($photo->small_path(), $name, $photo->small_width(), $photo->small_height(), 'class="shadow"'));
	return "<td width=\"243\"$cell_height align=\"center\" valign=\"middle\" background=\"$background\">$small_image$br$br</td>";
}	

if ($landscape) {
	$big_row = "<tr>" . medium_cell($category, $photo) . "</tr>";
	if ($first) echo($big_row);
	echo("<tr>");
	echo(small_cell($category, $small1));
	echo(small_cell($category, $small2));
	echo(small_cell($category, $small3));
	echo("</tr>");
	if (!$first) echo($big_row);
} else { // portrait
	$big_col = medium_cell($category, $photo);
	echo("<tr>");
	if ($first) echo($big_col);
	echo(small_cell($category, $small1));
	if (!$first) echo($big_col);
	echo("</tr>");
	echo("<tr>" . small_cell($category, $small2) . "</tr>");
	echo("<tr>" . small_cell($category, $small3) . "</tr>");
}

echo('</table>');

function info_table_row($label, $categories) {
	global $photo;
	global $results_columns;
	global $results_rows;
	global $catid;
	$results_count = $results_columns * $results_rows;
	if ($label) $label .= ':';
	
	if (!$categories || (is_array($categories) && count($categories) == 0)) return '';
	$row = "<tr>\n\t<td align=\"right\" valign=\"top\">$label&nbsp;&nbsp;</td>\n\t<td>\n\t\t";

	if (is_array($categories)) {
		$count = 0;
		foreach ($categories as $i => $_catid) {
			if ($catid == $_catid) continue;
			$category = new Category($_catid);
		
			$link = "category.php?id=$_catid";
			$row .= hyperlink($link, $category->display_name(), NULL, $category->path());
			$url = $category->url;
	
			if ($url) {
				$link_type = $category->link_type();
				$row .= ' ' . hyperlink($url, img("Shoebox/browser/images/links/$link_type.png", 
					$link_type, 14, 14), 'target="external"');
			}
			/*if ($i < count($categories) - 1) */
				$row .= '<br/>';
			$count++;
		} 
	} else {
		$row .= $categories;
	}
	
	if (is_array($categories) && !$count) return;
	$row .= "\n\t</td>\n</tr>\n"; 
	return $row;
}

$photo_index++;

echo("<center>");
echo('<table border="0" cellspacing="0" cellpadding="0"><tr>');

$info_table = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
if ($photo_index) $info_table .= info_table_row('photo', "$photo_index of $file_count");
$info_table .= info_table_row('when', $photo->when);
$info_table .= info_table_row('where', $photo->where);
$info_table .= info_table_row('who', $photo->who);
$info_table .= info_table_row('what', $photo->what);
$info_table .= info_table_row('etc', $photo->etc);

$download_image = $photo->has_large_version() ? $photo->large_path() : $photo->medium_path();
if ($photo->path) $info_table .= info_table_row('file', hyperlink($download_image, $photo->short_path())); 

$children = $category->children_catids;
if ($children) $children = array_diff($children, $photo->when(), $photo->where(), $photo->who(), $photo->what(), $photo->etc());

if (count($children) || $category->url) {
	$info_table .= '</table>';
	echo("<td align=\"center\" valign=\"top\">$info_table</td>");
	echo("<td width=\"30\">&nbsp;</td>");
	$info_table = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
}
 	
if ($category->url) {
	$link_type = $category->link_type();
	$link = $link_type;
	if ($link == 'website') $link = $category->url;
	if (strpos($link, 'http://') === 0) $link = substr($link, 7);
	if (strpos($link, 'www.') === 0) $link = substr($link, 4);
	if ($link[strlen($link) - 1] == '/') $link = substr($link, 0, strlen($link) - 1);
	$info_table .= info_table_row('link', hyperlink($category->url, $link . ' ' . 
		img("Shoebox/browser/images/links/$link_type.png", $link_type, 14, 14), 'target="external"'));
}

if ($category->parent_catid) $info_table .= info_table_row('parent', array($category->parent_catid));

$child_count = count($children);
$max_children = 5;
$show_all = $_GET['showall'];
if ($child_count > $max_children && !$show_all) $children = array_slice($children, 0, $max_children - 1);
$info_table .= info_table_row('&nbsp;&nbsp;&nbsp;children', $children);
if ($child_count > $max_children && !$show_all)
	$info_table .= info_table_row('', hyperlink("category.php?$args&photo=$path&showall=yes", 'more&hellip'));

$info_table .= '</table>';

echo("<td align=\"center\" valign=\"top\">$info_table</td>");

echo('</tr></table>');
echo($br);

// echo('<br><img src="images/spacer.gif" width="10" height="8"><br>');

echo('<table border="0" cellspacing="0" cellpadding="2">');

echo('<tr><td align="right">loop</td><td>');
if ($shuffle) 
	echo(hyperlink("category.php?$args&photo=$path&shuffle=off", 
		img(theme('shuffle_on.png'), 'shuffle on', 113, 28), '', 'Show photos in chronological order'));
else
 	echo(hyperlink("category.php?$args&photo=$path&shuffle=on", 
		img(theme('shuffle_off.png'), 'shuffle off', 113, 28), '', 'Show photos in random order'));
echo('</td><td>shuffle</td></tr>');

echo('<tr><td align="right">all photos</td><td>');
if ($best) 
	echo(hyperlink("category.php?$args&photo=$path&best=off", 
		img(theme('best_on.png'), 'best on', 113, 28), '', 'Show all photos'));
else
 	echo(hyperlink("category.php?$args&photo=$path&best=on", 
		img(theme('best_off.png'), 'best off', 113, 28), '', 'Show only top-rated photos'));
echo('</td><td>top-rated</td></tr>');

echo('<tr><td align="right">white</td><td>');
if ($theme == 'dark') 
	echo(hyperlink("category.php?$args&photo=$path&theme=light", 
		img(theme('dark.png'), 'dark', 113, 28), '', 'Make the background white'));
else
 	echo(hyperlink("category.php?$args&photo=$path&theme=dark", 
		img(theme('light.png'), 'light', 113, 28), '', 'Make the background black'));
echo('</td><td>black</td></tr>');

echo('</table><br>');

?>

&copy; 1998-2010 <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Andrew Zamler-Carhart</a>. All rights reserved.<br><br>
</center>
</td>
</tr>
</table>

</body>
</html>
