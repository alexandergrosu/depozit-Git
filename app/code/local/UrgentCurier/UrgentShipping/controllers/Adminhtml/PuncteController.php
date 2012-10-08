<?php
class UrgentCurier_UrgentShipping_Adminhtml_PuncteController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post['pickup_default']) {
            include('app/code/local/UrgentCurier/UrgentShipping/controllers/urgent_curier.class.php');
            $updateconfigdata = new Mage_Core_Model_Config();
            $updateconfigdata->saveConfig('urgentcurier/config/pickup_default', $post['pickup_default'], 'default', 0);
            Mage::getSingleton('adminhtml/session')->addSuccess('Punctul de ridicare implicit a fost modificat!');
        }
        if ($post['Name']) {
            $soap = Mage::getStoreConfig('urgentcurier/config/soap');
            $idclient = Mage::getStoreConfig('urgentcurier/config/idclient');
            include('app/code/local/UrgentCurier/UrgentShipping/controllers/urgent_curier.class.php');
            $obj_urgent = new urgent_curier();
            $save = $obj_urgent -> SavePickUpSite($soap, $post['IdPickUpSite'], $idclient, $post['Name'], $post['IdOras'], $post['Oras'], $post['Address'], $post['Phone'], $post['Contact'], $post['IdStrada'], $post['NumarStrada'], $post['Strada'], $post['Email'], '1');
            if ($save>0) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Punctul de ridicare a fost salvat!');
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Punctul de ridicare nu a fost salvat.<br />Va rugam sa completati corect toate campurile!');
            }
        }
        
        $this->_redirect('*/*');
    }
}