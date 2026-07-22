<?php

include('../shared/layout.php');
include('../shared/category.php');

$columns = array();

$catid = $_GET['id'];
$name = $_GET['category'];
$search = $_GET['search'];

if ($name && !$catid) $catid = Category::id_for_name($name);
if ($search && !$catid) $catid = Category::id_for_name($search);

if ($catid) {
	$category = new Category($catid);
	
	$ancestors = $category->ancestors();
	
	foreach($ancestors as $i => $ancestor) {
		$columns[$i] = '&id=' . $ancestor->catid;
		if ($i < count($ancestors) - 1) {
			$selected = $ancestors[$i + 1];
			$columns[$i] .= '&selected=' . $selected->catid . '#selected';
		}
	}
} else {
	$columns[0] = '&id=0000000001';
}

$rows = array();
$num_columns = 10;
$browser_width = $column_width * $num_columns;

if ($browser_rows < 4) $browser_rows = 8;
$browser_height = 16 * $browser_rows;

for ($i = 0; $i < $num_columns; $i++) {
	$rows[] = td('browser', iframe("column.php?column=$i" . $columns[$i], "browser$i", $column_width, "$browser_height", 'auto'));
}

head('Shoebox', 'browser');
echo("<table width=\"$browser_width\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
echo(tr($rows));
echo("</table>\n");
tail();

?>