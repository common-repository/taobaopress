<?php 

/**
 * 淘宝博客后台设置
 * Enter description here ...
 */
function display_page() {
	$taobao_nick=tao_var_get('taobaopress_nick',TAOBAOPRESS_NICK);
	$taobaoke_nick=tao_var_get('taobaopress_taobaoke',$taobao_nick);
	$taobao_url=tao_var_get('taobaopress_url',TAOBAOPRESS_URL);
?>
<div>
	<h2>
		淘宝店铺信息
	</h2>
	<ul>
		<li>掌柜昵称:<?php echo $taobao_nick; ?></li>
<?php 
if ( $taobao_nick != $taobaoke_nick ) {
?>
		<li>淘宝客:<?php echo $taobaoke_nick; ?></li>
<?php 
}
?>
		<li>店铺网址:<a href="<?php echo $taobao_url; ?>" target="_blank"><?php echo $taobao_url; ?></a></li>
	</ul>
</div>
<?php 
	return "";
} ?>