<?php

include('../Shoebox/catalog/info.php');
include('../Shoebox/catalog/favorites.php');
include('../Shoebox/shared/category.php');
include('../Shoebox/shared/layout.php');

$theme = cookie_monster('theme', 'dark');
if ($theme != 'light') $shuffle = 'dark'; 

$docroot = '../Shoebox/';

include('websites.php');

$site = $_GET['site'];
$website = $websites[$site]; 

$name = $website['name'];
$url = $website['url'];

?>
<html>
<head>
	<title>KavaFoto - <?php echo $name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="Author" content="Andrew Zamler-Carhart">
	<meta name="Company" content="KavaFoto">
	<link rel="shortcut icon" href="../images/favicon.png" type="image/x-png" />
	<!-- <meta http-equiv="refresh" content="30"> -->
	<link rel="stylesheet" type="text/css" href="../<?php echo(theme('styles.css')); ?>" media="screen" />
</head>
<body>
<script src="Shoebox/shared/shortcut.js" type="text/javascript" charset="utf-8"></script>
<script src="Shoebox/shared/reflection.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
<!--

function category(catid) {
	parent.location.href="Shoebox/index.php?id=" + catid;
}

function category_page(catid, page) {
	parent.location.href="Shoebox/index.php?id=" + catid + "&page=" + page;
}

// -->
</script>

<table border="0" width="1100" cellspacing="0" cellpadding="0">
<tr><td width="350" valign="top">
<img src="../images/spacer.gif" width="10" height="10"><br>
<span class="headline"><a href="../index.php">&nbsp;KavaFoto</a></span><br>
</td><td width="750" valign="top">
<img src="../images/spacer.gif" width="10" height="31"><br>

<table border="0" cellspacing="5" cellpadding="5" width="750"><tr>
<?php 	

echo("<a name=\"$key\"></a>");
echo("<span class=\"section\">$name</span><br><br>");
echo("<center><a href=\"$url\"><img class=\"shadow\" src=\"images/$site-large.jpg\" width=\"640\"></a></center><br>");

$catid = $website['catid'];
$category = new Category($catid);

echo("<table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"640\"><tr>");

for ($i = 0; $i < 3; $i++) {
	$path = $category->random_photo_with_rating(5);
	$photo = new Photo($path);

	$category = new Category($catid);
	$path = $category->random_photo_with_rating(5);
	$photo = new Photo($path);
	$name = $category->name;
	$photo_index = array_search($path, $category->file_paths());
	$link = "../category.php?id=$catid&photo=$path";

	if ($i == 0) $align = 'left';
	else if ($i == 1) $align = 'center';
	else $align = 'right';

	$small_image = hyperlink($link, img($photo->medium_path(), $name, $photo->small_width() * 1.2, $photo->small_height() * 1.2, 'class="shadow"'));
	echo "<td align=\"$align\" valign=\"middle\">$small_image</td>";
}

echo("<tr></table></center><br>");

echo($website['bio'] . $br . $br);
$short_url = substr($url, 7);
echo('Visit the website at ' . hyperlink($url, $short_url) . '.' . $br . $br);

?>

&copy; 1998-2010 <a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#97;&#110;&#100;&#114;&#101;&#119;&#64;&#107;&#97;&#118;&#97;&#102;&#111;&#116;&#111;&#46;&#99;&#111;&#109;">Andrew Zamler-Carhart</a>. All rights reserved.<br><br>

</td>
</tr>
</table>

</body>
</html>
