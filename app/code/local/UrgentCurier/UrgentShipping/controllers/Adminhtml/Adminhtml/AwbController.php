<?php
class UrgentCurier_UrgentShipping_Adminhtml_AwbController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException('Toate campurile sunt obligatorii!');
            }
            $resource = Mage::getSingleton('core/resource');            
            $readConnection = $resource->getConnection('core_read');
            $readConnection->query("UPDATE urgent_curier SET
                pickup_id = '".$post['pickup_id']."',
                destinatar = '".$post['destinatar']."',
                dest_judet = '".$post['dest_judet']."',
                dest_judet_id = '".$post['dest_judet_id']."',
                dest_localitate = '".$post['dest_localitate']."',
                dest_localitate_id = '".$post['dest_localitate_id']."',
                dest_strada = '".$post['dest_strada']."',
                dest_strada_id = '".$post['dest_strada_id']."',
                dest_strada_nr = '".$post['dest_strada_nr']."',
                dest_adresa = '".$post['dest_adresa']."',
                dest_pers_contact = '".$post['dest_pers_contact']."',
                dest_telefon = '".$post['dest_telefon']."',
                plicuri = '".$post['plicuri']."',
                colete = '".$post['colete']."',
                kilograme = '".$post['kilograme']."',
                valoare_declarata = '".$post['valoare_declarata']."',
                plata_ramburs = '".$post['plata_ramburs']."',
                plata_expeditie = '".$post['plata_expeditie']."',
                livrare_sambata = '".$post['livrare_sambata']."',
                continut_expeditie = '".$post['continut_expeditie']."',
                observatii = '".$post['observatii']."',
                ramburs_numerar = '".$post['ramburs_numerar']."',
                ramburs_cont_colector = '".$post['ramburs_cont_colector']."',
                ramburs_alt_tip = '".$post['ramburs_alt_tip']."'
            WHERE id_comanda = '".$post['id_comanda']."'");
            Mage::getSingleton('adminhtml/session')->addSuccess('Datele expeditiei au fost salvate!');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('urgentshipping/adminhtml_index/index');
    }
}