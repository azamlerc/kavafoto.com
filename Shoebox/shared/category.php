<?php
/**
 * This file contains the Artist, Album and Song classes.
 *
 * @package Shoebox
 * @author KavaSoft
 */

$docroot = '../';

function shoebox_join_path($base, $path) {
	return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function shoebox_photo_asset_path($path) {
	global $docroot;
	global $photo_base_url;

	if ($photo_base_url) return shoebox_join_path($photo_base_url, $path);
	return $docroot . ltrim($path, '/');
}

function shoebox_parse_php_variable($source, $variable_name) {
	if (!$source) return null;

	$source = preg_replace('/^\s*<\?(php)?/i', '', $source);
	$source = preg_replace('/\?>\s*$/', '', $source);

	$scope = array();
	$scope['__shoebox_source'] = $source;
	$scope['__shoebox_variable_name'] = $variable_name;
	eval($scope['__shoebox_source']);

	return isset($$variable_name) ? $$variable_name : null;
}

function shoebox_folder_info($folder_path) {
	global $docroot;
	global $photo_base_url;

	$local_path = $docroot . 'photos/' . $folder_path . 'index.php';
	if (file_exists($local_path)) {
		include($local_path);
		return isset($info) ? $info : null;
	}

	if ($photo_base_url) {
		$remote_path = shoebox_join_path($photo_base_url, $folder_path . 'index.php');
		$source = @file_get_contents($remote_path);
		$remote_info = shoebox_parse_php_variable($source, 'info');
		if ($remote_info) return $remote_info;
	}

	return null;
}

/**
 * The Category object represents a category. 
 *
 * Categories are identified using a catid like 1045251304-1622197219.
 *
 * @package Shoebox
 */
class Category {
	var $catid;
	var $name;
	var $display_name;
	var $parent_catid;
	var $children_catids;
	var $url;
	var $comments;
	var $photo_count;
	var $file_paths;
	var $top_rated;
	var $favorite;
	
	var $parent;
	var $children;
	
	var $alias;
	var $original;
	
// class methods

	function root_category() {
		return new Category('0000000001');
	}

	function who_category() {
		return new Category('0000000002');
	}

	function where_category() {
		return new Category('0000000003');
	}

	function what_category() {
		return new Category('0000000004');
	}

	function when_category() {
		return new Category('0000000005');
	}

	function id_for_name($name) {
		global $docroot;
		include($docroot . "catalog/search.php");
		$term = strtolower($name);
		$term = str_replace('_', ' ', $term);
		$result = $search[$term];
		if (is_array($result)) {
			$originals = array();
			foreach($result as $catid) {
				$category = new Category($catid);
				if (!$category->alias) $originals[] = $catid;
			}
			if (count($originals) == 1) return $originals[0];
			else return null;
		}
		else return $result;
	}

	function id_for_date($date) {
		global $docroot;
		include($docroot . "catalog/dates.php");
		return $dates[$date];
	}

	function ids_for_search($term) {
		global $docroot;
		include($docroot . "catalog/search.php");
		$term = strtolower($term);
		$term = str_replace('_', ' ', $term);
		
		$results = array();
		
		foreach ($search as $name => $result) {
			if (stripos_safe($name, $term) !== false) {
				if (is_array($result)) {
					$results = array_merge($results, $result);
				} else {
					$results[] = $result;
				}
			}
		}
		
		return $results;
	}

// instance methods

	function Category($catid) {
		global $docroot;
		$fileid = substr($catid, strlen($catid) - 1, 1);
		$path = $docroot . "catalog/categories/$fileid.php";
		if (file_exists($path)) include ($path);
		$info = $categories[$catid];

		$this->catid = $catid;
		$this->name = $info['name'];
		$this->display_name = $info['display_name'];
		$this->parent_catid = $info['parent'];
		$this->children_catids = $info['children'];
		$this->url = $info['url'];
		$this->comments = $info['comments'];
		$this->top_rated = $info['top_rated'];
		$this->alias = $info['alias'];
		$this->original = $info['original'];
		$this->favorite = $info['favorite'];
		$this->photo_count = $info['photo_count'];
		
		$this->file_paths = null;
		
		if (!$this->name) $this->name = $catid;
	}
	
	function name() {
		return $this->name;
	}

	function display_name() {
		return $this->display_name ? $this->display_name : $this->name;
	}

	function parent() {
		if (!$this->parent) {
			if ($this->parent_catid) {
				$this->parent = new Category($this->parent_catid);
			}
		}
		
		return $this->parent;
	}
	
	function children() {
		if ($this->alias == true) {
			$original = new Category($this->original);
			return $original->children();
		}
		
		if (!$this->children) {
			$this->children = array();
			if ($this->children_catids) {
				foreach ($this->children_catids as $child_catid) {
					$this->children[] = new Category($child_catid);
				}
			}
		}
		
		return $this->children;
	}
	
	function ancestors() {
		$parent = $this->parent();
		
		if ($parent) {
			$ancestors = $parent->ancestors();
			$ancestors[] = $this;
			return $ancestors;
		} else {
			return array($this);
		}
	}
	
	function path() {
		$parent = $this->parent();
		
		if ($parent) {
			$parent_path = $parent->path();
			if ($parent_path) $path = $parent_path . ' &gt; ' . $this->name;
			else $path = $this->name;
			$path = str_replace('\'', '\\\'', $path);
			return $path;
		}
		
		return '';
	}
	
	function file_paths() {
		if ($this->alias == true) {
			$original = new Category($this->original);
			return $original->file_paths();
		}
		
		if (!$this->file_paths) {
			global $docroot;
			$catid = $this->catid;
			$fileid = substr($catid, strlen($catid) - 2, 2);
			// if (strlen($catid) == 10) $fileid = $catid;
			$path = $docroot . "catalog/memberships/$fileid.php";
			if ($fileid && file_exists($path)) include ($path);
			$this->file_paths = $memberships[$catid];
		}
		
		return $this->file_paths;
	}
	
	function size_image() {
		if ($this->alias == true) {
			$original = new Category($this->original);
			return $original->size_image();
		}

		$count = $this->photo_count;
		
	         if ($count == 0)     return '../browser/images/size/0.png';
	    else if ($count == 1)     return '../browser/images/size/1.png';
	    else if ($count <= 2)     return '../browser/images/size/2.png';
	    else if ($count <= 5)     return '../browser/images/size/3.png';
	    else if ($count <= 10)    return '../browser/images/size/4.png';
	    else if ($count <= 20)    return '../browser/images/size/5.png';
	    else if ($count <= 50)    return '../browser/images/size/6.png';
	    else if ($count <= 100)   return '../browser/images/size/7.png';
	    else if ($count <= 200)   return '../browser/images/size/8.png';
	    else if ($count <= 500)   return '../browser/images/size/9.png';
	    else if ($count <= 1000)  return '../browser/images/size/10.png';
	    else if ($count <= 2000)  return '../browser/images/size/11.png';
	    else if ($count <= 5000)  return '../browser/images/size/12.png';
	    else if ($count <= 10000) return '../browser/images/size/13.png';
	    else if ($count <= 20000) return '../browser/images/size/14.png';
	    else if ($count <= 50000) return '../browser/images/size/15.png';
	    else                      return '../browser/images/size/16.png';
	}
	
	function link_type() {
		if (strpos($this->url, '@') !== false) return 'mail';
		else if (strpos($this->url, 'map') !== false) return 'map';
		else if (strpos($this->url, 'google') !== false) return 'google';
		else if (strpos($this->url, 'wikipedia') !== false) return 'wikipedia';
		else if (strpos($this->url, 'myspace') !== false) return 'myspace';
		else if (strpos($this->url, 'facebook') !== false) return 'facebook';
		else if (strpos($this->url, 'blog') !== false) return 'blogger';
		else return 'website';	
	}
	
	function favorite_photo() {
		if ($this->alias) {
			$original = new Category($this->original);
			return $original->favorite_photo();
		}
		
		if ($this->favorite) {
			return $this->favorite;
		} else if (isset($this->top_rated)) {
			$key = array_rand($this->top_rated);
			return $this->top_rated[$key];
		} else {
			$key = array_rand($this->file_paths());
			return $this->file_paths[$key];
		}
	}

	function random_photo() {
		if ($this->alias) {
			$original = new Category($this->original);
			return $original->random_photo();
		}
		
		$file_paths = $this->file_paths();
		if (!count($file_paths)) return null;
		
		$key = array_rand($file_paths);
		return $this->file_paths[$key];
	}
	
	function random_photo_with_rating($rating) {
		for ($i = 0; $i < 20; $i++) {
			$path = $this->random_photo();
			$photo = new Photo($path);
			if ($photo->rating >= $rating) return $path;
		}
		
		return $path;
	}
} 

/**
 * The Photo object represents a photo.
 *
 * Photos are identified using a path like 2007/02/03/DSC_8615.JPG.
 *
 * @package Shoebox
 */
class Photo {
	var $path;
	var $name;
	var $folder;
	var $extension;
	var $stem;
	
	var $width;
	var $height;
	var $aspect_ratio;
	
	var $comments;
	var $rating;
	
	var $who;
	var $what;
	var $where;
	var $when;
	var $etc;
	
	function all_photos() {
		global $all_photos;
		global $docroot;

		if (!$all_photos) {
			include($docroot . "catalog/all_files.php");		
			$all_photos = $all_files;
		}
		
		return $all_photos;
	}

	function info_for_folder($folder_path) {
		global $all_infos;
		
		if (!$all_infos) $all_infos = array();
		$info = $all_infos[$folder_path];
		
		if (!$info) {
			$info = shoebox_folder_info($folder_path);
			$all_infos[$folder_path] = $info;
		}
		
		return $info;
	}

	// returns paths to photos based on the form data
	// id=catid, category=name, search=search+terms
	function photos_for_request() {
		$catid = $_GET['id'];
		$name = $_GET['category'];
		$search = $_GET['search'];

		if ($name && !$catid) $catid = Category::id_for_name($name);
		if ($search && !$catid) $catid = Category::id_for_name($search);

		if ($catid) {
			$category = new Category($catid);
			return $category->file_paths();
		} else if ($search) {
			return Photo::photos_for_search($search);
		} else {
			return null;
		}
	}

	function name_for_search($search_terms) {
		global $docroot;
		
		$terms = explode(' ', $search_terms);
		$photos = null;
		
		include($docroot . 'catalog/searches.php');
		foreach ($searches as $search) {
			if (strcasecmp($search['name'], $search_terms) == 0)
				return $search['name'];
		}
	}

	function photos_for_search($search_terms) {
		global $docroot;
		
		$terms = explode(' ', $search_terms);
		$photos = null;
		
		include($docroot . 'catalog/searches.php');
		foreach ($searches as $search) {
			if (strcasecmp($search['name'], $search_terms) == 0)
				return $search['files'];
		}
		
		
		if (count($terms) == 1) {
			$ids = Category::ids_for_search($terms[0]);
			$photos = Photo::photos_for_categories($ids); // sort them!
		} else {
			$photos = array();
			$term = array_pop($terms);
			$ids = Category::ids_for_search($term);
			$photos = Photo::photos_for_categories($ids);
			
			foreach ($terms as $term) {
				$ids = Category::ids_for_search($term);
				$term_photos = Photo::photos_for_categories($ids);
				$photos = array_intersect($photos, $term_photos);
			}
		}

		sort($photos);
		return $photos;
	}

	function photos_for_categories($catids) {
		$photos = array();
		
		foreach ($catids as $catid) {
			$category = new Category($catid); 
			$category_files = $category->file_paths();
			if ($category_files) $photos = array_merge($photos, $category_files);
		}
		
		return array_unique($photos);
	}

// instance methods

	function Photo($path) {
		$this->path = $path;
		$this->name = substr(strrchr($path, '/'), 1);
		$this->folder = substr($path, 0, strlen($path) - strlen($this->name));
		$this->extension = substr(strrchr($path, '.'), 1);
		$this->stem = substr($path, 0, strlen($path) - strlen($this->extension) - 1);

		$folder_info = Photo::info_for_folder($this->folder);
		$info = $folder_info[$this->name];
		if (!$info) $info = array();
		
		$this->width = $info['width'];
		$this->height = $info['height'];
		$this->aspect_ratio = ($this->height > 0) ? $this->width / $this->height : 1.33333;
		
		$this->rating = $info['rating'];
		$this->comments = $info['comments'];

		$this->when = $this->category_array($info['when']);
		$this->where = $this->category_array($info['where']);
		$this->who = $this->category_array($info['who']);
		$this->what = $this->category_array($info['what']);
		$this->etc = $this->category_array($info['etc']);
		
		
	}

	function category_array($catids) {
		if (!$catids) return array();
		if (!is_array($catids)) return array($catids);
		return $catids;
	}

	function small_path() {
		return shoebox_photo_asset_path('photos/' . $this->stem . '_small.' . $this->extension);
	}

	function medium_path() {
		return shoebox_photo_asset_path('photos/' . $this->stem . '_medium.' . $this->extension);
	}

	function large_path() {
		return shoebox_photo_asset_path('photos/' . $this->stem . '_large.' . $this->extension);
	}

	function has_large_version() {
		global $photo_base_url;
		if ($photo_base_url) return true;
		return file_exists($this->large_path());
	}

	function short_path() {
		$short_path = $this->path;
		$short_path = str_replace('.jpg', '', $short_path);
		$short_path = str_replace('dsc_', '', $short_path);
		$short_path = str_replace('img_', '', $short_path);
		$short_path = str_replace('dscn', '', $short_path);
		return $short_path;
	}

	function small_width() {
		global $small_size;
		return $this->aspect_ratio < 1 ? round($small_size * $this->aspect_ratio) : $small_size;
	}

	function small_height() {
		global $small_size;
		return $this->aspect_ratio > 1 ? round($small_size / $this->aspect_ratio) : $small_size;
	}

	function medium_width() {
		global $medium_size;
		return $this->aspect_ratio < 1 ? round($medium_size * $this->aspect_ratio) : $medium_size;
	}

	function medium_height() {
		global $medium_size;
		return $this->aspect_ratio > 1 ? round($medium_size / $this->aspect_ratio) : $medium_size;
	}

	function large_width() {
		global $large_size;
		return $this->aspect_ratio < 1 ? round($large_size * $this->aspect_ratio) : $large_size;
	}

	function large_height() {
		global $large_size;
		return $this->aspect_ratio > 1 ? round($large_size / $this->aspect_ratio) : $large_size;
	}
	
	function who() {
		if (!$this->who) return array();
		if (!is_array($this->who)) return array($this->who);
		return $this->who;
	}
	
	function what() {
		if (!$this->what) return array();
		if (!is_array($this->what)) return array($this->what);
		return $this->what;
	}
	
	function where() {
		if (!$this->where) return array();
		if (!is_array($this->where)) return array($this->where);
		return $this->where;
	}
	
	function when() {
		if (!$this->when) return array();
		if (!is_array($this->when)) return array($this->when);
		return $this->when;
	}
	
	function etc() {
		if (!$this->etc) return array();
		if (!is_array($this->etc)) return array($this->etc);
		return $this->etc;
	}
}

function stripos_safe($haystack, $needle){
    return strpos($haystack, stristr($haystack, $needle));
}
