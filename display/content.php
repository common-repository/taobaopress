<?php 

/**
 * item
 * Enter description here ...
 */
function display_page( $iid = null, $display_desc = true ) {
	require TAOBAOPRESS_PATH . 'taoapi/function.php';
	
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
	$cat_name = "所有分类"; 
	$result_subcats_count = 0; 
	if($catid != 0) { 
		$Taoapi->method = 'taobao.itemcats.get'; 
		$Taoapi->fields = 'cid,name,parent_cid,status'; 
		$Taoapi->cids = $catid; 
		$TaoapiCat = $Taoapi->Send('get','xml')->getArrayData(); 
		$result_cat = $TaoapiCat["item_cats"]['item_cat']; 
		$cat_name = $result_cat['name']; 
		$parent_cid = $result_cat['parent_cid']; 
	} 
	if(is_array($click_url)){
		$click_url = ""; 
	} 
	if(is_array($shop_click_url)){ 
		$shop_click_url = ""; 
	}
	

	$props = $props;
	$Taoapi->method = 'taobao.itempropvalues.get'; 
	$Taoapi->fields = 'prop_name,name,status,sort_order'; 
	$Taoapi->cid = $catid;
	// FIXME:数组到 key:value,key:value
	$Taoapi->pvs = $props;
	$itempropvalues = $Taoapi->Send('get','xml')->getArrayData(); 
	$props = $itempropvalues['prop_values']['prop_value'];

	// 相关
	if ( $display_desc ) {
		$Taoapi->method = 'taobao.taobaoke.items.get'; 
		$Taoapi->fields = 'iid,num_iid,title,nick,pic_url,price,click_url,shop_click_url,seller_credit_score,commission,commission_rate,commission_num,commission_volume'; 
		$Taoapi->nick = "bocboy";
		$Taoapi->cid = $catid;
		$Taoapi->page_no = 1; 
		$Taoapi->page_size = '9'; 
		$Taoapi->sort = "commissionNum_desc"; 
		$Taoapi->start_price = 1; 
		$Taoapi->end_price = 10000; 
		$Taoapi->start_credit = $shoplevelstart; 
		$Taoapi->end_credit = $shoplevelend; 
		$Taoapi->start_commissionRate = $stratmoneyKeys; 
		$Taoapi->end_commissionRate = $endmoneyKeys; 
		$TaoapiItems = $Taoapi->Send('get','xml')->getArrayData(); 
		$taobaokeItem = $TaoapiItems["taobaoke_items"]["taobaoke_item"];;
	}
	// 非淘宝客
	if ( empty($taobaoke_nick) ||  $nick == $taobaoke_nick ) {
		$click_url = "http://item.taobao.com/item.htm?id=".$iid; 
	}
	
	$templateurl = get_theme_root_uri() . "/taobaopress";
	
	$html = '
<div class="clear"></div>
<div id="shop_content">
	<div class="taodi_tips">
		<div class="viewweizhi">'.$title.'</div>
	</div>
	<div class="titemsbox">
		<div class="titemsbox_l" style="overflow:hidden">
			<a href="'.$click_url.'" target="_blank">
				<img alt="'.$title.'" src="'.$pic_path.'_310x310.jpg">
			</a>
		</div>
		<div class="titemsbox_r">
			<ul>
				<li class="price">淘 宝 价：<b>'.$price.'</b>元 </li>
				<li>掌柜名称：'.$nick.'</li>
				<li style="height:35px">卖家信用：<img src="'.$templateurl.'/level_'.$level.'.gif">
					<a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid='.$nick.'&site=cntaobao&s=1&charset=utf-8" class="ww">
						<img alt="点击这里给我发消息" src="http://amos.im.alisoft.com/online.aw?v=2&uid='.$nick.'&site=cntaobao&s=1&charset=utf-8" alt="'.$nick.'" align="absmiddle">
					</a>
				</li>
				<li>所在地区：'.$state.''.$city.'</li>
				<li>宝贝运费：<span>平邮：'.$post_fee.'元</span><span>快递：'.$express_fee.'元</span><span>EMS：'.$ems_fee.'元</span></li>
				<li>上架时间：'.$list_time.'</li>
				<li>下架时间：'.$delist_time.'</li>
			</ul>
			<div>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5569200673611833";
/* 商品显示区域 */
google_ad_slot = "7167878721";
google_ad_width = 200;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
			</div>
			<div class="go_buy">
				<a href="'.$click_url.'" target="_blank">
					<img src="'.$templateurl.'/buy.gif">
				</a>
				<a href="'.$shop_click_url.'" target="_blank">
					<img src="'.$templateurl.'/shop.gif">
				</a>
			</div>
		</div>
		<div class="clear"></div>
	</div>';
	// 属性
	if ( $display_desc ) {
		$html .= '
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5569200673611833";
/* 妈妈折 */
google_ad_slot = "5487552378";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	<div class="titems_info">
		<div class="ti_box">
			<div class="ti">
				<h1>商品详细信息-'.$title.'</h1>
			</div>
		</div>
		<!-- 商品详细参数开始 -->
		<div class="titems_xxinfo">
			<ul>';
		if(isset($props['prop_name'])){
			$html .= '<li>'.$props['prop_name'].':'.$props['name'].'</li>';
		}
		else if(is_array($props)){
			for($i = 0; $i < count($props); $i++) {
				$html .= '<li>'.$props[$i]['prop_name'].':'.$props[$i]['name'].'</li>';
			}
		}
		$html .= '
			</ul>
			<div class="clear"></div>
		</div>';
		// 描述
	    $pat = "/<(\/?)(script|i?frame|style|html|body|title|link|a|meta|\?|\%)([^>]*?)>/isU";
	    $descclear = preg_replace($pat,"",$desc);
	
		$str = explode(",",$arrays["产品关键字替换"]["keyword"]);
		foreach($str as $val){
			$descclear = str_replace($val,"<a href=".$val."><b>".$val."</b></a>",$descclear);
		} 
		
		$html .= '<div id="tb_content">
			'.$descclear.'
		</div>';
		// 相关热销产品
		$html .= '
		<div>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5569200673611833";
/* 商品横向 */
google_ad_slot = "9996129610";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
		</div>
		<div class="ti_box">
			<div class="ti"><h1>相关热销-'.$cat_name.'-商品排行榜</h1></div>
		</div>
		<div class="taodi_listbox">
			<dl>';
		for($i = 0; $i < count($taobaokeItem); $i++) {
			$taousernick = $taobaokeItem[$i]["nick"];
			$urlview="http://www.taobaopress.com/shop/item/?iid=" . $taobaokeItem[$i]['num_iid'];
			$pageview ="";
	
			$picurl = "";
			
			$html .= '
    			<dt>
    				<em>
						<a href="'.$taobaokeItem[$i]["click_url"].'" target="_blank">
							<img alt="'.$taobaokeItem[$i]["title"].'" src="'.$taobaokeItem[$i]["pic_url"].'_b.jpg">
						</a>
    				</em>
    				<p class="taodi_title">
						<a href="'.$taobaokeItem[$i]["click_url"].'" target="_blank">
							'.$taobaokeItem[$i]["title"].'
						</a>
    				</p>
    				<p class="taodi_nick">
    					<a href="'.$taobaokeItem[$i]["shop_click_url"].'" target="_blank">
    						<img src="'.$templateurl.'/'.$style.'/toshop.gif" align="absmiddle"/>
    					</a>
    					<b>￥'.$taobaokeItem[$i]["price"].'元</b>
    				</p>
    				<p class="taodi_sales">已售：<b>'.$taobaokeItem[$i]["commission_num"].'</b>件</p>
    				<p>掌柜：'.$taobaokeItem[$i]["nick"].'</p>
    				<p>卖家信用：<img src="'.$templateurl.'/level_'.$taobaokeItem[$i]["seller_credit_score"].'.gif"></p>
    			</dt>';
		}
		$html .='
			</dl>
		</div>';
	$html .='
	</div>';
	}
	$html .='
</div>';
		
	return $html;
}