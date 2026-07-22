<?php

include('../catalog/info.php');
include('../shared/layout.php');
include('../shared/category.php');

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

$number = $_GET['number'];
$number--; // indexed from one 
$path = $_GET['photo'];

$results_count = $results_columns * $results_rows;

$paths = Photo::photos_for_request();

if ($paths) {
	if ($path) $number = array_search($path, $paths);
	else $path = $paths[$number];
	$page = ceil($number / 20);
	$photo = new Photo($path);

	$count = count($paths);
	$show_previous = $number > 0;
	$show_next = $number < $count - 1;
}

head('Shoebox', 'results', 'self.focus(); onload_cancelMouseClick();');
echo('<script src="../shared/shortcut.js" type="text/javascript" charset="utf-8"></script>');
echo('<script src="../shared/reflection.js" type="text/javascript" charset="utf-8"></script>');
echo("\n<script type=\"text/javascript\">\n<!--\n\n");

echo("shortcut.add(\"Left\", function() { previous(); });\n");
echo("shortcut.add(\"Right\", function() { next(); });\n");
echo("shortcut.add(\"Space\", function() { next(); });\n\n");
echo("shortcut.add(\"Up\", function() { index(); });\n\n");
echo("shortcut.add(\"Return\", function() { index(); });\n\n");
echo("shortcut.add(\"Escape\", function() { parent.location.href=\"../index.php\"; });\n");
echo("\n");

echo("function category(catid) {\n");
	if ($page > 1) $arg = " + \"&page=$page\"";
	echo("\tparent.results.location.href=\"results.php?id=\" + catid$arg;\n");
	echo("\tparent.browser.location.href=\"browser.php?id=\" + catid$arg;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?id=\" + catid$arg;\n");
echo("}\n\n");

echo("function category_page(catid, page) {\n");
	echo("\tparent.results.location.href=\"results.php?id=\" + catid + \"&page=\" + page;\n");
	echo("\tparent.browser.location.href=\"browser.php?id=\" + catid;\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?id=\" + catid + \"&page=\" + page;\n");
echo("}\n\n");

if ($catid) {
	$arg = "id=$catid";
} else if ($name) {
	$arg = "category=$name";
} else if ($search) {
	$arg = "search=" . str_replace(' ', '+', $search);
}

if ($show_previous) {
	echo("function previous() {\n");
	$previous = $number;
	echo("\tparent.results.location.href=\"photo.php?$arg&number=$previous\";\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?$arg&number=$previous\";\n");
	echo("}\n\n");
}

if ($show_next) {
	echo("function next() {\n");
	$next = $number + 2;
	echo("\tparent.results.location.href=\"photo.php?$arg&number=$next\";\n");
	echo("\tparent.linkbar.location.href=\"toolbar.php?$arg&number=$next\";\n");
	echo("}\n\n");
}
?>

function cancelMouseClick(e){
 	return false;
}

function onload_cancelMouseClick() {
	var allImages = document.getElementsByTagName('IMG')
	for(var i=0; allImages.length;i++){
		allImages[i].oncontextmenu= cancelMouseClick;
		allImages[i].onmousedown= cancelMouseClick;
		allImages[i].onmouseup= cancelMouseClick;
	}
}

<?php

echo("function index() {\n");
$page = ceil(($number + 1) / $results_count);
if ($page > 1) $page_arg = "&page=$page";

echo("\tparent.parent.results.location.href=\"results.php?$arg$page_arg\";\n");
echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg$page_arg\";\n");
echo("}\n\n");

echo("// -->\n</script>\n");
echo("<table class=\"results\" width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"10\" cellpadding=\"0\"><tr>");

