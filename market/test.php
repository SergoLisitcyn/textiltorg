<?php

function cuter($name){
    if(substr($name, -3) == ' во'){
        return substr($name, 0, -3);
    }else{
        return $name;
    }
}

$test = '123456789 в';

echo $test . '<br>';
echo cuter($test) . '<br>';

$arResult = array(
    "item-1" => array(
        "name" => '1111',
        "price" => 123,
    ),
    "item-2" => array(
        "name" => '1111',
        "price" => 1111,
    ),
    "item-3" => array(
        "name" => '1121',
        "price" => 1111,
    ),
);

foreach($arResult as $item){
    foreach($arResult as $check){
        if($item["name"] == $check["name"] . ' во'){
            $item["price"] = $check["price"];
            $check = '';
        }
    }
print_r($arResult);
}
        
?>