<?php

include('../catalog/info.php');
include('../shared/layout.php');
include('../shared/category.php');

$catid = $_GET['id'];
$name = $_GET['category'];
$search = $_GET['search'];

if ($name && !$catid) $catid = Category::id_for_name($name);
if ($search && !$catid) $catid = Category::id_for_name($search);
if ($catid) $category = new Category($catid);

$paths = Photo::photos_for_request();

$page = $_GET['page'];
if (!$page) $page = 1;
$total = 0;
$number = $_GET['number'];
$path = $_GET['photo'];
$show_photo = $number > 0 || $path !== null;
if ($show_photo) $index = $number - 1;

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
		}
		
		$show_previous = $number > 1;
		$show_next = $number < $count;
	} else {
		$show_previous = $page > 1;
		$show_next = $page < $total;
	}
}

head('Shoebox', 'toolbar');
echo("<script type=\"text/javascript\">\n<!--\n\n");

if ($catid) {
	$arg = "id=$catid";
} else if ($search) {
	$arg = "search=" . str_replace(' ', '+', $search);
}

if ($show_previous) {
	echo("function previous() {\n");
	if ($show_photo) {
		$previous = $number - 1;
		echo("\tparent.parent.results.location.href=\"photo.php?$arg&number=$previous\";\n");
		echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg&number=$previous\";\n");
	} else {
		$previous = $page - 1;
		echo("\tparent.parent.results.location.href=\"results.php?$arg&page=$previous\";\n");
		echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg&page=$previous\";\n");
	}
	echo("}\n\n");
}

if ($show_next) {
	echo("function next() {\n");
	if ($show_photo) {
		$next = $number + 1;
		echo("\tparent.parent.results.location.href=\"photo.php?$arg&number=$next\";\n");
		echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg&number=$next\";\n");
	} else {
		$next = $page + 1;
		echo("\tparent.parent.results.location.href=\"results.php?$arg&page=$next\";\n");
		echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg&page=$next\";\n");
	}
	echo("}\n\n");
}

if ($show_photo) {
	echo("function index() {\n");

	$page = ceil($number / $results_count);
	if ($page > 1) $page_arg = "&page=$page";

	echo("\tparent.parent.results.location.href=\"results.php?$arg$page_arg\";\n");
	echo("\tparent.parent.linkbar.location.href=\"toolbar.php?$arg$page_arg\";\n");
	echo('}');
}

echo("function fullScreen(theURL) {\n");
echo("\twindow.open(theURL, '', 'fullscreen');\n");
echo("}\n\n");

echo("// -->\n</script>\n");
echo("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>\n<td class=\"header\" width=\"190\">");

// $title = 'Shoebox 2';
if ($category) $title = $category->display_name();
else if ($search) {
	$search_title = Photo::name_for_search($search);
	if ($search_title) $title = $search_title;
}

echo($title);
echo("</td>\n<td align=\"center\" valign=\"middle\">");
$title = "Shoebox - $title";

function facebook($title, $bookmark) {
	return "javascript:var d=document,f='http://www.facebook.com/share',l=d.location,e=encodeURIComponent,p='.php?src=bm&v=4&i=1223982827&u='+e('$bookmark')+'&t='+e('$title');1;try{if (!/^(.*\.)?facebook\.[^.]*$/.test(l.host))throw(0);share_internal_bookmarklet(p)}catch(z) {a=function() {if (!window.open(f+'r'+p,'sharer','toolbar=0,status=0,resizable=1,width=626,height=436'))l.href=f+p};if (/Firefox/.test(navigator.userAgent))setTimeout(a,0);else{a()}}void(0)";
}

function email_link($title, $bookmark) {
	$title = str_replace('+', '%20', urlencode($title));
	$bookmark = urlencode($bookmark);
	return "mailto:?subject=$title&body=$bookmark";
}

function shoebox_link($category, $search) {
	if ($category) {
		$name = $category->name;
		if (strlen($name) > 2) {
			$name = str_replace(' ', '+', $name);
			$base = "catname=$name";
		} else {
			$catid = $category->catid;
			$base = "catid=$catid";
		}
	} else if ($search) {
		$search = str_replace(' ', '+', $search);
		$base = "search=$search";
	}

	return "shoebox://localhost/$base$arg";
}
	
$show_shoebox = strpos(page_url(), 'local') > 0;
	
if ($paths) {
	$count = count($paths);
	
	$major_separator = ' &bull; ';
	$minor_separator = ' / ';
	
	if ($show_photo) {
		$photo = new Photo($paths[$index]);
		
		printf("photo %s of %s", number_format($number), number_format($count));

		if ($count > 1) echo($major_separator . hyperlink('javascript:index()', 'index', null, 'Go back to the thumbnails for this category (&uarr; key)'));
		$bookmark = bookmark($category, $search, $page, $photo->path);
	} else {
		if ($count == 0) echo('no photos');
		else if ($count == 1) echo ('1 photo');
		else printf("%s photos", number_format($count));
	
		if ($show_previous || $show_next) echo($major_separator . "page $page of $total");
	
		$bookmark = bookmark($category, $search, $page);
	}

	// if ($show_previous) echo($minor_separator . hyperlink('javascript:previous();', 'previous'));
	// if ($show_next) echo($minor_separator . hyperlink('javascript:next();', 'next'));

	echo($major_separator . hyperlink('../index.php', 'home', 'target="_top"', 'Go back to the home page (escape key)'));
//	echo($major_separator . hyperlink("javascript:fullScreen('$bookmark');", 'fullscreen'));
	echo($major_separator . hyperlink("javascript:self.moveTo(0,0);self.resizeTo(screen.availWidth,screen.availHeight);", 'fullscreen'));
	echo($major_separator . hyperlink($bookmark, 'link', 'target="_top"'));
	echo($major_separator . hyperlink(email_link($title, $bookmark), 'email', null, 'Email a link to this page'));
	echo($major_separator . hyperlink(facebook($title, $bookmark), 'facebook', null, 'Share this page on Facebook'));
	if ($show_shoebox) echo($major_separator . hyperlink(shoebox_link($category, $search), 'shoebox', null, 'Go to this category in the Shoebox application'));
}

?>
</td>
<!-- <script type="text/javascript" src="http://s7.addthis.com/js/200/addthis_widget.js"></script> -->
<form action="results.php" method="get" target="results">
	<td align="right" width="190">
	<input type="hidden" name="update" value="toolbar">
	<input type="search" name="search" autosave="shoeboxsearch" results="5" size="25" value="<?php echo($search); ?>"/>
	</td>
</form>
</table>
</body>
</html>