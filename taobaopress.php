<?php

define('TAOBAOPRESS_PATH', dirname(__FILE__) . '/');
include_once 'function.php';

if (false !== strpos($current_page, 'plugins.php')) {
	include_once(TAOBAOPRESS_PATH . 'setup/activation.php');
    register_activation_hook(__FILE__, 'taobaopress_activate_plugin');
    register_deactivation_hook(__FILE__, 'taobaopress_deactivate_plugin');
}

if (ereg('/wp-admin/', $_SERVER['REQUEST_URI'])) { // just load in admin
	include_once (TAOBAOPRESS_PATH .'setup/menu-hook.php');
	add_action('admin_menu', 'taobaopress_option_menu');
}
else {
	// add style
	add_action('wp_print_styles', 'taobaopress_stylesheet');
	if (!function_exists('taobaopress_stylesheet')) {
		function taobaopress_stylesheet() {
			$myStyleUrl =  '/wp-content/themes/taobaopress/styles.css';
			wp_register_style('taobaoStyleSheets', $myStyleUrl);
			wp_enqueue_style( 'taobaoStyleSheets');
		}
	}

	add_filter('the_content', 'taobaopress_default');
}

function getItemId( $content ) {
	$item = array();
	if ( preg_match('/\\[id=[0-9.]{1,10}\\]/',$content) ) {
		$regex ="/(\[id=[0-9.]{1,10})\\]/U"; 
		preg_match_all($regex,$content,$arr);
		if ( count($arr[0]) > 0 ) {
			list($key,$iid) = split('=',$arr[0][0]);
			$iid = preg_replace('/\\]/',"",$iid);;
			$item[0] = $arr[0][0];
			$item[1] = $iid;
			$item[2] = "id";
		}
	}
	else if ( preg_match('/\\[item-[0-9.]{1,10}\\]/',$content) ) {
		$regex ="/(\[item-[0-9.]{1,10})\\]/U"; 
		preg_match_all($regex,$content,$arr);
		if ( count($arr[0]) > 0 ) {
			list($key,$iid) = split('-',$arr[0][0]);
			$iid = preg_replace('/\\]/',"",$iid);;
			$item[0] = $arr[0][0];
			$item[1] = $iid;
			$item[2] = "item";
			
		}
	}
	else if ( preg_match('/\\[image=[0-9.]{1,10}\\]/',$content) ) {
		$regex ="/(\[image=[0-9.]{1,10})\\]/U"; 
		preg_match_all($regex,$content,$arr);
		if ( count($arr[0]) > 0 ) {
			list($key,$iid) = split('=',$arr[0][0]);
			$iid = preg_replace('/\\]/',"",$iid);;
			$item[0] = $arr[0][0];
			$item[1] = $iid;
			$item[2] = "image";
		}
	}
	if ( count($item) < 3 )
		return false;
	return $item;
}

/**
 * 主调程序
 */ 
function taobaopress_default($content) {
	if ( preg_match('/\\[taobaopress-shop\\]/',$content) ) {
		include_once( 'display/shop.php' );
		if (function_exists('display_page')) {
			try {
				$result = display_page();
				$content = preg_replace('/\\[taobaopress-shop\\]/',$result,$content);
			}
			catch ( Exception $e) {
			}
		}
	}
	else if ( preg_match('/\\[taobaopress-item\\]/',$content) ) {
		include_once( 'display/content.php' );
		if (function_exists('display_page')) {
			try {
				$result = display_page(null,true);
				$content = preg_replace('/\\[taobaopress-item\\]/',$result,$content);
			}
			catch ( Exception $e) {
			}
		}
	}
	else {
		$arr = getItemId($content);
		if ( $arr != false ) {
			$key = $arr[0];
			$iid = $arr[1];
			$type = $arr[2];
			if ( $type == 'image' ) {
				include_once( 'display/image.php' );
				if (function_exists('display_page')) {
					$result = display_page($iid);
				}
			}
			else if ( is_single() || is_page() ) {
				include_once( 'display/content.php' );
				if (function_exists('display_page')) {
					$result = display_page($iid);
				}
			}
			else {
				include_once( 'display/excerpt.php' );
				if (function_exists('display_page')) {
					$result = display_page($iid);
				}
			}
			$key = preg_replace('/\[/','\\[',$key);
			$key = preg_replace('/\]/','\\]',$key);
			$content = preg_replace('/'.$key.'/',$result,$content);
		}
	}
	
	return $content;
}
?>