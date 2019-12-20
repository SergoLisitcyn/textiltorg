<?
$bik = intval($_REQUEST["bik"]);

if ($bik)
{
    $data = file_get_contents("http://www.bik-info.ru/api.html?type=json&bik=".$bik);

    if (!empty($data))
    {
        $data = str_replace("&quot;", "", $data);
        header("Content-Type: application/json");
        echo $data;
    }
}