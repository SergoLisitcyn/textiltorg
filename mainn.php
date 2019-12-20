<?

$var=array();
$f=$_GET["secFir"];
$_GET["secFir"]='';
foreach($_GET as $key => $item){
	if(!empty($item)){
		$var[]=strval($key);
		$var[]='=';
		$var[]=strval($item);
		$var[]='&';
	}
}
$var=implode($var);
header('Location: http://www.textiletorg.ru/'.$f.'?'.$var);
?>