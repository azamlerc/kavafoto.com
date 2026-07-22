<?php
/**
 * This file contains a number of useful functions for generating HTML and formatting data. 
 *
 * @package iTunes_Catalog
 * @author KavaSoft
 */

$br = '<br />';

if (file_exists("../catalog/info.php")) include("../catalog/info.php");

// make sure that the theme is valid
if (!($theme == 'light' || $theme == 'dark')) $theme = 'light';

function theme($filename) {
	global $theme;
	return "themes/$theme/$filename";
}

function cookie_monster($key, $default) {
	$value = $_GET[$key]; // check if a cookie is being set
	if ($value) setcookie($key, $value); // if so, set the cookie
	if (!$value) $value = $_COOKIE[$key]; // check if the cookie is already set
	if (!$value) $value = $default; // otherwise use the default value
	return $value;
}

/**
 * Prints the <doctype> and <html> tags.
 */
function doctype() {
	echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
	echo("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");
}	

/**
 * Prints a <head> tag with title, generator, author, and stylesheet attributes.
 *
 * @param string the title of the page
 * @param string the name of the stylesheet
 */
function head($title, $class, $onload = NULL) {
	echo("<html>\n");
	echo("<head>\n");
	echo("\t<title>$title</title>\n");
	echo("\t<meta name=\"generator\" content=\"" . $GLOBALS['generator'] . "\" />\n");
	echo("\t<meta name=\"author\" content=\"" . $GLOBALS['author'] . "\" />\n");
	echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"screen\" />");
	echo("\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . theme('colors.css') . "\" media=\"screen\" />");
	echo("</head>\n");
	if ($onload) $onload = " onload=\"$onload\"";
	echo("<body class=\"$class\"$onload>\n");
}	

function tail() {
	echo("</body>\n");
	echo("</html>");
}

/**
 * Returns a <td> table cell tag.
 *
 * @param string the class
 * @param string the content that should be included inside the tag
 * @param integer the width in pixels
 * @param integer the height in pixels
 * @param integer the number of columns
 * @param integer the number of rows
 * @param string extra attributes to be included in the tag
 * @return string the <td> tag
 */
function td($class, $content, $width = NULL, $height = NULL, $colspan = 1, $rowspan = 1, $stuff = '') {
	if ($stuff) $stuff = ' ' . $stuff;
	if ($class) $stuff .= " class=\"$class\"";
	if ($width) $stuff .= " width=\"$width\"";
	if ($height) $stuff .= " height=\"$height\"";
	if ($colspan > 1) $stuff .= " colspan=\"$colspan\"";
	if ($rowspan > 1) $stuff .= " rowspan=\"$rowspan\"";
	return "\t<td$stuff>\n\t\t$content\n\t</td>\n";
}

/**
 * Returns a <tr> table row tag.
 *
 * @param array an array of <td> tags
 * @return string the <tr> tag
 */
function tr($rows) {
	$tr = "<tr>\n";
	foreach($rows as $row) $tr .= $row;
	return $tr . "</tr>\n";
}

/**
 * Returns a <a> hyperlink tag.
 *
 * @param string the path to the destination file
 * @param string the text or image to be linked
 * @param string extra attributes to be included in the tag
 * @return string the <a> tag
 */
function hyperlink($path, $text, $stuff = '', $hovertext = '') {
	if ($stuff) $stuff = ' ' . $stuff;
	if ($hovertext) $hovertext = "
		onmouseover=\"window.status='$hovertext'; return true;\" 
		onmouseout=\"window.status=''; return true;\"";
	return "<a href=\"$path\"$stuff$hovertext>$text</a>";
}

/**
 * Returns a <a> tag with a name attribute.
 *
 * @param string the name of the anchor
 * @return string the <a> tag
 */
function anchor($name) {
	return "<a name=\"$name\"></a>";
}

/*
function artist_link($artist_key, $text, $stuff = 'target="_top"') {
	$artist_link = $GLOBALS['artist_link'];
	$path = "../$artist_link/index.php?artist=$artist_key";
	return hyperlink($path, $text, $stuff);
}

function album_link($artist_key, $album_key, $text, $stuff = 'target="_top"') {
	$artist_link = $GLOBALS['album_link'];
	$path = "../$artist_link/index.php?artist=$artist_key&album=$album_key";
	return hyperlink($path, $text, $stuff);
}
*/

