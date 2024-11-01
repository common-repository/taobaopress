<?php 
require_once 'Taoapi.php';

$setCacheTime="0";  //缓存时间

$Taoapi_Config = Taoapi_Config::Init();
//$Taoapi_Config->setCharset('UTF-8');
//$Taoapi_Config->setTestMode(false) ->setVersion(2) 
//->setCloseError(true) 
//->setErrorlog(true)
//->setCache(6);
// ->setAppKey("12004702")  
// ->setAppSecret("08a096729647d3db476bcddfe06dbc97");

$Taoapi = new Taoapi;
$Taoapi->Cache->setCacheTime($setCacheTime);

$nick = tao_var_get('taobaopress_nick','');
if ( empty($nick) ) {
	$nick="伶木彗子";
	$taobaoke_nick='bocboy';
}
else {
	$taobaoke_nick=tao_var_get('taobaopress_taobaoke',$nick);
}
if ( empty( $taobaoke_nick ) ) 
	$taobaoke_nick = $nick;
	
// 热卖商品分类名称
$taobao_cat_name='淘博客热卖';

?>