function info_table_row($label, $categories) {
	global $photo;
	global $results_columns;
	global $results_rows;
	$results_count = $results_columns * $results_rows;
	
	if (!$categories || count($categories) == 0) return '';
	$row = "<tr>\n\t<td align=\"right\" valign=\"top\">$label:&nbsp;&nbsp;</td>\n\t<td>\n\t\t";
	foreach ($categories as $i => $catid) {
		$category = new Category($catid);

		$photo_index = array_search($photo->path, $category->file_paths());
		$page = ceil(($photo_index + 1) / $results_count);

		if ($page > 1) $link = "javascript:category_page('$catid',$page)";
		else $link = "javascript:category('$catid')";
		$row .= hyperlink($link, $category->display_name(), NULL, $category->path());
		$url = $category->url;
	
		if ($url) {
			$link_type = $category->link_type();
			
			$row .= ' ' . hyperlink($url, img("images/links/$link_type.png", $link_type, 16, 16), 'target="external"');
		}
		if ($i < count($categories) - 1) $row .= '<br/>';
	} 
	$row .= "\n\t</td>\n</tr>\n"; 
	return $row;
}

function names_for_categories($categories) {
	$names = array();
	
	foreach ($categories as $catid) {
		$category = new Category($catid);
		$names[] = $category->display_name();
	}
	
	return $names;
}

if ($path) {
	echo("<td width=\"40\" valign=\"middle\" align=\"left\">");

	echo(!$show_previous ? spacer_img(40, 400) : hyperlink('javascript:previous();', 
		rollover_img(theme('previous.png'), theme('previous2.png'), 'previousArrow', 'Previous', 40, 400), NULL, 
		"Go to the previous picture (&larr; key)"));
	echo("</td>");
	
	$short_path = $photo->short_path();

	$info_table = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
	$info_table .= info_table_row('when', $photo->when);
	$info_table .= info_table_row('where', $photo->where);
	$info_table .= info_table_row('who', $photo->who);
	$info_table .= info_table_row('what', $photo->what);
	$info_table .= info_table_row('etc', $photo->etc);
	$info_table .= "<tr>\n\t<td align=\"right\" valign=\"top\">file:&nbsp;&nbsp;</td>\n\t<td>\n\t\t";
	$info_table .= $short_path;
	$info_table .= "\n\t</td>\n</tr>\n"; 
	$info_table .= '</table>';
	
	$alt_array = array();
	$alt_array = array_merge($alt_array, names_for_categories($photo->where));
	$alt_array = array_merge($alt_array, names_for_categories($photo->who));
	$alt_array = array_merge($alt_array, names_for_categories($photo->what));
	$alt_array = array_merge($alt_array, names_for_categories($photo->etc));
	$alt = implode(', ', $alt_array);
	
	$has_large = file_exists($photo->large_path());
	$image = img($photo->medium_path(), $alt, $photo->medium_width(), $photo->medium_height(), "class=\"shadow\"");
	if ($has_large) {
		$image = 
		popup($photo->large_path(), $photo->name, $photo->large_width(), $photo->large_height(), $image, null, 'Open a large version of this photo in a new window');
		$enlarge_button = 
		popup($photo->large_path(), $photo->name, $photo->large_width(), $photo->large_height(), 	
		rollover_img(theme('enlarge.png'), theme('enlarge2.png'), 'enlarge', 'Enlarge', 20, 20, 'class="enlarge"'), null, 'Open a large version of this photo in a new window');
	}
	
	echo("<td height=\"100%\" align=\"center\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td rowspan=\"3\">$image</td><td rowspan=\"3\" width=\"15\">&nbsp;</td><td valign=\"top\">$info_table</td></tr>");
	echo("<tr><td valign=\"bottom\" width=\"200\"><br>$photo->comments</td></tr>");
	echo("<tr><td valign=\"bottom\">$enlarge_button</td></tr>");
	echo("</table></td>");

	echo("<td width=\"40\" valign=\"middle\" align=\"left\">");
	
	echo(!$show_next ? spacer_img(40, 400) : hyperlink('javascript:next();', 
		rollover_img(theme('next.png'), theme('next2.png'), 'nextArrow', 'Next', 40, 400), NULL, 
		"Go to the next picture (&rarr; key)"));
	echo("</td>");

	if ($copyright) {
		$copyright = copyright($copyright, $author, $email, $title, bookmark(new Category($catid), $search, $page, $path));
		echo("</tr><tr><td colspan=\"3\" align=\"center\">$copyright</td>");
	}
}

echo("</tr>\n</table>\n");
tail();

?>