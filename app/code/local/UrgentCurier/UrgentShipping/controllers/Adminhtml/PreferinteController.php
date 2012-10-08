<?php
class UrgentCurier_UrgentShipping_Adminhtml_PreferinteController extends Mage_Adminhtml_Controller_Action
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
            $updateconfigdata = new Mage_Core_Model_Config();
            $updateconfigdata->saveConfig('urgentcurier/config/PickUpDateFrom', $post['PickUpDateFrom'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/PickUpDateUntil', $post['PickUpDateUntil'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/livrare_sambata', $post['livrare_sambata'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/plata_expeditie', $post['plata_expeditie'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/plata_ramburs', $post['plata_ramburs'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/asigurare', $post['asigurare'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/tip_ramburs', $post['tip_ramburs'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/plafon_plata_dest', $post['plafon_plata_dest'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/cost_default_expeditie', $post['cost_default_expeditie'], 'default', 0);
            $updateconfigdata->saveConfig('urgentcurier/config/cost_fix_expeditie', $post['cost_fix_expeditie'], 'default', 0);
            Mage::getSingleton('adminhtml/session')->addSuccess('Preferintele au fost salvate!');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }
}