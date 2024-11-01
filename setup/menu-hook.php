<?php
define ('TAOBAOPRESS_HOOK_MENU', 'taobaopress_dispatch_page');

function taobaopress_option_menu() {
    if (function_exists('add_options_page')) {
        add_menu_page('淘宝博客管理', '淘博客', 'administrator', 'taobaopress.php', 'taobaopress_dispatch_page');
        add_submenu_page('taobaopress.php', '淘宝店铺设置', '店铺设置', 'administrator', 'taobaopress-settings.php', TAOBAOPRESS_HOOK_MENU);
        add_submenu_page('taobaopress.php', '同步店铺商品', '同步商品', 'administrator', 'taobaopress-sync.php', TAOBAOPRESS_HOOK_MENU);
    }
}


function taobaopress_dispatch_page() {
    define('TAOBAOPRESS_SETUP_PATH', 'setup/');
    $page = empty($_GET['page']) ? 'taobaopress-settings.php' : $_GET['page'];

	if (file_exists(TAOBAOPRESS_PATH . TAOBAOPRESS_SETUP_PATH . $page)) {
		include_once (TAOBAOPRESS_PATH . TAOBAOPRESS_SETUP_PATH . $page);

        $vars = array();
        if (function_exists('display_page')) {
            $vars = display_page();//每个页面都需要定义该函数
        }
        else {
            echo '页面' . $page . '没有定义函数display_page';
        }
    }
    
}


?>
