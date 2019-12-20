<?
$json = file_get_contents('https://pecom.ru/ru/calc/towns.php');
$regions = json_decode($json);
echo "<pre>"; print_r($regions); echo "</pre>";
echo "<pre>"; print_r('test'); echo "</pre>";
?>