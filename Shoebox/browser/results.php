<?php

include('../catalog/info.php');
include('../shared/category.php');
include('../shared/layout.php');

if ($username && $password) {
	if ($_SERVER['PHP_AUTH_USER'] != $username || MD5($_SERVER['PHP_AUTH_PW']) != $password) {
		header('WWW-Authenticate: Basic realm="Shoebox"');
		header('HTTP/1.0 401 Unauthorized');
		echo("Please enter a name and password.");
		exit();
	}
}

$catid = $_GET['id'];
$name = $_GET['category'];
$search = $_GET['search'];
$page = $_GET['page'];
if (!$page) $page = 1;

$homepage = !$catid && !$name && !$search;

if ($name && !$catid) $catid = Category::id_for_name($name);
if ($search && !$catid) $catid = Category::id_for_name($search);
if ($catid) {
	$category = new Category($catid);
	$parent = $category->parent();
	$parent_catid = $parent->catid;
}

$paths = Photo::photos_for_request();

if ($paths) {
	$count = count($paths);
	$results_count = $results_columns * $results_rows;
	
	if ($count > $results_count) {
		if (!isset($page)) $page = 1;
		$total = ceil($count / $results_count);
	}
	
	$show_previous = $page > 1;
	$show_next = $page < $total;
	$page_offset = ($page - 1) * $results_count;
}

head('Shoebox', 'results', 'updateToolbar();');
echo('<script src="../shared/shortcut.js" type="text/javascript" charset="utf-8"></script>');
echo('<script src="../shared/reflection.js" type="text/javascript" charset="utf-8"></script>');
echo("\n<script type=\"text/javascript\">\n<!--\n\n");

if ($homepage) {
	echo("shortcut.add(\"Left\", function() { parent.results.location='results.php'; });\n");
	echo("shortcut.add(\"Right\", function() { parent.results.location='results.php'; });\n");
	echo("shortcut.add(\"Space\", function() { parent.results.location='results.php'; });\n");
} else {
	echo("shortcut.add(\"Left\", function() { previous(); });\n");
	echo("shortcut.add(\"Right\", function() { next(); });\n");
	echo("shortcut.add(\"Space\", function() { next(); });\n");
	echo("shortcut.add(\"Up\", function() { category('$parent_catid'); });\n");
	echo("shortcut.add(\"Escape\", function() { parent.location.href=\"../index.php\"; });\n");

	echo("\n");
	echo("shortcut.add(\"Return\", function() { photo(" . ($page_offset + 1) . "); });\n");
	echo("shortcut.add(\"1\", function() { photo(" . ($page_offset + 1) . "); });\n");
	echo("shortcut.add(\"2\", function() { photo(" . ($page_offset + 2) . "); });\n");
	echo("shortcut.add(\"3\", function() { photo(" . ($page_offset + 3) . "); });\n");
	echo("shortcut.add(\"4\", function() { photo(" . ($page_offset + 4) . "); });\n");
	echo("shortcut.add(\"5\", function() { photo(" . ($page_offset + 5) . "); });\n");
	echo("shortcut.add(\"6\", function() { photo(" . ($page_offset + 6) . "); });\n");
	echo("shortcut.add(\"7\", function() { photo(" . ($page_offset + 7) . "); });\n");
	echo("shortcut.add(\"8\", function() { photo(" . ($page_offset + 8) . "); });\n");
	echo("shortcut.add(\"9\", function() { photo(" . ($page_offset + 9) . "); });\n");
}
echo("\n");
echo("function updateToolbar() {\n");
	echo("\tself.focus();\n\n");

	if ($count == 1) {
		echo("\tphoto(1);\n");
	} else {
		if ($category) {
			$display_name = $category->display_name();
			if (strpos($display_name, '&') === false)
				$title = $title . ' - ' . $display_name;
		}
		else if ($search) $title = $title . ' - ' . ucwords($search);
		echo("\tparent.document.title=\"$title\";\n");
		if ($_GET['update'] == 'toolbar') {
			$encoded_search = str_replace(' ', '+', $search);
			echo("\tparent.linkbar.location.href=\"toolbar.php?search=$encoded_search\";\n");
			if ($catid)	
				echo("\tparent.browser.location.href=\"browser.php?id=$catid\";\n");
		}
	}
echo("}\n\n");

echo("function photo(index) {\n");
	if ($catid) $arg = "id=$catid";
	else if ($search) $arg = "search=$search";
	echo("\tparent.results.location.href=\"photo.php?$arg&number=\" + index;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?$arg&number=\" + index;\n");
echo("}\n\n");

echo("function category(catid) {\n");
	echo("\tparent.results.location.href=\"results.php?id=\" + catid;\n");
	echo("\tparent.browser.location.href=\"browser.php?id=\" + catid;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?id=\" + catid;\n");
echo("}\n\n");

echo("function category_page(catid, page) {\n");
	echo("\tparent.results.location.href=\"results.php?id=\" + catid + \"&page=\" + page;\n");
	echo("\tparent.browser.location.href=\"browser.php?id=\" + catid;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?id=\" + catid + \"&page=\" + page;\n");
echo("}\n\n");

echo("function search(term) {\n");
	echo("\tparent.results.location.href=\"results.php?search=\" + term;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?search=\" + term;\n");
echo("}\n\n");

if ($catid) {
	$arg = "id=$catid";
} else if ($search) {
	$arg = "search=" . str_replace(' ', '+', $search);
}