/**
 * Returns an <img> image tag.
 *
 * @param string the path to the image file
 * @param string the alternate text
 * @param integer the width in pixels
 * @param integer the height in pixels
 * @param string extra attributes to be included in the tag
 * @return string the <img> tag
 */
function img($path, $alt, $width = '', $height = '', $stuff = '') {
	$alt = " alt=\"$alt\"";
	if ($width) $width = " width=\"$width\""; else $width = '';
	if ($height) $height = " height=\"$height\""; else $height = '';
	if ($stuff) $stuff = ' ' . $stuff;
	return "<img src=\"$path\"$alt$width$height$stuff />";
}

function rollover_img($path, $hover_path, $name, $alt, $width = 0, $height = 0, $stuff = '') {
	$alt = "alt=\"$alt\"";
	if ($width) $width = " width=\"$width\"";
	if ($height) $height = " height=\"$height\"";
	if ($stuff) $stuff = ' ' . $stuff;
	return "\t<img src=\"$path\" name=\"$name\" \n" .
	 	"\t\tonMouseOver=\"document.$name.src='$hover_path'\" \n" .
		"\t\tonMouseOut=\"document.$name.src='$path'\"\n\t\t$alt$width$height$stuff />\n";
}

function spacer_img($width, $height) {
	return img('images/spacer.gif', 'spacer', $width, $height);
}

/**
 * Returns a <div> tag.
 *
 * @param string the class
 * @param string the text
 * @return string the <div> tag
 */
function div($class, $text) {
	return "<div class=\"$class\">$text</div>\n";
}

/**
 * Returns a <frameset> tag.
 *
 * @param string either 'rows' or 'cols'
 * @param string the size in pixels of the frames, e.g. '30,*,10'
 * @param array the frames or framesets
 * @return string the <frameset> tag
 */
function frameset($dimension, $sizes, $frames) {
	$frameset = "<frameset $dimension=\"$sizes\" border=\"0\">\n";
	foreach($frames as $frame) $frameset .= "$frame";
	return $frameset . "</frameset>\n";
}

/**
 * Returns a <frame> tag.
 *
 * @param string the path to the source file
 * @param string the name of the frame
 * @param string whether scrolling is allowed, 'yes'|'no'|'auto'
 * @param string the class
 * @return string the <frame> tag
 */
function frame($src, $name, $scrolling, $class = NULL) {
	if ($class) $class = " class=\"$class\"";
	return ("<frame$class src=\"" . $src . "\" name=\"" . $name . "\" scrolling=\"" . $scrolling . "\" />\n");
}

/**
 * Returns an <iframe> inline frame tag.
 *
 * @param string the path to the source file
 * @param string the name of the frame
 * @param integer the width in pixels
 * @param integer the height in pixels
 * @param string whether scrolling is allowed, 'yes'|'no'|'auto'
 * @return string the <iframe> tag
 */
function iframe($src, $name, $width, $height, $scroll = 'auto') {
	return "<iframe src=\"$src\"\n\t\tname=\"$name\" width=\"$width\" height=\"$height\" scrolling=\"$scroll\" frameborder=\"0\"></iframe>";
}

/**
 * Returns an <a> hyperlink that will open a popup window.
 *
 * This method is preferable to creating popups with JavaScript functions, as these are blocked by many browsers.
 *
 * @param string the path to the destination file
 * @param string the name of the popup window
 * @param integer the width of the window in pixels
 * @param integer the height of the window in pixels
 * @param string the text or image to be linked
 * @param string extra attributes to be included in the tag
 * @return string the <a> tag
 */
function popup($path, $windowname, $width, $height, $text, $stuff = '', $hovertext = '') {
	if ($stuff) $stuff .= ' '; else $stuff = '';
	$stuff .= "onclick=\"javascript:window.open('$path','$windowname','width=$width,height=$height'); return false;\"";
	return hyperlink('', $text, $stuff, $hovertext);
}

function page_url() {
	$url = 'http';
	if ($_SERVER["HTTPS"] == "on") $url .= "s";
 	$url .= "://";
 	if ($_SERVER["SERVER_PORT"] != "80") 
 		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 	else
  		$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	
	return $url;
}

