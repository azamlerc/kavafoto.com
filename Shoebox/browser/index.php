<?php

include('../shared/layout.php');
include('../shared/category.php');
include('../catalog/info.php');

head($title, 'results');

function print_outline($category) {

	$name = $category->name();
	$files = $category->photo_count;
	$catid = $category->catid;
	
	echo("<ul>\n");
	echo("<li>\n");
		echo(hyperlink("../index.php?id=$catid", $name));
		if ($files) {
			$link = $files == 1 ? "1 photo" : "$files photos";
			// $link = hyperlink("index.php?id=$catid", $link);
			echo(" ($link)");
		} 
		
		$children = $category->children();
		if ($children && count($children)) {
			foreach ($children as $child) {
				print_outline($child);
			}
		}
	echo("</li>\n");
	echo("</ul>\n");
}

$catid = $_GET['id'];
$category = $catid ? new Category($catid) : Category::root_category();
print_outline($category);

tail();

?>