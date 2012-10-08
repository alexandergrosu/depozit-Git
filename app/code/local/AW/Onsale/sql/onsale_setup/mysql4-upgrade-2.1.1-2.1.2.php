<?php
	/**
	 * aheadWorks Co.
	 *
	 * NOTICE OF LICENSE
	 *
	 * This source file is subject to the EULA
	 * that is bundled with this package in the file LICENSE.txt.
	 * It is also available through the world-wide-web at this URL:
	 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
	 *
	 * @category   AW
	 * @package    AW_Onsale
	 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
	 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
	 */
?>
<?php

	function updateScoupe(Mage_Eav_Model_Entity_Setup $setup, $entityTypeId, $code, $value)
	{
		$id = $setup->getAttribute($entityTypeId, $code, 'attribute_id');
		$setup->updateAttribute($entityTypeId, $id, 'is_global', $value);
	}

	$installer = $this;
	$installer->startSetup();

	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

	updateScoupe($setup, 'catalog_product', 'aw_os_product_display', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_product_position', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_product_image', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_product_text', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_category_display', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_category_position', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_category_image', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_category_text', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_product_image_path', 0);
	updateScoupe($setup, 'catalog_product', 'aw_os_category_image_path', 0);

	$installer->endSetup();