if ($show_previous) {
	echo("function previous() {\n");
	$previous = $page - 1;
	echo("\tparent.results.location.href=\"results.php?$arg&page=$previous\";\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?$arg&page=$previous\";\n");
	echo("}\n\n");
}

if ($show_next) {
	echo("function next() {\n");
	$next = $page + 1;
	echo("\tparent.results.location.href=\"results.php?$arg&page=$next\";\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?$arg&page=$next\";\n");
	echo("}\n\n");
}

$copyright = copyright($copyright, $author, $email, $title, bookmark($category, $search, $page));

echo("// -->\n</script>\n");

echo("<table class=\"results\" width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"10\" cellpadding=\"0\"><tr>\n");

echo("<td width=\"40\" valign=\"middle\" align=\"left\">");
$previous_link = $homepage ? 'results.php' : 'javascript:previous();';
if ($homepage || $show_previous) echo(hyperlink($previous_link, rollover_img(theme('previous.png'), theme('previous2.png'), 'previousArrow', 'Previous', 40, 400), NULL, "Go to the previous page (&larr; key)"));
else echo(spacer_img(40, 400));
echo("</td>");

echo('<td align="center" valign="middle" height="100%">');
echo('<table width="95%" height="90%" border="0" cellspacing="10" cellpadding="0">');

$results_count = $results_columns * $results_rows;

if ($paths) {
	$start = ($page - 1) * $results_count;
	if (count($paths) > $results_count) $paths = array_slice($paths, $start, $results_count);

	foreach ($paths as $index => $path) {
		$photo = new Photo($path);
		
		if ($index % $results_columns == 0) echo("<tr>\n");
		$number = $start + $index + 1;
		$link = "javascript:photo($number)";
		echo(td('results', hyperlink($link, img($photo->small_path(), $photo->name, $photo->small_width(), $photo->small_height(), 'class="shadow"'), NULL, 'Go to the photo ' . strtoupper($photo->name)), 
			null, null, null, null, 'align="center"'));
		if ($index % $results_columns == $results_columns - 1) echo("</tr>\n");
	}
	
	$comments = $category->comments;
	if ($comments) {
		echo("<tr><td colspan=\"$results_columns\" align=\"center\">$comments</td></tr>");
	}
}

if ($homepage) {
	include('../catalog/favorites.php');
	include('../catalog/searches.php');

	// add favorites and searches
	$needed = $results_count - count($home_page) - $random_categories;
	if ($needed > 0) {
		$favorites = array_merge($favorites, $searches);
		$favorites = array_diff($favorites, $home_page);
		shuffle($favorites);
		if (count($favorites) > $needed) $favorites = array_slice($favorites, 0, $needed);
		$home_page = array_merge($home_page, $favorites);
		
	}
	
	// add random categories
	if ($random_categories > 0) {
		include('../catalog/categories.php');
		shuffle($catids);
		if (count($catids) > $needed) $catids = array_slice($catids, 0, $random_categories);
		$home_page = array_merge($home_page, $catids);
	}
	
	shuffle($home_page);
	
	foreach($home_page as $index => $favorite) {
		if (is_array($favorite)) {
			$files = $favorite['files'];
			$photo_path = $files[array_rand($files)];
			$photo = new Photo($photo_path);
			$name = $favorite['name'];
			$hovertext = "Search for &ldquo;$term&rdquo;";
			$term = strtolower($name);
			$term = urlencode($term);
			$link = "javascript:search('$term')";
		} else {
			$category = new Category($favorite);
			$photo_path = $category->favorite_photo();
			$photo = new Photo($photo_path);
			$name = $category->display_name();
			$photo_index = array_search($photo_path, $category->file_paths());
			$page = ceil(($photo_index + 1) / $results_count);
			$link = $page > 1 ? "javascript:category_page('$favorite',$page)" : "javascript:category('$favorite')";
			$hovertext = $category->path();
		}
		
		if ($index % $results_columns == 0) echo('<tr>');
		echo(td('results', hyperlink($link, img($photo->small_path(), $name, $photo->small_width(), $photo->small_height(), 'class="shadow"') . 
			$br . spacer_img(10, 10) . $br . $name, null, $hovertext), null, null, null, null, 'align="center"'));
		if ($index % $results_columns == $results_columns - 1) echo('</tr>');
	}

	$kavasoft_link = hyperlink('http://www.kavasoft.com/', 'KavaSoft', 'target="external"');
	$shoebox_link = hyperlink('http://www.kavasoft.com/Shoebox/', 'Shoebox', 'target="external"');
	$created_by = "Created by $shoebox_link $program_version from $kavasoft_link. $br Published on $formatted_date at $formatted_time. $br $copyright";
} else {
	$created_by = "$copyright";
}
$footer_row = "<tr><td colspan=\"3\" align=\"center\" valign=\"bottom\">$created_by</td></tr>";
	
echo('</table></td>');

echo("<td width=\"40\" valign=\"middle\" align=\"left\">");
$next_link = $homepage ? 'results.php' : 'javascript:next();';
if ($homepage || $show_next) echo(hyperlink($next_link, rollover_img(theme('next.png'), theme('next2.png'), 'nextArrow', 'Next', 40, 400), NULL, "Go to the next page (&rarr; key)"));
else echo(spacer_img(40, 400));
echo("</td>");

echo('</tr>');
if ($footer_row) echo($footer_row);

echo("</table>\n");
tail();

?>