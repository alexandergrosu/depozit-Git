<?php
$patern = strtolower($_GET["term"]);
if (!$patern) return;
$client = new SoapClient("http://app.urgentonline.ro/Integration/WebSite/SiteIntegrationService.asmx?WSDL");

// get judetele pt afisare impreuna cu localitatile
$counties = $client->GetCounties();
$result = $counties->GetCountiesResult->any;
$result = str_replace(array('<IdJudet>','<Denumire>'),array('##<IdJudet>','%%<Denumire>'), $result);
$result = strip_tags($result);
$explode = explode('##', $result);
foreach ($explode as $key=>$val) {
    $item = explode('%%',$val);
    if ($item[0]&&$item[1]) {
        $NameJudet_Arr[$item[0]]=$item[1];
        $IdJudet_Arr[$item[1]]=$item[0];
    }
}

// get lista localitatilor pe baza $patern
$result = $client->GetCityNamesjSON(array('patern'=>$patern));
$suggestions = $result->GetCityNamesjSONResult->suggestions;
$data = $result->GetCityNamesjSONResult->data;
foreach ($suggestions as $pre_array) {
    $array = '';
    if (!is_array($pre_array)) $array[] = $pre_array; else $array = $pre_array;
    foreach ($array as $key=>$val) {
        $explode = explode('[', $val);
        $nume[$key] = $val;
        $localitate[$key] = trim($explode[0]);
        $judet[$key] = trim($explode[1], '] ');
    }
}
foreach ($data as $pre_array) {
    $array = '';
    if (!is_array($pre_array)) $array[] = $pre_array; else $array = $pre_array;
    foreach ($array as $key=>$val) {
        $res[] = array(
            'id'=>$val,
            'desc'=>$localitate[$key],
            'label'=>$nume[$key],
            'judet'=>$judet[$key],
            'judet_id'=>$IdJudet_Arr[$judet[$key]]
        );
    }
}
echo json_encode($res);
?>