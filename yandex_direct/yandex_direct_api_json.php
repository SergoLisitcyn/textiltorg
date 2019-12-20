<?
class YandexDirectApiJson {
	
	private $Url = 'https://api.direct.yandex.com/json/v5/';
	private $Token = 'AgAAAAAyt3SXAAXaF2YICMyVAEBaptyqGAoeq0o';
	private $Login = 'textiletorg-test007';
	
	
	public function AdImagesAdd($List = array()){
		$Result = array();
		$Params = array(
			"method" => "add",
			"params" => array(
				"AdImages" => array()
			)
		);
		
		foreach($List as $key => $value){
			if(file_exists($value["PATH"]) && $value["NAME"] != ""){
				$Params["params"]["AdImages"][] = array(
					"ImageData" => base64_encode(file_get_contents($value["PATH"])),
					"Name" => $value["NAME"],
				);
			}
		}
		
		if(!empty($Params["params"]["AdImages"])){
			$Result = $this->Send("adimages","POST",$Params);
		}
		return $Result;
	}
	
	public function AdImagesGet($SelectionCriteria = array(),$FieldNames = array()){
		$Result = array();
		$Params = array("method" => "get","params" => array());
		
		if(!empty($SelectionCriteria)){
			$Params["params"]["SelectionCriteria"] = $SelectionCriteria;
		}
		
		if(!empty($FieldNames)){
			$Params["params"]["FieldNames"] = $FieldNames;
		}
		
		if(!empty($Params["params"])){
			$Result = $this->Send("adimages","POST",$Params);
		}
		return $Result;
	}
	
	public function AdsUpdate($Ads = array()){
		$Result = array();
		$Params = array("method" => "update","params" => array("Ads" => array()));
		if(!empty($Ads)){
			$Params["params"]["Ads"] = $Ads;
		}
		if(!empty($Params["params"]["Ads"])){
			$Result = $this->Send("ads","POST",$Params);
		}
		return $Result;
	}
	
	public function AdsGet($SelectionCriteria = array(),$FieldNames = array(),$TextAdFieldNames = array(),$TextAdPriceExtensionFieldNames = array()){
		$Result = array();
		$Params = array("method" => "get","params" => array());
		if(!empty($SelectionCriteria)){
			$Params["params"]["SelectionCriteria"] = $SelectionCriteria;
		}
		if(!empty($FieldNames)){
			$Params["params"]["FieldNames"] = $FieldNames;
		}
		if(!empty($TextAdFieldNames)){
			$Params["params"]["TextAdFieldNames"] = $TextAdFieldNames;
		}
		if(!empty($TextAdPriceExtensionFieldNames)){
			$Params["params"]["TextAdPriceExtensionFieldNames"] = $TextAdPriceExtensionFieldNames;
		}
		if(!empty($Params["params"])){
			$Result = $this->Send("ads","POST",$Params);
		}
		return $Result;
	}
	
	private function Send($Url = "",$Method = "",$Params = array()){
		$Result = array();
		$Url = $this->Url.$Url;
		$Headers = array(
		   "Authorization: Bearer ".$this->Token,                  
		   "Client-Login: ".$this->Login,                      
		   "Accept-Language: ru",                             
		   "Content-Type: application/json; charset=utf-8"
		);
		$Body = json_encode($Params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$StreamContext = stream_context_create(array(
		   'http' => array(
		      'method' => $Method,
		      'header' => $Headers,
		      'content' => $Body
		   )
		));
		$Result = json_decode(file_get_contents($Url, 0, $StreamContext),true);
		return $Result;
	}
}
?>