<?php 

/**
 * 同步商品
 * Enter description here ...
 */
function display_page() {
	global $user_ID;
	
	include_once( TAOBAOPRESS_PATH. 'taoapi/function.php' );

	$current_date=date('Y-m-d');
	$taobao_nick=tao_var_get('taobaopress_nick','');
	$taobao_sync_date=tao_var_get('taobaopess_sync_date','');
	$taobaopress_message = '';
	
	if ( empty( $taobao_nick)) {
		$taobaopress_message = '未设置淘宝会员名';
	}
	else {
		// 用户
		$Taoapi->method = 'taobao.user.get'; 
		$Taoapi->fields = 'user_id,nick,seller_credit'; 
		$Taoapi->nick = $taobao_nick;
		$TaoapiResult=$Taoapi->Send('get','xml');
		$resultUser = $TaoapiResult->getArrayData(); 
		$taobaoUser = $resultUser['user'];

		// 店铺
		$Taoapi->method = 'taobao.shop.get'; 
		$Taoapi->fields = 'sid,cid,nick,title,pic_path,created,modified'; 
		$Taoapi->nick = $taobao_nick;
		$TaoapiResult=$Taoapi->Send('get','xml');
		$resultShop = $TaoapiResult->getArrayData();
		$taobaoShop= $resultShop['shop'];

		// 所有商品
		$Taoapi->method = 'taobao.items.get'; 
		$Taoapi->fields = 'pic_url,iid,num_iid,title,nick,cid,price,type,delist_time,post_fee,volume'; 
		$Taoapi->nicks = $taobao_nick; 
		$Taoapi->page_no = 1; 
		$Taoapi->page_size = 1;
		
		$results = $Taoapi->Send('get','xml')->getArrayData(); 
		$taobaokeItem = $results['items']['item']; 
		$totalResults = $results['total_results'];
	}
	
	if ( $current_date == $taobao_sync_date) {
		$taobaopress_message = '每天只能同步一次商品';
	}
	else if ( !empty($_POST['Taobaopress-Sync']) && $totalResults > 0 ) {
		// 同步200个商品
		$Taoapi->method = 'taobao.items.get'; 
		$Taoapi->fields = 'pic_url,iid,num_iid,title,nick,cid,price,type,delist_time,post_fee,volume'; 
		$Taoapi->nicks = $taobao_nick; 
		$Taoapi->page_no = 1; 
		if ( $totalResults >= 400 )
			$Taoapi->page_size = 20;
		else 
			$Taoapi->page_size = 10;
		$Taoapi->order_by = "list_time:desc";
		
		$results = $Taoapi->Send('get','xml')->getArrayData(); 
		$taobaokeItem = $results['items']['item'];
		$size = count($taobaokeItem);
		$count=0;

		// 创建分类目录 
		$cat_id = 0;
		if ( $cat_id = category_exists($taobao_cat_name, 0) ) {
		}
		else {
			$cat_id = wp_insert_category( array('cat_name' => $taobao_cat_name, 'category_parent' => 0, 'category_nicename' => 'taobao') );
		}

		$post_category = array($cat_id);

		for($i = 0; $i < $size; $i++) {
			$iid = $taobaokeItem[$i]['num_iid'];
			$title = $taobaokeItem[$i]['title'];
			$html = '[id='.$iid.']';
			$html .= '<a href="http://item.taobao.com/item.htm?id='.$iid.'" target="_blank">'.$title.'</a>';
			$postarr = array('post_status' => 'publish', 'post_type' => 'post', 'post_author' => $user_ID,
			'post_content' => $html, 'post_title' => $title, 'post_category' => $post_category);
			
			try {
				wp_insert_post($postarr);
				$count++;
			}
			catch ( Except $e ) {
				;
			}
		}
		$taobaopress_message = '一共同步了'.$count.'个商品';
		tao_var_set('taobaopess_sync_date',date('Y-m-d'));
	}
?>
<div>
	<h2>
		同步商品
	</h2>
	<p>
			<?php echo $taobaopress_message; ?>
	</p>
	<form method="post">
		<table class="form-table">
	    <tbody>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label>店铺名称</label>
	    		</th>
	    		<td>
	    			<?php echo $taobaoShop['title']; ?>
				</td>
			</tr>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label>掌柜名称</label>
	    		</th>
	    		<td>
	    			<?php echo $taobaoUser['nick']; ?>
				</td>
			</tr>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label>商品数量</label>
	    		</th>
	    		<td>
	    			<?php echo $totalResults; ?>
				</td>
			</tr>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label>上次同步日期</label>
	    		</th>
	    		<td>
	    			<b><?php echo $taobao_sync_date;?></b>
				</td>
			</tr>
	    	<tr valign="top">
	    		<th scope="row">
	    			<label>说明</label>
	    		</th>
	    		<td>
	    			<b>每次仅同步最新上架的10个商品，建议不要操作过频繁!</b>
				</td>
			</tr>

			</tbody>
		</table>
		<p class="submit" style="margin-left:20px">
			<input type="submit" value="开始同步" class="button-primary" name="Taobaopress-Sync"/>
	  	</p>
	</form>
</div>
<?php } ?>
