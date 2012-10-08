<?php
$patern = strtolower($_GET["term"]);
$citycode = strtolower($_GET["citycode"]);
if (!$patern) return;
$client = new SoapClient("http://app.urgentonline.ro/Integration/WebSite/SiteIntegrationService.asmx?WSDL");

// get lista strazilor pe baza $patern si $citycode
$result = $client->GetStreetNames(array('patern'=>$patern, 'idOras'=>$citycode));
$data = $result->GetStreetNamesResult;
$lines = preg_split('/\n|\r|\r\n/', $data);
foreach ($lines as $item) {
    if ($item) {
        $explode = explode('|', $item);
        $res[] = array(
            'id'=>end($explode),
            'label'=>$explode[0]
        );
    }
}
echo json_encode($res);
?>