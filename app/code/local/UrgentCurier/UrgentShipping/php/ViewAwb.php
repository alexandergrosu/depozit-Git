<?php
$mageFilename = '../../../../../../app/Mage.php';
require $mageFilename;

$awb = intval(addslashes($_GET['awb']));
if (!$awb) die('Eroare!');
$soap = Mage::getStoreConfig('urgentcurier/config/soap');
$idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
$iduser = Mage::getStoreConfig('urgentcurier/config/iduser');
$idtarifset = Mage::getStoreConfig('urgentcurier/config/idtarifset');
$secret = md5($idclient.$iduser.$idtarifset.date('d.m.Y'));
if ($secret == addslashes($_GET['secret'])) {
    include('../controllers/urgent_curier.class.php');
    $obj_urgent = new urgent_curier();
    $result = $obj_urgent -> GetDetailsAwbById($soap, $idclient, $awb);
    if ($result) {
        $awbPriceSite = (array)$result['awbPriceSite'];
        echo '<table cellspacing="0" style="margin: 0 0 5px 0;">
            <tr class="headings"><th colspan="5" class="no-link last">Detalii AWB</th></tr>
            <tr><td>Numar Awb</td><td class="last">'.$result['CodBara'].'</td></tr>
            <tr><td>Status</td><td class="last">'.($result['AwbStatusExpresie']?$result['AwbStatusExpresie']:'Netiparit').'</td></tr>
            <tr><td>Data confirmare</td><td class="last">'.$result['DataConfirmare'].'</td></tr>
            <tr><td>Nume confirmare</td><td class="last">'.$result['NumeConfirmare'].'</td></tr>
            <tr><td>Data expeditie</td><td class="last">'.date('d.m.Y - H:i:s', strtotime($result['DataCreare'])).'</td></tr>
            <tr><td>Oras destinatar</td><td class="last">'.$result['OrasDest'].'</td></tr>
            <tr><td>Adresa destinatar</td><td class="last">'.$result['AdresaDest'].'</td></tr>
            <tr><td>Greutate</td><td class="last">'.$awbPriceSite['Greutate'].' KG</td></tr>
            <tr><td>Ramburs</td><td class="last">'.$awbPriceSite['RambursValoare'].' RON</td></tr>
            <tr><td>Continut declarat</td><td class="last">'.$result['AwbContent'].'</td></tr>
            <tr><td>Observatii</td><td class="last">'.$result['AwbObs'].'</td></tr>
            <tr><td>Plata expeditie la</td><td class="last">'.($awbPriceSite['IdTipPlatitor']==1?'Expeditor':'Destinatar').'</td></tr>
            <tr><td>Plata ramburs la</td><td class="last">'.($result['IdTipPlatitorRamburs']==1?'Destinatar':'Expeditor').'</td></tr>
        </table>
        <button id="awb_back" type="button" class="scalable back"><span>Inapoi la expeditie</span></button>';
    } else {
        echo 'Nu a fost gasit niciun awb cu id-ul '.$awb;
    }
}
?>