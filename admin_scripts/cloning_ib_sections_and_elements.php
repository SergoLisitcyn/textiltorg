<?
set_time_limit(5000);

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

global $USER;

class CloningIbSectionsAndElements {
	
	private $Files = null;
	private $Dirs = null;
	private $MStep = 4;
	
	private $Ib = 0;
	private $SWhere = 0;
	private $SWhat = array();
	private $SPrefix = array();
	private $EPrefix = array();
	
	private $SFilter = array();
	private $EFilter = array();
	
	private $Log = array();
	
	private $MUElements = 300;
	private $MUSections = 100;
	
	function __construct($Ib = 0,$SWhere = 0,$SWhat = array(),$SPrefix = array(),$EPrefix = array(),$SFilter = array(),$EFilter = array()) {
		$this->Ib = $Ib;
		$this->SWhere = $SWhere;
		$this->SWhat = $SWhat;
		$this->SPrefix = $SPrefix;
		$this->EPrefix = $EPrefix;
		$this->EFilter = $EFilter;
		$this->SFilter = $SFilter;
		
		$this->Dirs->Report->Name = "report_".$this->Ib."_".$this->SWhere."_".implode("",$SWhat);
		$this->Dirs->Report->Absolute = __DIR__."/".$this->Dirs->Report->Name;
		if(!file_exists($this->Dirs->Report->Absolute)){mkdir($this->Dirs->Report->Absolute,0777);}
		
		$this->Files->Log->Name = "cloning_ib_sections_and_elements_log.json";
		$this->Files->Log->Absolute = $this->Dirs->Report->Absolute."/".$this->Files->Log->Name;
		
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("sale");
	}
	
	public function Init(){
		$this->GetLog();

		dumpLog($this->Log["STEP"], 'STEP', 'clon.log');

		if($this->Log["STEP"] == 4){
			echo "End";
			$this->Dbg($this->Log);
		} else if($this->Log["STEP"] == 3){
			if($this->UpdateDataCloningElementsAndOffers()){
				++$this->Log["STEP"];
			}
			echo "Cloning Data Elements and Offers";
			$this->Dbg($this->Log,false);
		}elseif($this->Log["STEP"] == 2){
			$this->GetDataCloningElementsAndOffers();
			++$this->Log["STEP"];
			echo "Get Data Elements and Offers";
			$this->Dbg($this->Log,false);
		} elseif($this->Log["STEP"] == 1){
			if($this->UpdateDataCloningSections()){
				++$this->Log["STEP"];
			}
			echo "Cloning Data Sections";
			$this->Dbg($this->Log,false);
		} else {
			$this->GetDataCloningSections();
			echo "Get Data Sections";
			$this->Dbg($this->Log,false);
			++$this->Log["STEP"];
		}
		$this->SetLog();
	}
	
	private function UpdateDataCloningElementsAndOffers(){
		$Result = false;
		$Flag = false;
		$i = 0;
		$Item = array();
		
		$NElm = new CIBlockElement;
		foreach($this->Log["DATA"]["ELEMENTS"] as $key => $value){
		    dumpLog($value);

			$Flag = false;
			if(isset($this->Log["DATA"]["SECTIONS"][$key]) && $this->Log["DATA"]["SECTIONS"][$key] > 0){
				$Section = $this->Log["DATA"]["SECTIONS"][$key];
				foreach($value as $VKey => $VValue){
					if($VValue > 0){
						$Item = array();
						$Fields = array();
						$CId = 0;
						$Query = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->Ib,"ID" => $VValue), false);
						if($Answer = $Query->GetNextElement()){
							$Item = $Answer->GetFields();
							$Item["PROPERTIES"] = $Answer->GetProperties(array(),array("EMPTY" => "N"));
							$Item["PRICES"] = $this->GetElementPrices($Item["ID"]);
							$Item["CATALOG_PRODUCT"] = CCatalogProduct::GetByID($Item["ID"]);
							
							$Fields = $this->GetElementFields($Item,$Section);
							
							if(!empty($this->EPrefix)){
								foreach($this->EPrefix as $PKey => $PValue){
									if(isset($Item[$PKey])){
										$Item[$PKey] .= $PValue;
									}
								}
							}
							
							$Query = CIBlockElement::GetList(
								array(), 
								array("IBLOCK_ID" => $this->Ib,"IBLOCK_SECTION_ID" => $Section,"NAME" => $Item["NAME"],"CODE" => $Item["CODE"]),
								false,
								false,
								array("ID")
							);
							if($Answer = $Query->Fetch()){
								$CId = $Answer["ID"];
                                dumpLog($CId, 'UpdateDataCloningElementsAndOffers $CId Answer', 'clon.log');
							} else {
								$Fields = $this->GetElementFields($Item,$Section);
								/*
								if(isset($Fields["PROPERTY_VALUES"][11])){
									$CId = $NElm->Add($Fields);
									echo $CId;
									$this->Dbg($Fields);
								}*/
								
								
								if($CId = $NElm->Add($Fields)){
									echo "Elm Id:".$CId."<br />";
									if($this->AddCatalogProduct($CId,$Item["CATALOG_PRODUCT"])){
										$this->AddElementPrices($CId,$Item["PRICES"]);
									} else {
										echo "Error add product catalog";
										$this->Dbg("");
									}
                                    dumpLog($CId, 'UpdateDataCloningElementsAndOffers $CId Add', 'clon.log');
								} else {
									echo "Error add element.";
									$this->Dbg($NElm->LAST_ERROR);
								}
							}

						}
						
						unset($this->Log["DATA"]["ELEMENTS"][$key][$VKey]);
						++$i;
						if($i == $this->MUElements){
							$Flag = true;
							break;
						}
					}
				}
			}
			
