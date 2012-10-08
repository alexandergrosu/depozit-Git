<?php
$mageFilename = '../../../../../../app/Mage.php';
require $mageFilename;

$awbNumber = intval(addslashes($_GET['awb']));
if (!$awbNumber) die('Va rugam sa introduceti numarul AWB!');
$soap = Mage::getStoreConfig('urgentcurier/config/soap');
$idClient = Mage::getStoreConfig('urgentcurier/config/idclient');
include('../controllers/urgent_curier.class.php');
    $obj_urgent = new urgent_curier();
    $tracking = $obj_urgent -> GetAwbTracking($soap, $awbNumber, $idClient);
    $explode = explode('|', $tracking);
    echo $explode[1];
?>