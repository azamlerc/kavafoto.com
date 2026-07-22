<?php

include('../shared/category.php');
include('../shared/layout.php');
include('../catalog/info.php');

head('Shoebox', 'column');

$catid = $_GET['id'];
$selected_id = $_GET['selected'];
$column = $_GET['column'];
if (!$column) $column = 0;
$next_column = $column + 1;
$cur_frame = "browser$column";
$next_frame = "browser$next_column";
$count = 0;

echo("<script type=\"text/javascript\">\n<!--\n\n");

echo("function click(catid) {\n");
	echo("\tparent.$next_frame.location.href=\"column.php?column=$next_column&id=\" + catid;\n");
	echo("\tparent.$cur_frame.location.href=\"column.php?column=$column&id=$catid&selected=\" + catid + \"#selected\";\n");
	echo("\tparent.parent.results.location.href=\"results.php?id=\" + catid;\n");
	echo("\tparent.parent.linkbar.location.href=\"toolbar.php?id=\" + catid;\n");

	if ($selected_id) {
		for ($i = $next_column + 1; $i < 10; $i++) {
			echo("\tparent.browser$i.location.href=\"column.php\";\n");
		}
	}
echo("}\n\n// -->\n</script>\n");

if ($catid) {
	$category = new Category($catid);
	$children = $category->children();
	$count = count($children);
}

$scroller = $count <= $browser_rows ? "\t<td width=\"15\" background=\"" . theme('scroller.png') . "\"></td>\n" : '';

if ($browser_rows < 4) $browser_rows = 8;

if ($count) {
	echo('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
	foreach ($children as $child) {
		$icon = $child->size_image();
		$name = $child->name();
		$child_id = $child->catid;
		
		$selected = $child_id == $selected_id;
		$has_children = count($child->children()) > 0;
		$image = "<img src=\"$icon\" width=\"16\" height=\"16\">";
		$link = "javascript:click('$child_id');";
		$class = $selected ? 'class="selected"' : 'class="browser"';
		$path = $child->path();
		$image = "<a href=\"$link\">$image</a>";
		$name = abbreviate($name, 24);
		$name = hyperlink($link, $name, $class);
		$arrow = $selected ? theme('arrow2.png') : theme('arrow.png');
		$arrow = $has_children ? "\t<td $class width=\"13\"><a href=\"$link\"><img src=\"$arrow\" width=\"13\" height=\"16\"></a></td>\n" : "\t<td $class>&nbsp;</td>\n";
		if ($selected) $name = "<a name=\"selected\"></a>" . $name;
		echo("<tr onmouseover=\"window.status='$path'; return true;\" onmouseout=\"window.status=''; return true;\">\n\t<td $class width=\"18\" valign=\"top\">\n\t$image</td>\n\t<td $class nowrap>$name</td>$arrow$scroller</tr>\n");
	}
	
	for ($i = $count; $i < $browser_rows; $i++) {
		echo("<tr>\n\t<td width=\"18\" height=\"16\" class=\"browser\">&nbsp;</td>\n\t<td class=\"browser\">&nbsp;</td>\n\t<td class=\"browser\">&nbsp;</td>$scroller</tr>\n");
	}
	
	echo('</table>');
} else {
	echo('<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">');
	echo("<tr><td>&nbsp;</td><td width=\"15\" background=\"" . theme('scroller.png') . "\">&nbsp;</td></tr>\n");
	echo('</table>');
}

tail();

?>