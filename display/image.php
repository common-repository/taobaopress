<?php 

/**
 * item
 * Enter description here ...
 */
function display_page( $iid = null, $display_desc = false ) {
	require TAOBAOPRESS_PATH . 'taoapi/function.php';
	
	if ( !isset($iid) ) {
		$iid = $_GET['iid'];
	}

	// 取推广量最大的一个商品
	if ( !isset($iid) || $iid == '0000000000' ) {
		$Taoapi->method = 'taobao.taobaoke.items.get'; 
		$Taoapi->fields = 'iid,num_iid,title,nick,pic_url,price,click_url,shop_click_url,seller_credit_score,commission,commission_rate,commission_num,commission_volume'; 
		$Taoapi->nick = "bocboy";
		$Taoapi->cid = '16';
		$Taoapi->page_no = 1; 
		$Taoapi->page_size = '9'; 
		$Taoapi->sort = "commissionNum_desc"; 
		$Taoapi->start_price = 100; 
		$Taoapi->end_price = 10000; 
		$Taoapi->start_credit = $shoplevelstart; 
		$Taoapi->end_credit = $shoplevelend; 
		$Taoapi->start_commissionRate = $stratmoneyKeys; 
		$Taoapi->end_commissionRate = $endmoneyKeys; 
		$TaoapiItems = $Taoapi->Send('get','xml')->getArrayData(); 
		$taobaokeItem = $TaoapiItems["taobaoke_items"]["taobaoke_item"];;
		$iid = $taobaokeItem[0]['num_iid'];
	}
	$Taoapi->method = 'taobao.taobaoke.items.detail.get'; 
	$Taoapi->fields = 'desc,auction_point,stuff_status,has_invoice,has_warranty,has_showcase,property_alias,input_pids,input_str,type,seller_cids,props,input_pids,cid,title,post_fee,express_fee,ems_fee,location,click_url,shop_click_url,seller_credit_score,nick,pic_url,price,cid,num,list_time,delist_time,location,props_name'; 
	$Taoapi->num_iids = $iid; 
	$Taoapi->nick = $taobaoke_nick;
	$TaoapiItem = $Taoapi->Send('get','xml')->getArrayData(); 
	$taobaoke_items = $TaoapiItem["taobaoke_item_details"]["taobaoke_item_detail"]; 
	$result_item = $taobaoke_items["item"]; 
	$props = $result_item['props']; 
	$props_name = $result_item['props_name']; 
	$title = $result_item['title']; 
	$post_fee = $result_item['post_fee']; 
	$express_fee = $result_item['express_fee']; 
	$ems_fee = $result_item['ems_fee']; 
	$catid = $result_item['cid']; 
	$desc = $result_item['desc']; 
	$num = $result_item['num']; 
	$price = $result_item['price']; 
	$pic_path = $result_item['pic_url']; 
	$list_time = $result_item['list_time']; 
	$delist_time = $result_item['delist_time']; 
	$click_url = $taobaoke_items['click_url']; 
	$shop_click_url = $taobaoke_items['shop_click_url']; 
	$seller_credit_score = $taobaoke_items['seller_credit_score']; 
	$city = $result_item['location']['city']; 
	$state = $result_item['location']['state']; 
	$level = $seller_credit_score;
	 
	// 非淘宝客
	if ( empty($taobaoke_nick) ||  $nick == $taobaoke_nick ) {
		$click_url = "http://item.taobao.com/item.htm?id=".$iid; 
	}
	
	$urlview= get_permalink();
	
	$templateurl = get_theme_root_uri() . "/taobaopress";
	
	$html = '
<div>
	
<a href="'.$click_url.'" target="_blank">
				<img alt="'.$title.'" src="'.$pic_path.'_310x310.jpg">
</a><br>
<div style="width:310px"><a href="'.$click_url.'" target="_blank">'.$title.'</a></div>
</div>';
		
	return $html;
}