			if(empty($this->Log["DATA"]["ELEMENTS"][$key])){unset($this->Log["DATA"]["ELEMENTS"][$key]);}
			if($Flag){break;}
		}
		
		return empty($this->Log["DATA"]["ELEMENTS"]);
	}
	
	private function AddElementPrices($CId = 0,$PItem = array()){
		$Fields = array();
		if($CId > 0&& !empty($PItem)){
			foreach($PItem as $key =>$value){
				$Fields = array(
					"PRODUCT_ID" => $CId,
					"EXTRA_ID" => $value["EXTRA_ID"],
					"CATALOG_GROUP_ID" => $value["CATALOG_GROUP_ID"],
					"PRICE" => $value["PRICE"],
					"CURRENCY" => $value["CURRENCY"],
					"QUANTITY_FROM" => $value["QUANTITY_FROM"],
					"QUANTITY_TO" => $value["QUANTITY_TO"],
				);
				CPrice::Add($Fields);
			}
		}
	}
	
	private function AddCatalogProduct($CId = 0,$CPItem = array()){
		$Result = false;
		$Fields = array();
		if($CId > 0&& !empty($CPItem)){
			$Fields = array(
				"ID" => $CId,
				"AVAILABLE" => $CPItem["AVAILABLE"],
				"TYPE" => $CPItem["TYPE"],
				"VAT_ID" => $CPItem["VAT_ID"],
				"VAT_INCLUDED" => $CPItem["VAT_INCLUDED"],
				"QUANTITY" => $CPItem["QUANTITY"],
				"QUANTITY_RESERVED" => $CPItem["QUANTITY_RESERVED"],
				"QUANTITY_TRACE" => $CPItem["QUANTITY_TRACE"],
				"CAN_BUY_ZERO" => $CPItem["CAN_BUY_ZERO"],
				"SUBSCRIBE" => $CPItem["SUBSCRIBE"],
				"BUNDLE" => $CPItem["BUNDLE"],
				"PURCHASING_PRICE" => $CPItem["PURCHASING_PRICE"],
				"PURCHASING_CURRENCY" => $CPItem["PURCHASING_CURRENCY"],
				"WEIGHT" => $CPItem["WEIGHT"],
				"WIDTH" => $CPItem["WIDTH"],
				"LENGTH" => $CPItem["LENGTH"],
				"HEIGHT" => $CPItem["HEIGHT"],
				"MEASURE" => $CPItem["MEASURE"],
				"BARCODE_MULTI" => $CPItem["BARCODE_MULTI"],
				"PRICE_TYPE" => $CPItem["PRICE_TYPE"],
				"RECUR_SCHEME_TYPE" => $CPItem["RECUR_SCHEME_TYPE"],
				"RECUR_SCHEME_LENGTH" => $CPItem["RECUR_SCHEME_LENGTH"],
				"TRIAL_PRICE_ID" => $CPItem["TRIAL_PRICE_ID"],
				"WITHOUT_ORDER" => $CPItem["WITHOUT_ORDER"],
			);
			$Result = CCatalogProduct::Add($Fields);
		}
		return $Result;
	}
	
	private function GetElementPrices($Id = 0){
		$Result = array();
		if($Id > 0){
			$Query = CPrice::GetList(array(),array("PRODUCT_ID" => $Id));
			while($Answer = $Query->Fetch()){
				$Result[] = $Answer;
			}
		}
		return $Result;
	}
	
	private function GetElementFields($Item = array(),$Section = 0){
		$Result = array();
		if(!empty($Item)){
			$Result = array(
				"IBLOCK_ID" => $Item["IBLOCK_ID"],
				"IBLOCK_SECTION_ID" => $Section,
				"ACTIVE" => "Y",
				"SORT" => $Item["SORT"],
				"NAME" => $Item["NAME"],
				"PREVIEW_PICTURE" => $Item["PREVIEW_PICTURE"] > 0 ? CFile::MakeFileArray($Item["PREVIEW_PICTURE"]) : "",
				"PREVIEW_TEXT" => $Item["PREVIEW_TEXT"],
				"PREVIEW_TEXT_TYPE" => $Item["PREVIEW_TEXT_TYPE"],
				"DETAIL_PICTURE" => $Item["DETAIL_PICTURE"] > 0 ? CFile::MakeFileArray($Item["DETAIL_PICTURE"]) : "",
				"DETAIL_TEXT" => $Item["DETAIL_TEXT"],
				"DETAIL_TEXT_TYPE" => $Item["DETAIL_TEXT_TYPE"],
				"CODE" => $Item["CODE"],
				"IBLOCK_TYPE_ID" => $Item["IBLOCK_TYPE_ID"],
				"PROPERTY_VALUES" => array()
			);
			foreach($Item["PROPERTIES"] as $key => $value){
				
				if($value["PROPERTY_TYPE"] == "F"){
					
					if($value["MULTIPLE"] == "Y"){
						foreach($value["VALUE"] as $PKey => $PValue){
							if($PValue > 0){
								$Result["PROPERTY_VALUES"][$value["ID"]]["n".$PKey]["VALUE"] = CFile::MakeFileArray($PValue);
							}
						}
					} else {
						$Result["PROPERTY_VALUES"][$value["ID"]] = CFile::MakeFileArray($value["VALUE"]);
					}
					
				} elseif($value["PROPERTY_TYPE"] == "L"){
					if($value["MULTIPLE"] == "Y"){
						$Result["PROPERTY_VALUES"][$value["ID"]] = $value["VALUE_ENUM_ID"];
					} else {
						$Result["PROPERTY_VALUES"][$value["ID"]] = Array("ENUM_ID" => $value["VALUE_ENUM_ID"]);
					}
				} elseif($value["PROPERTY_TYPE"] == "S" && $value["USER_TYPE"] == "HTML"){
					if($value["MULTIPLE"] == "Y"){
						foreach($value["VALUE"] as $PKey => $PValue){
							$Result["PROPERTY_VALUES"][$value["ID"]][] = array("VALUE" => array ("TEXT" => $PValue["VALUE"]["TEXT"], "TYPE" => $PValue["VALUE"]["TYPE"]));
						}
					} else {
						$Result["PROPERTY_VALUES"][$value["ID"]][] = array("VALUE" => array ("TEXT" => $value["VALUE"]["TEXT"], "TYPE" => $value["VALUE"]["TYPE"]));
					}
				} else if(in_array($value["PROPERTY_TYPE"],array("S","N","E"))){
					$Result["PROPERTY_VALUES"][$value["ID"]] = $value["VALUE"];
				} else {
					$this->Dbg($value);
				}
			}
			//$this->Dbg($Result);
			//$this->Dbg($Item);
		}
		return $Result;
	}
	
	private function UpdateDataCloningSections($i = 0){
		$Result = true;
		$PSection = 0;
		$NSect = new CIBlockSection;
		
		foreach($this->Log["DATA"]["SECTIONS"] as $key => $value){
			$PSection = 0;
			$Fields = array();
			if($value == ""){
				
				$Query = CIBlockSection::GetList(
					array(),
					array("IBLOCK_ID" => $this->Ib,"ID" => $key),
					false,
					array("ID","SORT","NAME","PICTURE","IBLOCK_SECTION_ID","CODE","DETAIL_PICTURE","IBLOCK_TYPE_ID","IBLOCK_ID","DESCRIPTION","UF_*")
				);
				
				if($Answer = $Query->Fetch()){
					$Fields = $Answer;
					
					if(in_array($Fields["ID"],$this->SWhat)){
						$PSection = $this->SWhere;
					} elseif(
						$Fields["IBLOCK_SECTION_ID"] > 0 
						&& 
						isset($this->Log["DATA"]["SECTIONS"][$Fields["IBLOCK_SECTION_ID"]]) 
						&& 
						$this->Log["DATA"]["SECTIONS"][$Fields["IBLOCK_SECTION_ID"]] > 0
					){
						$PSection = $this->Log["DATA"]["SECTIONS"][$Fields["IBLOCK_SECTION_ID"]];
					}
					
					if(!empty($this->SPrefix)){
						foreach($this->SPrefix as $PKey => $PValue){
							if(isset($Fields[$PKey])){
								$Fields[$PKey].=$PValue;
							}
						}
					}
					
					if($PSection > 0){
						$Query = CIBlockSection::GetList(
							array(),
							array("IBLOCK_ID" => $this->Ib,"CODE" => $Fields["CODE"],"SECTION_ID" => $PSection),
							false,
							array("ID")
						);
						
						if($Answer = $Query->Fetch()){
							$this->Log["DATA"]["SECTIONS"][$key] = $Answer["ID"];
						} else {
							unset($Fields["ID"]);
							$Fields["ACTIVE"] = "Y";
							$Fields["IBLOCK_SECTION_ID"] = $PSection;
							$Fields["PICTURE"] = $Fields["PICTURE"] > 0 ? CFile::MakeFileArray($Fields["PICTURE"]) : "";
							$Fields["DETAIL_PICTURE"] = $Fields["DETAIL_PICTURE"] > 0 ? CFile::MakeFileArray($Fields["DETAIL_PICTURE"]) : "";	
							$this->Log["DATA"]["SECTIONS"][$key] = $NSect->Add($Fields);
						}
					}
				}
				$Result = false;
				break;
			}
		}
		
		if(!$Result){
			$i += 1;
			if($i != $this->MUSections){
				return $this->UpdateDataCloningSections($i);
			}
		}
		return $Result;
	}


	private function GetDataCloningElementsAndOffers() {
		$this->Log["DATA"]["ELEMENTS"] = array();
		$this->Log["DATA"]["OFFERS"] = array();
		
		$Elements = array();
		$Sections = array_keys($this->Log["DATA"]["SECTIONS"]);

		if(!empty($Sections)){
			
			$Filter = array("IBLOCK_ID" => $this->Ib,"IBLOCK_SECTION_ID" => $Sections);
			
			if(!empty($this->EFilter)){
				$Filter = array_merge($Filter,$this->EFilter);
			}

			$Query = CIBlockElement::GetList(array(), $Filter, false, false, array("ID","IBLOCK_SECTION_ID"));
			while($Answer = $Query->Fetch()){
				$this->Log["DATA"]["ELEMENTS"][$Answer["IBLOCK_SECTION_ID"]][] = $Answer["ID"];
				$Elements[] = $Answer["ID"];
			}

            dumpLog($Elements, 'GetDataCloningElementsAndOffers $Elements', 'clon.log');

			$Offers = CCatalogSKU::getOffersList($Elements);
			if(!empty($Offers)){	
				$this->Dbg("Offers",false);
				$this->Dbg($Offers);
                dumpLog($Offers, 'GetDataCloningElementsAndOffers $Offers', 'clon.log');
			}
		}
	}
	
	private function GetDataCloningSections(){
		$this->Log["DATA"]["SECTIONS"] = array();
		foreach($this->SWhat as $key => $value){
			$PSections = $this->GetSection(array(),array("ID" => $value),array("ID","LEFT_MARGIN","RIGHT_MARGIN"));
			if(isset($PSections[0])){
				$CSections = $this->GetSection(
					array("LEFT_MARGIN" => "ASC"),
					array('LEFT_MARGIN' => $PSections[0]["LEFT_MARGIN"], 'RIGHT_MARGIN' => $PSections[0]["RIGHT_MARGIN"]),
					array("ID","IBLOCK_SECTION_ID","NAME","CODE","SORT")
				);
				foreach($CSections as $DKey => $DValue){
					$this->Log["DATA"]["SECTIONS"][$DValue["ID"]] = "";
				}
			}
		}
	}
	
	public function GetSection($Sort = array(),$Filter = array(),$Select = array()){
		$Result = array();
		if(!empty($Filter)){
			$Filter["IBLOCK_ID"] = $this->Ib;
			$Query = CIBlockSection::GetList($Sort,$Filter,false,$Select);
			while($Answer = $Query->Fetch()){$Result[] = $Answer;}
		}
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


    static function GetListMRC($ib) {

	    $cacheDir =  __DIR__ . "/ListMRC";
        $ElementsMRC = [];

        if(file_exists($cacheDir)) {
            $ElementsMRC = json_decode(file_get_contents($cacheDir),true);
            return $ElementsMRC;
        }

        CModule::IncludeModule("iblock");
        $QueryMRC = CIBlockElement::GetList(array(), array(), false, false, array());

        while($AnswerMRC = $QueryMRC->Fetch()) {

            $res = CIBlockElement::GetProperty($ib, $AnswerMRC["ID"], array("sort" => "asc"), Array("CODE"=>"YM_SYNC"));
            $res2 = CIBlockElement::GetProperty($ib, $AnswerMRC["ID"], array("sort" => "asc"), Array("CODE"=>"MIN_PRICE_MSK"));
            $YM_SYNC = false;
            $MIN_PRICE_MSK = false;

            while ($ob = $res->GetNext()) {
                $YM_SYNC = $ob['VALUE_ENUM'];
            }

            while ($ob2 = $res2->GetNext()) {
                $MIN_PRICE_MSK = $ob2['VALUE'];
            }

            if (is_null($YM_SYNC) && is_null($MIN_PRICE_MSK)) {
                $ElementsMRC[] = $AnswerMRC["ID"];
            }
        }

        file_put_contents($cacheDir, json_encode($ElementsMRC));

        return $ElementsMRC;
    }

}

