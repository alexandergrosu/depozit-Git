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

	function setSortOrder(Mage_Eav_Model_Entity_Setup $setup, $entityTypeId, $code, $sortOrder)
	{
		$id = $setup->getAttribute($entityTypeId, $code, 'attribute_id');
		$setup->updateAttribute($entityTypeId, $id, array(), null, $sortOrder);
	}

	$installer = $this;
	$installer->startSetup();

	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

	setSortOrder($setup, 'catalog_product', 'aw_os_product_display',10);
	setSortOrder($setup, 'catalog_product', 'aw_os_product_position', 20);
	setSortOrder($setup, 'catalog_product', 'aw_os_product_image', 30);
	setSortOrder($setup, 'catalog_product', 'aw_os_product_text', 40);

	setSortOrder($setup, 'catalog_product', 'aw_os_category_display', 10);
	setSortOrder($setup, 'catalog_product', 'aw_os_category_position', 20);
	setSortOrder($setup, 'catalog_product', 'aw_os_category_image', 30);
	setSortOrder($setup, 'catalog_product', 'aw_os_category_text', 40);

	$setup->removeAttribute( 'catalog_product', 'aw_os_product_image_path' );
	$setup->removeAttribute( 'catalog_product', 'aw_os_category_image_path' );


    $setup->addAttribute('catalog_product', 'aw_os_product_image_path', array(
            'source'        => 'onsale/entity_attribute_source_imagepath',
            'group'         => 'Product Label',
            'label'         => 'Image Path',
            'note'          => '/img/image.png or http://domian.com/img/image.png',
            'input'         => 'text',
            'class'         => '',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'visible_on_front' => false,
			'sort_order'	=> 35,
        ));

    $setup->addAttribute('catalog_product', 'aw_os_category_image_path', array(
            'source'        => 'onsale/entity_attribute_source_imagepath',
            'group'         => 'Category Label',
            'label'         => 'Image Path',
            'note'          => '/img/image.png or http://domian.com/img/image.png',
            'input'         => 'text',
            'class'         => '',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'visible_on_front' => false,
			'sort_order'	=> 35,
        ));

	$installer->endSetup();