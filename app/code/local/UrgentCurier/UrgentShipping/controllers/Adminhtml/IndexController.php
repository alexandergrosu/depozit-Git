<?php
class UrgentCurier_UrgentShipping_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (!$post['items'] && $post['actiune']=='sterge') {
                Mage::throwException('Nu ati selectat nicio livrare pentru a fi stearsa!');
            }
            if (!$post['items'] && $post['actiune']=='valideaza') {
                Mage::throwException('Nu ati selectat nicio livrare pentru a fi validata!');
            }
            if (!$post['items'] && $post['actiune']=='anuleaza') {
                Mage::throwException('Nu ati selectat nicio livrare pentru a fi anulata!');
            }
            if (!$post['items'] && ($post['actiune']=='print_etichete' || $post['actiune']=='print_awb')) {
                Mage::throwException('Nu ati selectat nicio livrare pentru printarea documentelor!');
            }
            
            // resure pentru query in baza de date
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            
            // resurse pentru adaugare awb
            $soap = Mage::getStoreConfig('urgentcurier/config/soap');
            $idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
            $userId = Mage::getStoreConfig('urgentcurier/config/iduser');
            $PickUpDateFrom = Mage::getStoreConfig('urgentcurier/config/PickUpDateFrom');
            $PickUpDateUntil = Mage::getStoreConfig('urgentcurier/config/PickUpDateUntil');
            include('app/code/local/UrgentCurier/UrgentShipping/controllers/urgent_curier.class.php');
            //ini_set('soap.wsdl_cache_ttl', 1);
            $obj_urgent = new urgent_curier();
            
            // genereaza printuri si opreste scriptul
            if ($post['actiune']=='print_etichete') {
                $CodBaras = '';
                foreach ($post['items'] as $item) {
                    $CodBaras .= $post['awb_items'][$item].'|';
                }
                $CodBaras = trim($CodBaras, '|');
                $pdf = $obj_urgent -> GetAwbPrintEtichete($soap, $idclient, $CodBaras, '');
                header('Content-type: application/pdf');
                echo $pdf;
                die();
            }
            if ($post['actiune']=='print_awb') {
                $CodBaras = '';
                foreach ($post['items'] as $item) {
                    $CodBaras .= $post['awb_items'][$item].'|';
                }
                $CodBaras = trim($CodBaras, '|');
                $pdf = $obj_urgent -> GetAwbPrint($soap, $idclient, $CodBaras, '');
                header('Content-type: application/pdf');
                echo $pdf;
                die();
            }
            if ($post['actiune']=='print_borderou') {
                $pdf = $obj_urgent -> GetBorderouPrint($soap, $idclient, $post['orderId'], '', '');
                header('Content-type: application/pdf');
                echo $pdf;
                die();
            }
            
            // finalizeaza comanda si opreste scriptul
            if ($post['actiune']=='finalizeaza') {
                $obj_urgent -> EndOrder($soap, $userId, $idclient, $post['idClientPunctLucru'], 'Validated', $PickUpDateFrom, $PickUpDateUntil);
                $pdf = $obj_urgent -> GetBorderouPrint($soap, $idclient, $post['orderId'], '', '');
                header('Content-type: application/pdf');
                echo $pdf;
                die();
                //Mage::getSingleton('adminhtml/session')->addSuccess('Comanda curenta a fost finalizata!');
                //$this->_redirect('urgentshipping/adminhtml_index/index');
            }
            
            // aplica actiune pentru fiecare item bifat
            foreach ($post['items'] as $item) {
                if ($post['actiune']=='sterge') {
                    $readConnection->query("DELETE FROM urgent_curier WHERE id_comanda='".$item."'");
                }
                if ($post['actiune']=='valideaza') {
                    $list = $readConnection->fetchAll("SELECT * FROM urgent_curier WHERE id_comanda='".$item."'");
                    $line = $list[0];
                    if ( $line['ramburs_numerar'] == 0 && $line['ramburs_cont_colector'] > 0 && $line['ramburs_alt_tip'] == '' ) {
                        $Ramburs = '';
                        $TipCerere = 2;
                        $RambursValoare = $line['ramburs_cont_colector'];
                    }
                    if ( $line['ramburs_numerar'] == 0 && $line['ramburs_cont_colector'] == 0 && $line['ramburs_alt_tip'] == '' ) {
                        $Ramburs = '';
                        $TipCerere = 0;
                        $RambursValoare = '';
                    }
                    if ( $line['ramburs_numerar'] == 0 && $line['ramburs_cont_colector'] == 0 && $line['ramburs_alt_tip'] != '' ) {
                        $Ramburs = $line['ramburs_alt_tip'];
                        $TipCerere = 1;
                        $RambursValoare = $line['ramburs_numerar'];
                    }
                    if ( $line['ramburs_numerar'] > 0 && $line['ramburs_cont_colector'] == 0 ) {
                        $Ramburs = $line['ramburs_alt_tip'];
                        $TipCerere = 1;
                        $RambursValoare = $line['ramburs_numerar'];
                    }
                    $pickup = $obj_urgent -> GetPickUpSite($soap, $line['pickup_id']);
                    
                    try {
                    $NewAwb = $obj_urgent -> NewAwb($soap, $idclient, $post['orderId'][$line['id_comanda']], $line['id_comanda'], $post['IdClientExp'], $post['ClientExp'], $pickup['IdOras'], $pickup['Contact'], $pickup['Address'], $pickup['Phone'], $pickup['IdStrada'], $pickup['NumarStrada'], $line['destinatar'], $line['dest_localitate_id'], $line['dest_pers_contact'], $line['dest_adresa'], $line['dest_telefon'], $line['dest_strada_id'], $line['dest_strada_nr'], $Ramburs, $line['plata_ramburs'], $post['IdTarifSet'], $line['valoare_declarata'], $line['plicuri'], $line['colete'], $line['kilograme'], $TipCerere, $RambursValoare, $line['plata_expeditie'], $line['livrare_sambata'], $line['continut_expeditie'], $line['observatii'], $pickup['Email']);
                    }
                    catch(Exception $e) {
                        $erori_validare .= 'Comanda '.$item.' nu a putut fi validata. Verificati detaliile expeditiei!<br /><span style="font-weight:normal; font-size:11px;">('.$e->getMessage().')</span><br />';
                        $NewAwb = 'null';
                    }                    
                    if (is_numeric($NewAwb)) {
                        $readConnection->query("UPDATE urgent_curier SET status='validat', urgent_awb_codbara='".$NewAwb."' WHERE id_comanda='".$item."'");
                    }
                }
                if ($post['actiune']=='anuleaza') {
                    $DeleteAwb = $obj_urgent -> DeleteAwb($soap, $item);
                    if ($DeleteAwb == 1) {
                        $readConnection->query("UPDATE urgent_curier SET status='asteptare' WHERE urgent_awb_codbara='".$item."'");
                    }
                }
            }
            if ($post['actiune']=='sterge') {
                Mage::getSingleton('adminhtml/session')->addSuccess('Livrarile selectate au fost sterse!');
            }
            if ($post['actiune']=='valideaza') {
                if ($erori_validare) {
                    Mage::getSingleton('adminhtml/session')->addError($erori_validare);
                } else {
                    Mage::getSingleton('adminhtml/session')->addSuccess('Livrarile selectate au fost validate!');
                }
            }
            if ($post['actiune']=='anuleaza') {
                Mage::getSingleton('adminhtml/session')->addSuccess('Livrarile selectate au fost anulate!');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('urgentshipping/adminhtml_index/index');
    }
}