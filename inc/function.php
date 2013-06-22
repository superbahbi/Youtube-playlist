<?php
function check_str($arr, $title) {
	foreach ( $arr as $ar ) {
		if (strpos($title, $ar)) {
			return 1;
		}
	}
}
function del_str($arr, &$title) {
	foreach ( $arr as $ar ) {
		str_replace ($ar, "", $title);
	}
}
?>
