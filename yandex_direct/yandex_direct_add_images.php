<?
	$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/..");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	
	include(__DIR__."/yandex_direct_api_json.php");
	
	CModule::IncludeModule("iblock");
	
	$FLog = __DIR__."/yandex_direct_add_images.log";
	$FError = __DIR__."/yandex_direct_add_images.error";
	
	$NLog = array(); 
	$Log = array("SIZE" => 3,"PAGE" => 1, "CPAGE" => 0);
	
	if(file_exists($FLog)){
		$NLog = json_decode(file_get_contents($FLog),true);
		if(isset($NLog["PAGE"]) && isset($NLog["CPAGE"])){
			if($NLog["PAGE"] > 0 && $NLog["CPAGE"] > 0){
				if($NLog["CPAGE"]>=$NLog["PAGE"]){
					$Log = $NLog;
				} else {
					echo "stat";
					die();
				}
			}
		}
	}
	
	echo "<pre>";
		print_r($Log);
	echo "</pre>";
	
	$Ydaj = new YandexDirectApiJson();
	$Query = CIBlockElement::GetList(
		array(), 
		array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => 8,
			">DETAIL_PICTURE" => 0,
			"=PROPERTY_YANDEX_DIRECT_IMAGE_HASHES" => false,
		), 
		false,
		array("nPageSize" => $Log["SIZE"],"iNumPage" => $Log["PAGE"]),
		array("ID","NAME","DETAIL_PICTURE","PROPERTY_YANDEX_DIRECT_IMAGE_HASHES")
	);
	
	if($Log["CPAGE"] == 0){
		$Log["CPAGE"] = ceil($Query->SelectedRowsCount()/$Log["SIZE"]);
	}
	
	while($Answer = $Query->Fetch()){
		$ImageHash = "";
		$TErrro = "";
		if($Answer["PROPERTY_YANDEX_DIRECT_IMAGE_HASHES_VALUE"] == "" && $Answer["DETAIL_PICTURE"] > 0){
			$Answer["DETAIL_PICTURE"] = CFile::GetFileArray($Answer["DETAIL_PICTURE"]);
			
			$Size = array("width"=>960, "height"=>640);
			if($Answer["DETAIL_PICTURE"]["HEIGHT"] > $Answer["DETAIL_PICTURE"]["WIDTH"]){
				$Size = array("width"=>640, "height"=>960);
			}
			
			$Answer["DETAIL_PICTURE_RESIZE"] = CFile::ResizeImageGet($Answer["DETAIL_PICTURE"], $Size, BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
			
			if($Answer["DETAIL_PICTURE_RESIZE"]["src"] != ""){
				$Data = $Ydaj->AdImagesAdd(array(
					array(
						"PATH" => $_SERVER["DOCUMENT_ROOT"].$Answer["DETAIL_PICTURE_RESIZE"]["src"],
						"NAME" => $Answer["DETAIL_PICTURE"]["FILE_NAME"]
					)
				));
				
				if(isset($Data["result"]["AddResults"][0]["AdImageHash"]) && $Data["result"]["AddResults"][0]["AdImageHash"] != ""){
					$ImageHash = $Data["result"]["AddResults"][0]["AdImageHash"];
					CIBlockElement::SetPropertyValuesEx($Answer["ID"], false, array("YANDEX_DIRECT_IMAGE_HASHES" => $ImageHash));
					echo "Товар: ".$Answer["ID"]." yandex direct Img hash: ".$ImageHash."<br />";
				} else {
					$TErrro = "BITRIX ID: ".$Answer["ID"]." Ошибка Yandex Direct: ".json_encode($Data)."\r\n";
					file_put_contents($FError,$TErrro,FILE_APPEND);
				}
			}
		}
	}
	
	$Log["PAGE"] += 1;
	file_put_contents($FLog,json_encode($Log));
?>