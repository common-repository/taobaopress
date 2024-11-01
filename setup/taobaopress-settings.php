<?php 

/**
 * 淘宝博客后台设置
 * Enter description here ...
 */
function display_page() {
	global $user_ID;
	include_once( TAOBAOPRESS_PATH. 'taoapi/function.php' );
	
	$taobaopress_message="";
	$taobao_nick=tao_var_get('taobaopress_nick','');
	$taobaoke_nick=tao_var_get('taobaopress_taobaoke','');
	
	$taobao_url=tao_var_get('taobaopress_url','');
	$taobao_appkey=tao_var_get('taobaopress_appkey','12168497');
	$taobao_appsecret=tao_var_get('taobaopress_appsecret','93b5d06199f68ee8790e89da464a9430');
	
	if (!empty($_POST['Taobaopress-Submit'])) {
	    $taobao_nick = trim($_POST['nick']);
	    $taobaoke_nick=trim($_POST['taobaoke']);
	    $taobao_url = trim($_POST['url']);
	    // $taobao_appkey = trim($_POST['appkey']);
	    // $taobao_appsecret = trim($_POST['appsecret']);
	    
	    // 检查是否有店铺
	    $hasShop=false;
	    if ( !empty ($taobao_nick) ) {
	    	try {
				// 店铺
				$Taoapi->method = 'taobao.shop.get'; 
				$Taoapi->fields = 'sid,cid,nick,title,pic_path,created,modified'; 
				$Taoapi->nick = $taobao_nick;
				$TaoapiResult=$Taoapi->Send('get','xml');
				$resultShop = $TaoapiResult->getArrayData();
				$taobaoShop= $resultShop['shop'];
				$taobao_url = 'http://shop'.$taobaoShop['sid'].'.taobao.com/';
				if ( !empty($taobaoShop)) {
					$hasShop = true;
					// 生成两个页面
					$page = get_page_by_path('/shop');
					$post_parent=0;
					// shop
					if ( null == $page ) {
						$post_content = '店铺名称:'. $taobaoShop['title'] . '<br>';
						$post_content .= "[taobaopress-shop]";
						$postarr = array('post_status' => 'publish', 'post_type' => 'page', 'post_author' => $user_ID,
						'post_content' => $post_content, 'post_title' => 'shop');
						$post_id = wp_insert_post($postarr);
						$post_parent=$post_id;
						
						$page = get_page($post_id);
						$page->post_title = '淘宝店铺';
						wp_update_post($page);
					}
					else {
						$post_parent=$page->post_id;
					}
					
					$page = get_page_by_path('/shop/item');
					if ( null == $page ) {
						$post_content = '店铺名称:'. $taobaoShop['title'] . '<br>';
						$post_content .= "[taobaopress-item]";
						$postarr = array('post_status' => 'publish', 'post_type' => 'page', 'post_author' => $user_ID,
						'post_parent' => $post_parent,
						'post_content' => $post_content, 'post_title' => 'item');
						$post_id = wp_insert_post($postarr);
												
						$page = get_page($post_id);
						$page->post_title = '淘宝商品';
						wp_update_post($page);
					}
					
					// 创建分类目录 
					$cat_id = 0;
					if ( $cat_id = category_exists($taobao_cat_name, 0) ) {
					}
					else {
						$cat_id = wp_insert_category( array('cat_name' => $taobao_cat_name, 'category_parent' => 0, 'category_nicename' => 'taobao') );
					}
				}
	    	}
	    	catch ( Exception $e ) {
	    		;
	    	}
	    }
		//  && isNotEmpty($taobao_appkey) && isNotEmpty($taobao_appsecret) 
	    if ( $hasShop == false ) {
	        $taobaopress_message = '该会员'.$taobao_nick.' 未在淘宝开店！';
	    }
	    else if ( isNotEmpty($taobao_nick)) {
	        //save the setting into database
			tao_var_set('taobaopress_nick', $taobao_nick);
			tao_var_set('taobaopress_taobaoke', $taobaoke_nick);
			tao_var_set('taobaopress_url', $taobao_url);
			tao_var_set('taobaopress_appkey', $taobao_appkey);
			tao_var_set('taobaopress_appsecret', $taobao_appsecret);
			tao_var_set('taobao_taobaoke', false);
			
			
	        $taobaopress_message = '更新成功，请同步商品!';
	    }
	    else {
	        $taobaopress_message = 'nick, appkey, appsecret 都不能为空！';
	    }

	}
?>
<div>
	<h2>
		淘宝店铺参数设置
	</h2>
	<p>
			<?php echo $taobaopress_message; ?>
	</p>
	<form method="post">
		<table class="form-table">
	    <tbody>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label for="pid">淘宝会员名</label>
	    		</th>
	    		<td>
	    			<input type="text" value="<?php _e($taobao_nick); ?>" name="nick" size="30"/>
	    			<br/>昵称格式：天下吴双
				</td>
			</tr>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label for="pid">淘宝客</label>
	    		</th>
	    		<td>
	    			<input type="text" value="<?php _e($taobaoke_nick); ?>" name="taobaoke" size="30"/>
	    			<br/>如果是店主建的博客，“淘宝客”字段不用填写.
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit" style="margin-left:20px">
			<input type="submit" value="保存更改" class="button-primary" name="Taobaopress-Submit"/>
	  	</p>
	</form>
</div>
<?php } ?>

<?php 

/**
			<tr valign="top">
				<th scope="row">
					<label for="appkey">淘宝开放平台的APP KEY</label>
				</th>
				<td>
					<input type="text" value="<?php _e($taobao_appkey); ?>" name="appkey" size="30"/>
					<br/>淘宝开放平台应用服务的APP KEY，可以保持默认.<br/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="appsecret">淘宝开放平台的APP SECRET</label>
				</th>
				<td>
					<input type="text" value="<?php _e($taobao_appsecret); ?>" name="appsecret" size="40"/>
					<br/>淘宝开放平台应用服务的APP SECRET，可以保持默认.<br/>
				</td>
			</tr>
**/

?>