function addthis($path, $title, $text, $stuff = '') {
	if ($stuff) $stuff = ' ' . $stuff;
 	return "<a href=\"http://www.addthis.com/bookmark.php\" 
	        onmouseover=\"return addthis_open(this, '', '$path', '$title');\" 
	        onmouseout=\"addthis_close();\" 
	        onclick=\"return addthis_sendto();\" target=\"results\"$stuff>$text</a>";
}

/**
 * Composes a GET URL from a path, arguments, and optionally an anchor. 
 *
 * For example, if $string is 'file.php' and $args are { 'artist' => 'pink_floyd', 'album' => 'pulse' }, 
 * will return 'file.php?artist=pink_floyd&artist=pulse'
 *
 * @param string the path to the file
 * @param array a keyed array
 * @param string an anchor to append to the end, e.g. '#selected'
 * @return string the full path
 */
function path_with_args($path, $args, $anchor = '') {
	$separator = '?';
	if ($args) {
		foreach($args as $key => $value) {
			$path .= $separator . $key . '=' . $value;
			$separator = '&';
		}
	}
	$path .= $anchor;
	return $path;
}

/**
 * Returns the total time in days, hours, minutes and seconds.
 *
 * @param integer the the time in seconds
 * @return string the time represented in the format 'dd:hh:mm:ss'
 */
function time_format($time) {
	$days = NULL;
	$hours = NULL;
	
	$seconds = $time % 60; 
	$minutes = floor($time / 60) % 60;
	if ($time > 3600) $hours = floor($time / 3600) % 24;
	if ($time > 86400) $days = floor($time / 86400);
	
	$formatted_time = '';
	if ($days) $formatted_time .= $days . ':';
	if ($hours || $days) $formatted_time .= sprintf($days ? "%02d:" : "%d:", $hours);
	$formatted_time .= sprintf($hours || $days ? "%02d:" : "%d:", $minutes);
	$formatted_time .= sprintf("%02d", $seconds);
	
	return $formatted_time;
}

/**
 * Returns the total time in days, hours or minutes.
 *
 * @param integer the the time in seconds
 * @return string the time represented in the format '5.7 hours' 
 */
function time_format_simple($time) {
	$minutes = $time / 60;
	$hours = $minutes / 60;
	$days = $hours / 24;

	if ($hours < 1) 
		return local_format($minutes, 1) . ' ' . $GLOBALS['minutes'];
	else if ($days < 1) 
		return local_format($hours, 1) . ' ' . $GLOBALS['hours'];
	else 
		return local_format($days, 1) . ' ' . $GLOBALS['days'];
}

/**
 * Returns the number formatted in the local format with the specified number of decimal places.
 *
 * @param double a number
 * @param integer the number of decimal places
 * @return string the formatted number, e.g. '5.234,23' in the German locale
 */
function local_format($number, $places = 0) {
	return number_format($number, $places, $GLOBALS['decimal'], $GLOBALS['thousands']);
}

/**
 * Returns a formatted size.
 *
 * @param double the size in kilobytes
 * @return string the formatted size, e.g. '123 KB', '12.3 MB', or '1.23 GB'
 */
function size_format($size) {
    $kilobytes = $size;
    $megabytes = $kilobytes / 1024.0;
    $gigabytes = $megabytes / 1024.0;

    if ($kilobytes < 1024) 
		return local_format($kilobytes, 0) . ' ' . $GLOBALS['kb'];
    else if ($megabytes < 1024) 
		return local_format($megabytes, 1) . ' ' . $GLOBALS['mb'];
    else 
		return local_format($gigabytes, 2) . ' ' . $GLOBALS['gb'];
}

/**
 * Returns a formatted rating.
 *
 * @param integer the rating number, between 0 and 100
 * @return string the rating in HTML, e.g. rating(60) would return '&#9733;&#9733;&#9733;'
 */
function rating($stars) {
	$rating = '';
	while ($stars > 0) {
		$rating .= '&#9733;';
		$stars -= 20;
	}
	return $rating;
} 

/**
 * Truncates a string to a given number of characters, adding an ellipsis if necessary.
 *
 * @param string a string
 * @param integer the number of characters
 * @return string the truncated string
 */
