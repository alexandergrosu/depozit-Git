<?php
$mageFilename = '../../../../../../app/Mage.php';
require $mageFilename;

$id = intval(addslashes($_GET['id']));
$id_long = intval(addslashes($_GET['id_long']));
$get_secret = addslashes($_GET['secret']);
$idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
    
	//Schimba status-ul comenzii cand este actionat butonul "Urgent"
				     $orderId = $id;
					 $order = Mage::getModel('sales/order')->load($orderId);
					 $order->setData('state', "complete");
    				 $order->setStatus("Livrat-Urgent");
					 $isCustomerNotified = false;
   					 $order->save();
					 
echo "ok";
?>