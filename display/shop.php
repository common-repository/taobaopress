<?php 

/**
 * shop
 * Enter description here ...
 */
function display_page() {
	require TAOBAOPRESS_PATH . 'taoapi/function.php';
	require TAOBAOPRESS_PATH . 'taoapi/pages.php';
	
	$page=var_request('page',1);
	$pagenumKeys=30;
	$keyword=var_request('keyword','');
	
	// 用户
	$Taoapi->method = 'taobao.user.get'; 
	$Taoapi->fields = 'user_id,nick,seller_credit'; 
	$Taoapi->nick = $nick;
	$TaoapiResult=$Taoapi->Send('get','xml');
	$resultUser = $TaoapiResult->getArrayData(); 
	$taobaoUser = $resultUser['user'];

	// 店铺
	$Taoapi->method = 'taobao.shop.get'; 
	$Taoapi->fields = 'sid,cid,nick,title,pic_path,created,modified'; 
	$Taoapi->nick = $nick;
	$TaoapiResult=$Taoapi->Send('get','xml');
	$resultShop = $TaoapiResult->getArrayData();
	$taobaoShop= $resultShop['shop'];
	
	// 所有商品
	$Taoapi->method = 'taobao.items.get'; 
	$Taoapi->fields = 'pic_url,iid,num_iid,title,nick,cid,price,type,delist_time,post_fee,volume'; 
	$Taoapi->nicks = $nick; 
	$Taoapi->page_no = $page; 
	$Taoapi->page_size = $pagenumKeys;
	$Taoapi->order_by = "volume:desc"; 
	$Taoapi->start_price = "1"; 
	$Taoapi->end_price = "100000"; 
	
	$results = $Taoapi->Send('get','xml')->getArrayData(); 
	$taobaokeItem = $results['items']['item']; 
	$totalResults = $results['total_results'];

	// 分页数据
	$page_size=$pagenumKeys;
	$nums=$totalResults;
	$sub_pages=10;
	$pageCurrent=$page;
	$pageUrl=get_permalink() . "?keyword=" . $keyword . "&page=";
	$subPages=new SubPages($page_size,$nums,$pageCurrent,$sub_pages,$pageUrl,2);
	
	$html = '
<div class="clear"></div>
<div>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5569200673611833";
/* 店铺横向广告 */
google_ad_slot = "9212205659";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
<div id="shop_content">
	<div class="taodi_listbox1">
		<dl>';
	for($i = 0; $i < count($taobaokeItem); $i++) {
		$urlview= get_permalink() . "item/?iid=" . $taobaokeItem[$i]['num_iid'];
		$html .= '
			<dt>
				<em>
					<a href="'.$urlview.'" target="_blank">
						<img alt="'.$taobaokeItem[$i]["title"].'" src="'.$taobaokeItem[$i]["pic_url"].'_b.jpg">
					</a>
				</em>
				<p class="taodi_title">
					<a href="'.$urlview.'" target="_blank">
						'.$taobaokeItem[$i]["title"].'
					</a>
				</p>
				<p class="taodi_nick">
					<b>宝贝价格：￥'.$taobaokeItem[$i]["price"].'元</b>
				</p>
				<p class="taodi_nick">
					30天销量：'.$taobaokeItem[$i]["volume"].'
				</p>
			</dt>
		';
	}
	$html .= '
		</dl>
	</div>
	<div>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-5569200673611833";
/* 横向文字广告2 */
google_ad_slot = "5288047087";
google_ad_width = 468;
google_ad_height = 15;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	</div>
	<div id="pages">
	' . $subPages->subPageCss2() . '
	</div>
</div>
<div class="clear"></div>';
	
	return $html;
}
?>