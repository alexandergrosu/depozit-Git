<?php
    $installer = new Mage_Customer_Model_Entity_Setup('install');
    //$installer = $this;
    $installer->startSetup();
    /* @var $addressHelper Mage_Customer_Helper_Address */
    $addressHelper = Mage::helper('customer/address');
    $store         = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);
     
    /* @var $eavConfig Mage_Eav_Model_Config */
    $eavConfig = Mage::getSingleton('eav/config');
     
    // update customer address user defined attributes data
    $attributes = array(
        'citycode'           => array(   
            'label'    => 'citycode',
            'type'     => 'varchar',
            'input'    => 'text',
            'frontend_input'    => 'hidden',
            'backend_type'      => 'varchar',
            'is_user_defined'   => 0,
            'is_system'         => 0,
            'is_visible'        => 1,
            'sort_order'        => 140,
            'is_required'       => 0,
            'multiline_count'   => 0,
            'validate_rules'    => array(
                'max_text_length'   => 255,
                'min_text_length'   => 1
            ),
        ),
    );
     
    foreach ($attributes as $attributeCode => $data) {
        $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
        $attribute->setWebsite($store->getWebsite());
        $attribute->addData($data);
            $usedInForms = array(
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address'
            );
            $attribute->setData('used_in_forms', $usedInForms);
        $attribute->save();
    }
    
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $results = $readConnection->fetchAll("SHOW TABLES LIKE 'urgent_curier'");
    if (!is_array($results[0])) {
    $installer->run("
        CREATE TABLE IF NOT EXISTS `urgent_curier` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `urgent_order_id` int(11) NOT NULL,
            `urgent_awb_id` int(11) NOT NULL,
            `urgent_awb_codbara` int(11) NOT NULL,
            `id_comanda` varchar(25) NOT NULL,
            `data_comanda` datetime NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `pickup_id` int(11) NOT NULL,
            `destinatar` varchar(100) NOT NULL,
            `dest_judet` varchar(50) NOT NULL,
            `dest_judet_id` int(11) NOT NULL,
            `dest_localitate` varchar(100) NOT NULL,
            `dest_localitate_id` int(11) NOT NULL,
            `dest_strada` varchar(100) NOT NULL,
            `dest_strada_id` int(11) NOT NULL,
            `dest_strada_nr` varchar(10) NOT NULL,
            `dest_adresa` varchar(200) NOT NULL,
            `dest_pers_contact` varchar(100) NOT NULL,
            `dest_telefon` varchar(11) NOT NULL,
            `plicuri` int(11) NOT NULL,
            `colete` int(11) NOT NULL,
            `kilograme` int(11) NOT NULL,
            `valoare_declarata` decimal(10,2) NOT NULL,
            `moneda` varchar(50) NOT NULL,
            `plata_ramburs` varchar(50) NOT NULL,
            `plata_expeditie` varchar(50) NOT NULL,
            `livrare_sambata` int(1) NOT NULL,
            `continut_expeditie` text NOT NULL,
            `observatii` text NOT NULL,
            `ramburs_numerar` decimal(10,2) NOT NULL,
            `ramburs_cont_colector` decimal(10,2) NOT NULL,
            `ramburs_alt_tip` varchar(200) NOT NULL,
            `status` varchar(10) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `id_comanda` (`id_comanda`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
        ALTER TABLE {$installer->getTable('sales_flat_quote_address')} ADD COLUMN `citycode` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `city`;
        ALTER TABLE {$installer->getTable('sales_flat_order_address')} ADD COLUMN `citycode` VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL AFTER `city`;
        ");
        }
    $installer->endSetup();
?>