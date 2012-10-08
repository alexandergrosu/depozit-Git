<?php
class UrgentCurier_UrgentShipping_Adminhtml_ConfigController extends Mage_Adminhtml_Controller_Action
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
            include('app/code/local/UrgentCurier/UrgentShipping/controllers/urgent_curier.class.php');
            $obj_urgent = new urgent_curier();
            $check = $obj_urgent -> CheckClientCredentials($post['soap'], $post['idclient'], $post['idtarifset'], $post['iduser']);
            if ($check) {
                $updateconfigdata = new Mage_Core_Model_Config();
                $updateconfigdata->saveConfig('urgentcurier/config/soap', $post['soap'], 'default', 0);
                $updateconfigdata->saveConfig('urgentcurier/config/iduser', $post['iduser'], 'default', 0);
                $updateconfigdata->saveConfig('urgentcurier/config/idclient', $post['idclient'], 'default', 0);
                $updateconfigdata->saveConfig('urgentcurier/config/idtarifset', $post['idtarifset'], 'default', 0);
                $updateconfigdata->saveConfig('urgentcurier/config/idclientexp', $post['idclientexp'], 'default', 0);
                $updateconfigdata->saveConfig('urgentcurier/config/clientexp', $post['clientexp'], 'default', 0);
                include('app/code/local/UrgentCurier/UrgentShipping/sql/mysql4-install-1.0.0.php');
                Mage::getSingleton('adminhtml/session')->addSuccess('Setarile au fost salvate!');
            } else {
                Mage::throwException('Toate campurile sunt obligatorii!');
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }
}