function abbreviate($value, $length = 40) {
	// if the whole text is in a foreign alphabet, don't shorten it
	if ($value[0] == '&' && $value[strlen($value) - 1] == ';') return $value;
	
	$string = str_replace('&rsquo;', 'Õ', $value);

	if (strlen($string) > $length && strlen(html_entity_decode($string)) > $length) {
		$string = wordwrap($string, $length - 3, '&hellip;');
		$dots = strpos($string, '&hellip;');
		if ($dots > $length || $dots === FALSE) {
			$string = substr($string, 0, $length) . '&hellip;';
		} else if ($dots) {
			$string = substr($string, 0, $dots + 8);
		}
		$string = str_replace('Õ', '&rsquo;', $string);
		return $string;
	} else {
		return $value;
	}
}

/**
 * Returns the word 'song' or 'songs' depending upon whether $count is plural.
 *
 * @param integer the number of songs
 * @return string 'song' if $count is 1, 'songs' otherwise
 */
function songs_plural($count) {
	return $GLOBALS[$count == 1 ? 'songs_single' : 'songs_plural'];
}

/**
 * Returns the time it took to load the page in seconds and milliseconds.
 *
 * Set $start_time to microtime() when beginning to load the page,
 * then call load_time($start_time) after loading the page.
 *
 * @param string the start time from calling microtime()
 * @return float the time elapsed since $start_time in seconds and milliseconds
 */
function load_time($start_time) {
	$start_time_array = explode(' ', $start_time);
	$end_time_array = explode(' ', microtime());
	$load_secs = $end_time_array[1] - $start_time_array[1];
	$load_microsecs = $end_time_array[0] - $start_time_array[0];
	return round($load_secs + $load_microsecs, 3);
} 

/**
 * Returns the code for a QuickTime player plugin.
 *
 * @param string the path to the audio file
 * @param integer the width in pixels
 * @param bool whether the player should start automatically
 * @return string the <object> tag for the player
 */
function quicktime_player($source, $width, $autoplay) {
	$play = $autoplay ? 'true' : 'false';
	return "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6E\" width=\"$width\" height=\"16\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\">
	<param name=\"controller\" value=\"TRUE\" />	
	<param name=\"type\" value=\"video/quicktime\" />
	<param name=\"autoplay\" value=\"$play\" />
	<param name=\"target\" value=\"myself\" />
	<param name=\"src\" value=\"$source\" />
	<param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/indext.html\" />
	<embed width=\"$width\" height=\"16\" controller=\"TRUE\" autoplay=\"$play\" target=\"myself\" src=\"$source\" type=\"video/quicktime\" 
	bgcolor=\"#EEEEEE\" border=\"0\" pluginspage=\"http://www.apple.com/quicktime/download/indext.html\"></embed></object>";
}

/**
 * Converts an email address into HTML entities to make it more difficult for spambots to harvest.
 *
 * @param string the email address
 * @return string the encoded address
 */
function scramble_email($string) {
	$chars = array();
	$ent = null;
	
	$chars = preg_split("//", $string, -1, PREG_SPLIT_NO_EMPTY);	
	
	for ( $i = 0; $i < count($chars); $i++) {
		$ent[$i] = "&#" . ord($chars[$i]) . ";";
	}
	
	if (sizeof($ent) < 1) return "";
	
	return implode("",$ent);
}

function unique_name($name) {
	include('../catalog/search.php');
	return !is_array($search[strtolower($name)]);
}

function bookmark($category, $search, $page, $path = null) {
	$url = page_url();
	$url = str_replace('zamler-carhart.kavasoft.com', 'www.zamler-carhart.com', $url);
	$offset = strpos($url, '/browser');
	$url = substr($url, 0, $offset);

	if ($category) {
		$name = $category->name;
		if (strlen($name) > 2 && unique_name($name) && strpos($name, '&') === false) {
			$name = str_replace(' ', '_', $name);
			$base = "category=$name";
		} else {
			$catid = $category->catid;
			$base = "id=$catid";
		}
	} else if ($search) {
		$search = str_replace(' ', '+', $search);
		$base = "search=$search";
	}

	if ($page > 1) $arg = "&page=$page";
	if ($path) $arg = "&photo=$path";
	return "$url/?$base$arg";
}

function copyright($copyright, $author, $email, $title, $bookmark) {
	$title = str_replace('+', '%20', urlencode($title));
	$bookmark = urlencode($bookmark);	return str_replace($author, hyperlink(scramble_email("mailto:$email" . "?subject=$title&body=$bookmark"), $author), $copyright);
}

?>