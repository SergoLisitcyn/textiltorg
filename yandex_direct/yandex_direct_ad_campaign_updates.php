<?class YandexDirectAdCampaignUpdates {
	
	private $MStep = 2;
	
	private $Files = null;
	private $Dirs = null;
	private $BXIBId = 8;
	
	private $IDCampaign = 0;
	private $Log = array();
	private $BXESend = array();
	private $BXCGIds = array();
	
	private $MElms = 200;
	
	function __construct(){
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
	}
	
	public function Init($IDCampaign = 0,$BXCGIds = array(),$BXESend = array()){
		if($this->SetParams($IDCampaign,$BXCGIds,$BXESend)){
			$this->GetLog();
			if($this->Log["STEP"] == 2){
				$this->ESResult();
				++$this->Log["STEP"];
				$this->Dbg("End",false);
			} elseif($this->Log["STEP"] == 1){
				if($this->YandexDirectAdsUpdate()){
					++$this->Log["STEP"];
				}
				$this->Dbg("Update",false);
			} elseif($this->Log["STEP"] == 0){
				$this->YandexDirectAdsGet();
				$this->SetStatistic("",true);
				$this->SetErrors("",true);
				++$this->Log["STEP"];
				$this->Dbg("Start",false);
			}
			$this->SetLog();
		}
	}
	
	private function YandexDirectAdsUpdate(){
		$i = 0;
		$Data = $this->GetData();
		$AdsUpdate = array();
		foreach($Data as $key => $value){
			$AdsItem = array();
			$Product = array();
			$YPrice = 0;
			$Product = $this->BXGetProduct($value["TextAd"]["Title"],$value["TextAd"]["Href"]);
			if($Product["ID"] > 0){
				
				$AdsItem = array(
					"Id" => $value["Id"],
				);
				
				if($Product["PROPERTY_YANDEX_DIRECT_IMAGE_HASHES_VALUE"] != ""){
					if($value["TextAd"]["AdImageHash"] == ""){
						$AdsItem["TextAd"]["AdImageHash"] = $Product["PROPERTY_YANDEX_DIRECT_IMAGE_HASHES_VALUE"];
					} elseif($Product["PROPERTY_YANDEX_DIRECT_IMAGE_HASHES_VALUE"] != $value["TextAd"]["AdImageHash"]){
						$AdsItem["TextAd"]["AdImageHash"] = $Product["PROPERTY_YANDEX_DIRECT_IMAGE_HASHES_VALUE"];
					}
				}
				
				
				if($Product["ACTIVE"] == "N"){
					$this->AddStatistic($value["Id"],$value["TextAd"]["Title"],"Товар не активный.");
				}
				if(!empty($Product["PRICE"])){
					if(!$Product["IS_DEF_PRICE"]){
						$this->AddStatistic($value["Id"],$value["TextAd"]["Title"],"Тип цены у данного товара не дефолтный. Тип цены дополнительный: ".$Product["PRICE"]["CATALOG_GROUP_NAME"]);
					}
					$YPrice = intval($Product["PRICE"]["PRICE"]*1000000);
					if(empty($value["TextAd"]["PriceExtension"]) || $YPrice != $value["TextAd"]["PriceExtension"]["Price"]){
						$AdsItem["TextAd"]["PriceExtension"] = array(
							"Price" => $YPrice, 
		    				"PriceCurrency" => $Product["PRICE"]["CURRENCY"]
						);
					}
				} else {
					$this->AddStatistic($value["Id"],$value["TextAd"]["Title"],"Не найдена цена для товара.");
				}
			} else {
				$this->AddStatistic($value["Id"],$value["TextAd"]["Title"],"Товар не найден.");
			}
			
			if(isset($AdsItem["TextAd"])){
				$this->Dbg($AdsItem,false);
				$AdsUpdate[] = $AdsItem;
			}
			
			unset($Data[$key]);
			++$i;
			if($i == $this->MElms){
				break;
			}
		}	
		
		if(!empty($AdsUpdate)){
			$Ydaj = new YandexDirectApiJson();
			$Data = $Ydaj->AdsUpdate($AdsUpdate);
			if(isset($Data["error"])){
				$this->SetErrors(json_encode($Data["error"]));
			} 	
		}
		$this->SetData($Data);
		return empty($Data);
	}
	
	private function AddStatistic($Id = 0,$Name = "",$Comment = ""){
		if($Id > 0 && $Name != ""){
			$this->SetStatistic("Объявление: ".$Id.", Название: ".$Name.", Комментарий: ".$Comment);
		}
	}
	
	private function ESResult(){
		$Fields = array("ID_CAMPAIGN" => "","STATISTIC"=>"","ERRORS" => "");
		$Statistic = trim($this->GetStatistic());
		$Errors = trim($this->GetErrors());
		if($this->BXESend["EVENT"] != "" && $this->BXESend["LID"] != "" && ($Statistic != "" || $Errors != "")){
			$Fields["ID_CAMPAIGN"] = $this->IDCampaign;
			$Fields["STATISTIC"] = $Statistic;
			$Fields["ERRORS"] = $Errors;
			CEvent::Send($this->BXESend["EVENT"], $this->BXESend["LID"], $Fields);
		}
	}
	
	private function SetErrors($Data = "",$Flag = false){
		if($Data != "" || $Flag){
			if($Flag){
				file_put_contents($this->Files->Errors->Absolute,"");
			} else {
				file_put_contents($this->Files->Errors->Absolute,$Data."\r\n",FILE_APPEND);
			}
		}
	}
	
	private function GetErrors(){
		$Result = "";
		if(file_exists($this->Files->Errors->Absolute)){
			$Result = file_get_contents($this->Files->Errors->Absolute);
		}
		return $Result;
	}
	
	private function SetStatistic($Data = "",$Flag = false){
		if($Data != "" || $Flag){
			if($Flag){
				file_put_contents($this->Files->Statistic->Absolute,"");
			} else {
				file_put_contents($this->Files->Statistic->Absolute,$Data."\r\n",FILE_APPEND);
			}
		}
	}
	
	private function GetStatistic(){
		$Result = "";
		if(file_exists($this->Files->Statistic->Absolute)){
			$Result = file_get_contents($this->Files->Statistic->Absolute);
		}
		return $Result;
	}
	
	private function BXGetProduct($Name = "",$Link = ""){
		$Result = array("ID" => 0);
		$Filter = array();
		
		if($Link != ""){
			$Filter["CODE"] = $this->GetCodeFromLink($Link);
		} elseif($Name != ""){
			$Filter["NAME"] = "%".$Name."%";
		}
		
		if(!empty($Filter)){
			$Filter["IBLOCK_ID"] = $this->BXIBId;
			
			
			$Query = CIBlockElement::GetList(array(), $Filter, false,array("nTopCount" => 1),array("ID","ACTIVE","NAME","DETAIL_PICTURE","PROPERTY_YANDEX_DIRECT_IMAGE_HASHES"));
			if($Answer = $Query->Fetch()){
				$Result = $Answer;
			}
			
			if($Result["ID"] > 0){
				$Result["PRICES"] = array();
				$Result["PRICE"] = array();
				$Result["IS_DEF_PRICE"] = false;
				$Query = CPrice::GetList(array(),array("PRODUCT_ID" => $Result["ID"],"CATALOG_GROUP_ID" => $this->BXCGIds["ALL"]));
				while($Answer = $Query->Fetch()){
					$Result["PRICES"][] = $Answer;
					if($Answer["CATALOG_GROUP_ID"] == $this->BXCGIds["DEF"]){
						$Result["IS_DEF_PRICE"] = true; 
						$Result["PRICE"] = $Answer;
					}
				}
				if(empty($Result["PRICE"]) && !empty($Result["PRICES"])){
					$Result["PRICE"] = $Result["PRICES"][0];
				}
			}
		}
		
		return $Result;
	}
	
	private function GetCodeFromLink($Link = ""){
		$Result = "";
		$Codes = explode("/",str_replace(".html","",parse_url($Link,PHP_URL_PATH)));
		$CCodes = count($Codes);
		$Result = $Codes[$CCodes-1];
		return $Result;
	}
	
	private function YandexDirectAdsGet(){
		$Ydaj = new YandexDirectApiJson();
		$Data = $Ydaj->AdsGet(
			array("CampaignIds" => array($this->IDCampaign),"States" => array("ON"),"Statuses" => array("ACCEPTED")),
			array("Id","CampaignId"),
			array("Title","Href","AdImageHash"),
			array("Price","PriceCurrency")
		);
		
		if(isset($Data["result"]["Ads"]) && !empty($Data["result"]["Ads"])){
			$this->SetData($Data["result"]["Ads"]);
		} else {
			$this->Dbg($Data);
		}	
	}
	
	private function GetData(){
		$Result = array();
		if(file_exists($this->Files->Data->Absolute)){
			$Result = json_decode(file_get_contents($this->Files->Data->Absolute),true);
		}
		return $Result;
	}
	private function SetData($Data = array()){file_put_contents($this->Files->Data->Absolute,json_encode($Data));}
	
	private function SetParams($IDCampaign = 0,$BXCGIds = array(),$BXESend = array()){
		$Result = false;
		if($IDCampaign > 0){
			$this->IDCampaign = $IDCampaign;
			
			$this->Dirs->Report = __DIR__."/report_".$IDCampaign;
			if(!file_exists($this->Dirs->Report)){mkdir($this->Dirs->Report,0777);}
			$this->Files->Log->Name = "ad_campaign_log.json";
			$this->Files->Log->Absolute = $this->Dirs->Report."/".$this->Files->Log->Name;
			
			$this->Files->Data->Name = "ad_campaign_data.json";
			$this->Files->Data->Absolute = $this->Dirs->Report."/".$this->Files->Data->Name;
			
			if(isset($BXCGIds["DEF"]) && $BXCGIds["DEF"] > 0){
				$this->BXCGIds = $BXCGIds;
				$this->BXCGIds["ALL"] = array();
				$this->BXCGIds["ALL"][] = $BXCGIds["DEF"];
				if(isset($BXCGIds["ADDITIONAL"]) && !empty($BXCGIds["ADDITIONAL"])){
					$this->BXCGIds["ALL"] = array_merge($this->BXCGIds["ALL"],$BXCGIds["ADDITIONAL"]);
				}
				$Result = true;
			}	
		}
		
		$this->BXESend = $BXESend;
		$this->Files->Statistic->Name = "ad_campaign_statistic.txt";
		$this->Files->Statistic->Absolute = $this->Dirs->Report."/".$this->Files->Statistic->Name;
		
		$this->Files->Errors->Name = "ad_campaign_errors.txt";
		$this->Files->Errors->Absolute = $this->Dirs->Report."/".$this->Files->Error->Name;
		
		return $Result;
	}
	
	private function GetLog(){
		$Flag = true;
		$Log = array();
		if(file_exists($this->Files->Log->Absolute)){
			$Log = json_decode(file_get_contents($this->Files->Log->Absolute),true);
			if(isset($Log["STEP"]) && $Log["STEP"] > 0){
				if($this->MStep >= $Log["STEP"]){
					$this->Log = $Log;
					$Flag = false;
				}
			}
		}
		if($Flag){
			$this->Log = $this->DefLog();
		}	
	}
	
	private function SetLog(){file_put_contents($this->Files->Log->Absolute,json_encode($this->Log));}
	private function DefLog(){return array("STEP" => 0);}
	private function Dbg($Data,$Flag = true){echo "<pre>";print_r($Data);echo "</pre>";if($Flag){die();}}
	
}?>