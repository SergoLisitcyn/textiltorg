<?
set_time_limit(5000);
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

global $USER;

class ParsingProductsSiteEkatalogRu {

    private $Files = null;
    private $Dirs = null;
    private $MStep = 1;
    private $Ib = 8;
    private $Log = array();
    private $MElements = 500;
    private $PriceStep = 100;

    function __construct() {
        $this->Dirs->Report->Name = "report_ekatalogru";
        $this->Dirs->Report->Absolute = __DIR__."/".$this->Dirs->Report->Name;
        if(!file_exists($this->Dirs->Report->Absolute)){mkdir($this->Dirs->Report->Absolute,0777);}
        $this->Files->Log->Name = "log.json";
        $this->Files->Log->Absolute = $this->Dirs->Report->Absolute."/".$this->Files->Log->Name;

        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
    }

    public function Init(){
        $this->GetLog();

        if($this->Log["STEP"] == 1){

        } else {

            if(!isset($this->Log["NUM_PAGE"]) || !isset($this->Log["MAX_PAGE"])){
                $this->Log["NUM_PAGE"] = $this->Log["MAX_PAGE"] = 1;
            } else {
                $this->Log["NUM_PAGE"] = intval($this->Log["NUM_PAGE"]);
                $this->Log["MAX_PAGE"] = intval($this->Log["MAX_PAGE"]);
            }

            if($this->Log["NUM_PAGE"] > $this->Log["MAX_PAGE"]){
                $this->Log["NUM_PAGE"] = $this->Log["MAX_PAGE"] = 1;
            }

            $Query = CIBlockElement::GetList(array(), array(), false, false, array());

            if($this->Log["MAX_PAGE"] == 1){
                $this->Log["MAX_PAGE"] = ceil($Query->SelectedRowsCount()/$this->MElements);
            }

            while($Answer = $Query->Fetch()) {

                $vo = substr($Answer['CODE'], -3);
                if ($vo != '-vo') {
                    continue;
                }

                $name = substr($Answer['NAME'], 0, -3);
                $Html = $this->GetDataByNAme($name);

                preg_match_all('/<a.*?href=["\'](.*?)["\'].*?>/i', $Html, $matches);

                $ekatalogLinkGood = false;

                foreach ($matches[1] as $one) {
                    $pos = stripos($one, '/prices/');
                    if ($pos !== false) {
                        $ekatalogLinkGood = 'https://www.e-katalog.ru'.$one;
                    }
                }

                if(filter_var($ekatalogLinkGood, FILTER_VALIDATE_URL)){
                    $Html = $this->GetData($ekatalogLinkGood);
                    $Data = $this->ParsingHtml($Html);

                    if(!empty($Data)){
                        $Price = "";
                        foreach($Data as $key => $value){
                            if(isset($value["ATTR"]["itemprop"]) && $value["ATTR"]["itemprop"] == "lowPrice"){
                                $Price = $value["VALUE"];
                                break;
                            } elseif(isset($value["ATTR"]["price_marker"])){
                                $Price = $value["VALUE"];
                                break;
                            }
                        }
                        $Price = str_replace(array(" ","\s","&nbsp;"),"",$Price);

                        if($Price > 0) {
                            $Price = $Price - $this->PriceStep;
                            $CQuery = CCatalogGroup::GetList(array());
                            while ($CAnswer = $CQuery->Fetch()){
                                $PQuery = CPrice::GetList(array(),array("PRODUCT_ID" => $Answer["ID"],"CATALOG_GROUP_ID" => $CAnswer["ID"]));
                                if($PAnswer = $PQuery->Fetch()){
                                    dumpLog('Цена '.  $Price . ' ИД: ' . $Answer["ID"] . ' Имя: ' . $Answer["NAME"] . ' Ссылка: ' . $ekatalogLinkGood, 'Update', 'price.log');
									CPrice::Update($PAnswer["ID"], array("PRICE" => $Price));
								} else {
                                    dumpLog('Цена '.  $Price . ' ИД: ' . $Answer["ID"] . ' Имя: ' . $Answer["NAME"] . ' Ссылка: ' . $ekatalogLinkGood, 'Update', 'price.log');
                                    CPrice::Add(array(
                                        "PRODUCT_ID" => $Answer["ID"],
                                        "CATALOG_GROUP_ID" => $CAnswer["ID"],
                                        "PRICE" => $Price,
                                        "CURRENCY" => "RUB",
                                    ));
                                }
                            }
                        }
                    }
                }

                $this->Dbg($Answer,false);
                $this->Dbg($Data,false);
            }
            ++$this->Log["NUM_PAGE"];
            $this->Dbg($this->Log,false);
        }
        $this->SetLog();
    }

