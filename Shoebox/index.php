<?php

include('catalog/info.php');

if ($username && $password) {
	if ($_SERVER['PHP_AUTH_USER'] != $username || MD5($_SERVER['PHP_AUTH_PW']) != $password) {
		header('WWW-Authenticate: Basic realm="Shoebox"');
		header('HTTP/1.0 401 Unauthorized');
		echo("Please enter a name and password.");
		exit();
	}
}

include('shared/layout.php');
include('catalog/info.php');
include('shared/category.php');

$docroot = '';

$catid = $_GET['id'];
$name = $_GET['category'];
$search = $_GET['search'];
$page = $_GET['page'];
if (!$page) $page = 1;
$number = $_GET['number'];
$path = $_GET['photo'];
$show_photo = $number > 0 || $path !== null;
if ($show_photo) $index = $number - 1;

$use_frames = true;

if ($name && !$catid) $catid = Category::id_for_name($name);
if ($search && !$catid) $catid = Category::id_for_name($search);
if ($catid) $category = new Category($catid);

if ($category) $title = $title . ' - ' . $category->display_name();
else if ($search) $title = $title . ' - ' . ucwords($search);

$args = path_with_args('', $_GET);
$results = $_GET['number'] == null && $_GET['photo'] == null ? 'results.php' : 'photo.php';

echo("<html>\n");
echo("<head>\n");
echo("\t<title>$title</title>\n");
echo("\t<meta name=\"generator\" content=\"" . $GLOBALS['generator'] . "\" />\n");
echo("\t<meta name=\"author\" content=\"" . $GLOBALS['author'] . "\" />\n");
echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"browser/styles.css\" media=\"screen\" />");
echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"browser/" . theme('colors.css') . "\" media=\"screen\" />\n");
echo("\t<link rel=\"shortcut icon\" href=\"browser/images/favicon.png\" type=\"image/x-png\" />\n");
echo("</head>\n");

// If frames are turned on, this page is just a frameset and everything else is ignored by the browser.

if ($use_frames) {
	if ($browser_rows < 4) $browser_rows = 8;
	$browser_height = 16 * $browser_rows;
	
	echo("<frameset rows=\"40,$browser_height,*\" border=\"0\">");
	echo("\t<frame class=\"toolbar\" src=\"browser/toolbar.php$args\" name=\"linkbar\" scrolling=\"no\" />\n");
	echo("\t<frame class=\"browser\" src=\"browser/browser.php$args\" name=\"browser\" scrolling=\"no\" />\n");
	echo("\t<frame class=\"results\" src=\"browser/$results$args\" name=\"results\" scrolling=\"auto\" />\n");
	echo("</frameset>\n");
	echo("<noframes>\n");
}

// This section makes the site compatible with search engines, web spiders like HyperImage, and older browsers.
// All the content from the site is reproduced here without frames or JavaScript.

echo("<body class=\"index\">\n");

if (!$catid && !$name && !$search) {
	$catid = '0000000001';
	$category = new Category($catid);
	$paths = $category->file_paths();
} else {
	$paths = Photo::photos_for_request();
}

if ($paths) {
	$count = count($paths);
	$results_count = $results_columns * $results_rows;

	if ($count > $results_count) {
		if (!isset($page)) $page = 1;
		$total = ceil($count / $results_count);
	}

	if ($show_photo) {
		if ($path) {
			$index = array_search($path, $paths);
			$number = $index + 1;
			$photo = new Photo($path);
		}

		$show_previous = $number > 1;
		$show_next = $number < $count;
	} else {
		$show_previous = $page > 1;
		$show_next = $page < $total;
	}
}

echo("<p class=\"header\">$title</p>\n");

$comments = $category->comments;
if ($comments) {
	echo("<p>$comments</p>\n");
}

if ($category) {
	echo("<p>\n");
	$ancestors = $category->ancestors();
	foreach ($ancestors as $i => $child) {
		$icon = $child->size_image();
		$name = $child->name();
		$child_id = $child->catid;
	
		echo("\t" . hyperlink("index.php?id=$child_id", $name));
		if ($i < count($ancestors) - 1) echo(" <font size=-1>&#9654;</font> ");
		echo("\n");
	}
	echo("</p>\n");
}

if ($show_photo) {
	function info_table_row($label, $categories) {
		if (!$categories || count($categories) == 0) return '';
		$row = "\t$label:\n\t";
		foreach ($categories as $i => $catid) {
			$category = new Category($catid);
			$row .= hyperlink("index.php?id=$catid", $category->display_name());
			if ($i < count($categories) - 1) $row .= ",\n\t";
		} 
		$row .= "<br>\n"; 
		echo $row;
	}
	
	function names_for_categories($categories) {
		$names = array();

		foreach ($categories as $catid) {
			$category = new Category($catid);
			$names[] = $category->display_name();
		}

		return $names;
	}
	
	$alt_array = array();
	$alt_array = array_merge($alt_array, names_for_categories($photo->where));
	$alt_array = array_merge($alt_array, names_for_categories($photo->who));
	$alt_array = array_merge($alt_array, names_for_categories($photo->what));
	$alt_array = array_merge($alt_array, names_for_categories($photo->etc));
	$alt = implode(', ', $alt_array);

	echo(img($photo->medium_path(), $alt, 640, $photo->medium_height()) . "\n");

	if ($photo->comments) echo("<p>" . $photo->comments . "</p>\n");

	echo("<p>\n");
	info_table_row('when', $photo->when);
	info_table_row('where', $photo->where);
	info_table_row('who', $photo->who);
	info_table_row('what', $photo->what);
	info_table_row('etc', $photo->etc);
	echo("</p>\n");

	if ($show_next || $show_previous) {
		echo("<p>\n");
		if ($show_previous) echo("\t" . hyperlink("index.php?id=$catid&photo=" . $paths[$index - 1], "previous") . "\n");
		if ($show_next && $show_previous) echo(" | ");
		if ($show_next) echo("\t" . hyperlink("index.php?id=$catid&photo=" . $paths[$index + 1], "next") . "\n");
		echo("<p>\n");	
	}
} else if ($paths) {
	if ($category) {
		$children = $category->children();
		if (count($children)) {
			echo("<ul>\n");
			foreach ($category->children() as $child) {
				$icon = $child->size_image();
				$name = $child->name();
				$child_id = $child->catid;

				echo("\t<li>" . hyperlink("index.php?id=$child_id", $name) . "</li>\n");
			}
			echo("</ul>\n");
		}
	}
	
	$results_count = $results_columns * $results_rows;

	$start = ($page - 1) * $results_count;
	if (count($paths) > $results_count) $paths = array_slice($paths, $start, $results_count);

	foreach ($paths as $index => $path) {
		$photo = new Photo($path);

		$number = $start + $index + 1;
		$link = "index.php?id=$catid&photo=" . $photo->path;
		echo("\t" . hyperlink($link, "\n\t" . img($photo->small_path(), $photo->name, 160, $photo->small_height()))) . "\n";
		if ($index % $results_columns == $results_columns - 1) echo("<br>\n");
	}

	if ($show_next || $show_previous) {
		echo("<p>\n");
		if ($show_previous) echo("\t" . hyperlink("index.php?id=$catid&page=" . ($page - 1), "previous") . "\n");
		if ($show_next && $show_previous) echo(" | ");
		if ($show_next) echo("\t" . hyperlink("index.php?id=$catid&page=" . ($page + 1), "next") . "\n");
		echo("<p>\n");	
	}
}

echo("</body>\n");
if ($use_frames) echo("</noframes>\n");
echo("</html>\n");
?>