$filterOLD = array("3747","3749","3750","75866","70862","70838","70886","11474","70885","3288","3276","3354","694","78480","78481","74489","86151","83055","695","3338","1614","3324","1629","74484","86146","3279","66683","434","3273","3771","3878","1637","3293","3882","71981","3346","3348","3351","3275","2464","3313","86147","71985","70830","70841","70826","70825","71980","70863","70837","11451","3293","71979","11468","70829","70842","3335","70824","74494","70828","70839","70831","70846","3887","460","70860","73004","73005","11473","70801","66802","71983","70799","70868","70865","66951","66949","11458","11457","1151","70681","70771","374","70709","72059","371","70706","70703","3461","336","356","357","364","1610","70707","70751","428","327","70776","11466","387","70774","70734","71999","344","345","1610","70704","70680","70731","379","380","412","70728","70708","355","72054","407","72049","411","71997","70710","70643","348","70775","72046","11462","366","369","72048","376","343","72045","341","399","370","367","408","70712","672","674","71994","3463","70798","1609","3625","85481","3462","86180","335","70660","70705","464","424","67035","70777","72053","410","70732","409","72075","11369","70729","368","70735","406","400","1623","1628","1644","1643","1614","3324","1654","1656","3533","245","363","3344","3753","3752","3459","2465","3291","3280","346","490","63130","2729","213","214","215","245","659","668","669","666");
$filterMRC = CloningIbSectionsAndElements::GetListMRC(8);
$filterMRC = array();
$filterd_NEW = array(3883,3885,70835,66728,3305,3370,1625,66686,66687,3318,3262,11463,3272,3287,3776,86145,70845,3274,1636,3271,2467,3312,3314,3275,66944,3472,3267,1640,3265,3300,74496,66945,3768,706,698,696,697,701,71986,66682,3881,3304,74482,3879,3277,70832,3774,66685,66762,70836,3360,66801,3880,70840,11470,3747,3749,3750,75866,70862,70838,70886,11474,70885,3288,3276,3354,694,78480,78481,74489,86151,83055,695,3338,1614,3324,1629,74484,86146,3279,66683,434,3273,3771,3878,1637,3293,3882,71981,3346,3348,3351,3275,2464,3313,86147,71985,70830,70841,70826,70825,71980,70863,70837,11451,3293,71979,11468,70829,70842,3335,70824,74494,70828,70839,70831,70846,3887,460,70860,73004,73005,11473,70801,66802,71983,70799,70868,70865,66951,66949,11458,11457,1151,70681,70771,374,70709,72059,371,70706,70703,3461,336,356,357,364,1610,70707,70751,428,327,70776,11466,387,70774,70734,71999,344,345,1610,70704,70680,70731,379,380,412,70728,70708,355,72054,407,72049,411,71997,70710,70643,348,70775,72046,11462,366,369,72048,376,343,72045,341,399,370,367,408,70712,672,674,71994,3463,70798,1609,3625,85481,3462,86180,335,70660,70705,464,424,67035,70777,72053,410,70732,409,72075,11369,70729,368,70735,406,400,1623,1628,1644,1643,1614,3324,1654,1656,3533,245,363,3344,3753,3752,3459,2465,3291,3280,346,490,63130,2729,213,214,215,245,659,668,669,666);

$filter = array_merge($filterOLD, $filterMRC, $filterd_NEW);
$filter = array_unique($filter);

dumpLog($filter);

$Cisae = new CloningIbSectionsAndElements(
	8,
	973,
	array(19,20,23,24),
	array("CODE" =>"-vo"),
	array("NAME" => " во","CODE" => "-vo"),
	array(),
	array(
		"ID" => $filter
	)
);


$Cisae->Init();


if($USER->IsAdmin()){
	
}
?>