    private function ParsingHtml($Html = ""){
        $Result = array();
        preg_match_all("/<(\w+)[^>]*itemprop\s*=\s*(['\"])offers\\2[^>]*>(.*?)<\/\\1>/",$Html,$Matches,PREG_PATTERN_ORDER);
        if(!empty($Matches)){
            $Content = $Matches[3][0];
            preg_match_all("/<(\w+)[^>]*>(.*?)<\/\\1>/",$Content,$CMatches);
            foreach($CMatches[0] as $key => $value){
                $TagName = "";
                if(preg_match("/<\/(.*?)>/",$value,$TMatches)){
                    $TagName = $TMatches[1];
                }
                if($TagName != ""){
                    $Params = explode(" ",trim(str_replace(array("<".$TagName,">","</".$TagName)," ",$value)));
                    $i = 0;
                    $Count = count($Params)-1;
                    $Data = array();
                    foreach($Params as $Item){

                        if($Item == ""){continue;}

                        if($Count == $i){
                            $Data["VALUE"] = $Item;
                        } else {
                            if(preg_match("/=/",$Item)){
                                $Attr = explode("=",$Item);
                                $Data["ATTR"][$Attr[0]] = str_replace(array("\"","'"),"",$Attr[1]);
                            } else {
                                $Data["ATTR"][$Item] = "";
                            }
                        }
                        ++$i;

                    }
                    $Result[] = $Data;
                }
                $Content = str_replace($value,"",$Content);
            }

            preg_match_all("/<(.*?)>/",$Content,$matches1);

            foreach($matches1[0] as $key => $value){
                $Params = explode(" ",$value);
                $Data = array();
                foreach($Params as $Item){
                    if(preg_match("/^</",$Item)){continue;}

                    $Item = trim(str_replace(">","",$Item));
                    if(preg_match("/=/",$Item)){
                        $Attr = explode("=",$Item);
                        $Data["ATTR"][$Attr[0]] = str_replace(array("\"","'"),"",$Attr[1]);
                    } else {
                        $Data["ATTR"][$Item] = "";
                    }

                }
                $Result[] = $Data;
            }
        }
        return $Result;
    }

    private function GetData($Url = "") {
        $Result = "";
        if($Ci = curl_init()){
            curl_setopt($Ci,CURLOPT_URL,$Url);
            curl_setopt($Ci,CURLOPT_RETURNTRANSFER,true);
            $Result = curl_exec($Ci);
            if(curl_getinfo($Ci, CURLINFO_HTTP_CODE) != 200){
                $Result = "";
            }
            curl_close($Ci);
        }
        return $Result;
    }

    private function GetDataByNAme($Name = "") {
        $Result = "";
        $Name = str_replace(' ', '%20', $Name);
        $Url = "https://www.e-katalog.ru/ek-list.php?search_=" . $Name;

        if($Ci = curl_init()){
            curl_setopt($Ci,CURLOPT_URL,$Url);
            curl_setopt($Ci,CURLOPT_RETURNTRANSFER,true);
            $Result = curl_exec($Ci);
            if(curl_getinfo($Ci, CURLINFO_HTTP_CODE) != 200){
                $Result = "";
            }
            curl_close($Ci);
        }
        return $Result;
    }

    private function GetLog(){
        $Flag = true;
        $Log = array();
        if(file_exists($this->Files->Log->Absolute)){
            $Log = json_decode(file_get_contents($this->Files->Log->Absolute),true);
            if(isset($Log["STEP"]) && $Log["STEP"] >= 0){
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


    private function getVoList($ib = 8) {

        CModule::IncludeModule("iblock");
        $QueryVO = CIBlockElement::GetList(array(), array(), false, false, array());
        $ElementsVO = [];

        while($AnswerVO = $QueryVO->Fetch()) {
            $vo = substr($AnswerVO['CODE'], -3);
            if ($vo == '-vo') {
                $ElementsVO[] = $AnswerVO['ID'];
            }
        }
        return $ElementsVO;
    }

}

$Ppser = new ParsingProductsSiteEkatalogRu();
$Ppser->Init();

?>