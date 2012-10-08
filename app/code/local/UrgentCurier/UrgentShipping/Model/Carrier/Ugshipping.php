<?php
class UrgentCurier_UrgentShipping_Model_Carrier_Ugshipping extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'ugshipping';
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        
        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }
 
        $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        if($show){
            
            $cost_fix_expeditie = Mage::getStoreConfig('urgentcurier/config/cost_fix_expeditie');
            if ($cost_fix_expeditie=='') {
                require_once('app/code/local/UrgentCurier/UrgentShipping/controllers/urgent_curier.class.php');
                $obj_urgent = new urgent_curier();
                
                $soap = Mage::getStoreConfig('urgentcurier/config/soap');
                $idClient = Mage::getStoreConfig('urgentcurier/config/idclient');
                $idTarifSet = Mage::getStoreConfig('urgentcurier/config/idtarifset');
                $Greutate = round($request->getPackageWeight());
                    if ($Greutate < 1) $Greutate = 1;
                $asigurare = Mage::getStoreConfig('urgentcurier/config/asigurare');
                $valoareTransport = round($request->getPackageValue(), 2);
                    if (!$asigurare) $asigurare = 'da';
                    if ($asigurare=='da') {
                        $ValoareDeclarata = $valoareTransport;
                    } else {
                        $ValoareDeclarata = 0;
                    }
                $tip_ramburs = Mage::getStoreConfig('urgentcurier/config/tip_ramburs');
                    if (!$tip_ramburs) $tip_ramburs = 'cont';
                    if ($tip_ramburs=='cont') {
                        $RambursContColector = round($request->getPackageValue(), 2);
                        $RambursCash = 0;
                        $TipCerere = 2;
                        $RambursValoare = $RambursContColector;
                    } else {
                        $RambursContColector = 0;
                        $RambursCash = round($request->getPackageValue(), 2);
                        $TipCerere = 1;
                        $RambursValoare = $RambursCash;
                    }
                $pickup_default = Mage::getStoreConfig('urgentcurier/config/pickup_default');
                $pickup = $obj_urgent -> GetPickUpSites_Simple($soap, $idClient);
                if (!array_key_exists($pickup_default, $pickup)) $pickup_default = key($pickup);
                
                $pickup = $obj_urgent -> GetPickUpSite($soap, $pickup_default);
                $OrasExpId = $pickup['IdOras'];
                $OrasExp = $pickup['Oras'];
                $citycode = $request->getDestCitycode();
                
                if ($citycode) {
                    $rezultat_cost = $obj_urgent -> CalculateAwbSite($soap, $idClient, $idTarifSet, $ValoareDeclarata, 0, 1, $Greutate, $OrasExpId, $citycode, $RambursValoare, $TipCerere);
                } else {
                    $rezultat_cost['ValoareTotala'] = Mage::getStoreConfig('urgentcurier/config/cost_default_expeditie');
                    $rezultat_cost['TotalCostRamburs'] = 0;
                }
                
                $plata_expeditie = Mage::getStoreConfig('urgentcurier/config/plata_expeditie');
                    if (!$plata_expeditie) $plata_expeditie = '1';
                $plata_ramburs = Mage::getStoreConfig('urgentcurier/config/plata_ramburs');
                    if (!$plata_ramburs) $plata_ramburs = '2';
                $plafon_plata_dest = Mage::getStoreConfig('urgentcurier/config/plafon_plata_dest');
                    if ($valoareTransport > $plafon_plata_dest && $plafon_plata_dest>0) {
                        $plata_expeditie = '1';
                        $plata_ramburs = '2';
                    }
                if ($plata_expeditie!=1 && $plata_ramburs!=2) $cost_fix_expeditie = $rezultat_cost['ValoareTotala'];
                if ($plata_expeditie==1 && $plata_ramburs!=2) $cost_fix_expeditie = $rezultat_cost['TotalCostRamburs'];
                if ($plata_expeditie!=1 && $plata_ramburs==2) $cost_fix_expeditie = $rezultat_cost['ValoareTotala']-$rezultat_cost['TotalCostRamburs'];
                if ($plata_expeditie==1 && $plata_ramburs==2) $cost_fix_expeditie = 0;
                
            }
 
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setMethod($this->_code);
            $method->setCarrierTitle('Curier rapid');
            $method->setMethodTitle('Urgent Curier');
            $method->setPrice($cost_fix_expeditie);
            $method->setCost($cost_fix_expeditie);
            $result->append($method);
 
        }else{
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle('Urgent Curier');
            $error->setErrorMessage('Momentan, aceasta metoda de livrare nu este disponibila!');
            $result->append($error);
        }
        return $result;
    }
    public function getAllowedMethods()
    {
        return array('ugshipping'=>'Urgent Curier');
    }
}