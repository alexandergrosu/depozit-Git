<?php
$mageFilename = '../../../../../../app/Mage.php';
require $mageFilename;

$id = intval(addslashes($_GET['id']));
$id_long = intval(addslashes($_GET['id_long']));
$get_secret = addslashes($_GET['secret']);
$idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
$iduser = Mage::getStoreConfig('urgentcurier/config/iduser');
$idtarifset = Mage::getStoreConfig('urgentcurier/config/idtarifset');
    $livrare_sambata = Mage::getStoreConfig('urgentcurier/config/livrare_sambata');
        if (!$livrare_sambata) $livrare_sambata = '0';
    $plata_expeditie = Mage::getStoreConfig('urgentcurier/config/plata_expeditie');
        if (!$plata_expeditie) $plata_expeditie = '1';
    $plata_ramburs = Mage::getStoreConfig('urgentcurier/config/plata_ramburs');
        if (!$plata_ramburs) $plata_ramburs = '2';
    $asigurare = Mage::getStoreConfig('urgentcurier/config/asigurare');
        if (!$asigurare) $asigurare = 'da';
    $tip_ramburs = Mage::getStoreConfig('urgentcurier/config/tip_ramburs');
        if (!$tip_ramburs) $tip_ramburs = 'cont';
$secret = md5($idclient.$iduser.$idtarifset.date('d.m.Y'));
if ($get_secret != $secret) die('Acces nepermis!');

    $resource = Mage::getSingleton('core/resource');
    $sales_flat_order = $resource->getTableName('sales_flat_order');
    $sales_flat_order_address = $resource->getTableName('sales_flat_order_address');
    $sales_flat_order_item = $resource->getTableName('sales_flat_order_item');
    $sales_flat_order_payment = $resource->getTableName('sales_flat_order_payment');
    
    $readConnection = $resource->getConnection('core_read');
    $results = $readConnection->fetchAll("SELECT id FROM urgent_curier WHERE id_comanda='".$id_long."'");
    if (is_array($results[0])) die('old');
    $sel_query = "
        SELECT
        	
            sfo.weight,
            sfo.order_currency_code,
            sfo.created_at,
            sfo.grand_total,
            sfo.shipping_amount,
            sfo.total_paid,
            sfoa.region,
            sfoa.region_id,
            sfoa.city,
            sfoa.citycode,
            sfoa.street,
            sfoa.telephone,
            sfoa.lastname,
            sfoa.firstname,
            sfoa.middlename,
            sfoa.company,
            sfop.method,
            GROUP_CONCAT(sfoi.name SEPARATOR '; ') AS continut
        FROM
            $sales_flat_order sfo
        LEFT JOIN $sales_flat_order_address sfoa ON sfo.shipping_address_id = sfoa.entity_id
        LEFT JOIN $sales_flat_order_item sfoi ON sfo.entity_id = sfoi.order_id
        LEFT JOIN $sales_flat_order_payment sfop ON sfo.entity_id = sfop.parent_id
        WHERE
            sfo.entity_id = '$id' AND
            sfoi.parent_item_id IS NULL
        GROUP BY sfo.increment_id
        LIMIT 0,1
        ";
    $results = $readConnection->fetchAll($sel_query);
    if (is_array($results)) {
        foreach ($results as $val) {
            
        $plafon_plata_dest = Mage::getStoreConfig('urgentcurier/config/plafon_plata_dest');
        if (round(($val['grand_total']-$val['shipping_amount']), 2) > $plafon_plata_dest && $plafon_plata_dest>0) {
            $plata_expeditie = '1';
            $plata_ramburs = '2';
        }

            // get puctul de ridicare default din config
            $pickup_default = Mage::getStoreConfig('urgentcurier/config/pickup_default');
            // get array cu toate punctele de livrare
            $soap = Mage::getStoreConfig('urgentcurier/config/soap');
            $idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
            include('../controllers/urgent_curier.class.php');
            $obj_urgent = new urgent_curier();
            $pickup = $obj_urgent -> GetPickUpSites_Simple($soap, $idclient);
            // verifica daca punctul de ridicare default nu este in punctele de livrare disponibile
            if (!array_key_exists($pickup_default, $pickup)) $pickup_default = key($pickup);
            
            if ($val['weight']<1) $weight=1; else $weight=round($val['weight']);
            $valoare_ramburs = round($val['grand_total'] - $val['total_paid'], 2);
            if ($val['method'] != 'cashondelivery') $valoare_ramburs = 0;
            elseif ($plata_expeditie==2 || $plata_ramburs==1) $valoare_ramburs = round($val['grand_total'] - $val['shipping_amount'] - $val['total_paid'], 2);

                if ($tip_ramburs=='cont') {
                    $ramburs_cont_colector = $valoare_ramburs;
                    $ramburs_numerar = 0;
                } else {
                    $ramburs_cont_colector = 0;
                    $ramburs_numerar = $valoare_ramburs;
                }
                if ($asigurare=='da') {
                    $valoare_declarata = round(($val['grand_total']-$val['shipping_amount']), 2);
                } else {
                    $valoare_declarata = 0;
                }
            
            function strip($var) {
                return str_replace(array('\'','"'), '', $var);
            }
			
			//Schimba status-ul comenzii cand este actionat butonul "Urgent"
				     $orderId = $id;
					 $order = Mage::getModel('sales/order')->load($orderId);
					 $order->setData('state', "processing");
    				 $order->setStatus("De Livrat cu Urgent");
					 $isCustomerNotified = false;
   					 $order->save();
			
			// if statement -> daca COMPANIE are valoare atunci il trece la destinator
			// daca nu -> trece NUMELE + PRENUMELE
			if (isset($val['company'])) {
				$toggle = "'".strip($val['company'])."'";
			} else {
				$toggle = "'".strip($val['firstname'])." ".strip($val['lastname'])." ".strip($val['middlename'])."'";
			}
			
            $writeConnection = $resource->getConnection('core_write');
            $insert_query = "
                INSERT INTO `urgent_curier` (
                    `id`, 
                    `id_comanda`, 
                    `data_comanda`, 
                    `pickup_id`, 
                    `destinatar`, 
                    `dest_judet`, 
                    `dest_judet_id`, 
                    `dest_localitate`, 
                    `dest_localitate_id`, 
                    `dest_adresa`, 
                    `dest_pers_contact`, 
                    `dest_telefon`, 
                    `plicuri`, 
                    `kilograme`, 
                    `valoare_declarata`, 
                    `moneda`, 
                    `plata_ramburs`, 
                    `plata_expeditie`, 
                    `livrare_sambata`, 
                    `continut_expeditie`, 
                    `ramburs_numerar`, 
                    `ramburs_cont_colector`, 
                    `status`
                ) VALUES (
                    NULL, 
                    '".strip($id_long)."',
                    '".strip($val['created_at'])."',
                    '".strip($pickup_default)."',
                    '".strip($toggle)."',
                    '".strip($val['region'])."',
                    '".strip($val['region_id'])."',
                    '".strip($val['city'])."',
                    '".strip($val['citycode'])."',
                    '".strip($val['street'])." ".strip($val['postcode'])."',
                    '".strip($val['lastname'])." ".strip($val['firstname'])." ".strip($val['middlename'])."',
                    '".strip($val['telephone'])."',
                    '1', 
                    '".strip($weight)."',
                    '".strip($valoare_declarata)."',
                    '".strip($val['order_currency_code'])."', 
                    '".$plata_ramburs."', 
                    '".$plata_expeditie."', 
                    '".$livrare_sambata."', 
                    'componente laptop',
                    '".strip($ramburs_numerar)."',
                    '".strip($ramburs_cont_colector)."',
                    'asteptare'
                )
            ";
            $writeConnection->query($insert_query);
            echo 'ok';
        }
    } else {
        echo 'err';
    }
?>