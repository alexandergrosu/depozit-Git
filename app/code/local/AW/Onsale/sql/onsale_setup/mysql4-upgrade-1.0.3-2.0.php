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
	$installer = $this;
	
	$installer->startSetup();
	
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	
	$setup->removeAttribute( 'catalog_product', 'aw_os_product_display' );
	$setup->removeAttribute( 'catalog_product', 'aw_os_product_position' );
	$setup->removeAttribute( 'catalog_product', 'aw_os_product_image' );
	$setup->removeAttribute( 'catalog_product', 'aw_os_product_text' );
	$setup->removeAttribute( 'catalog_product', 'aw_os_category_display' );
    $setup->removeAttribute( 'catalog_product', 'aw_os_category_position' );
    $setup->removeAttribute( 'catalog_product', 'aw_os_category_image' );
    $setup->removeAttribute( 'catalog_product', 'aw_os_category_text' );

    $setup->addAttribute('catalog_product', 'aw_os_product_display', array(
            'backend'       => 'onsale/entity_attribute_backend_boolean_config',
            'source'        => 'onsale/entity_attribute_source_boolean_config',
            'label'         => 'Display',
            'group'         => 'Product Label',
            'input'         => 'select',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => true,
            'user_defined'  => false,
            'default'       => '0',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_product_position', array(
            'backend'       => 'onsale/entity_attribute_backend_position',
            'source'        => 'onsale/entity_attribute_source_position',
            'label'         => 'Position',
            'group'         => 'Product Label',
            'input'         => 'select',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => true,
            'user_defined'  => false,
            'default'       => 'BR',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_product_image', array(
            'backend'       => 'onsale/entity_attribute_backend_image',
            'label'         => 'Image',
            'group'			=> 'Product Label',
            'input'         => 'image',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'default'       => '',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_product_text', array(
            'source'        => 'onsale/entity_attribute_source_text',
            'group'         => 'Product Label',
            'label'         => 'Text',
            'note'          => 'You can use predefined values in this field. Please refer to extension manual.',
            'input'         => 'text',
            'class'         => '',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'default'       => 'CUSTOM',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_category_display', array(
            'backend'       => 'onsale/entity_attribute_backend_boolean_config',
            'source'        => 'onsale/entity_attribute_source_boolean_config',
            'label'         => 'Display',
            'group'         => 'Category Label',
            'input'         => 'select',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => true,
            'user_defined'  => false,
            'default'       => '0',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_category_position', array(
            'backend'       => 'onsale/entity_attribute_backend_position',
            'source'        => 'onsale/entity_attribute_source_position',
            'label'         => 'Position',
            'group'         => 'Category Label',
            'input'         => 'select',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => true,
            'user_defined'  => false,
            'default'       => 'BR',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_category_image', array(
            'backend'       => 'onsale/entity_attribute_backend_image',
            'label'         => 'Image',
            'group'         => 'Category Label',
            'input'         => 'image',
            'class'         => 'validate-digit',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'default'       => '',
            'visible_on_front' => false
        ));

    $setup->addAttribute('catalog_product', 'aw_os_category_text', array(
            'source'        => 'onsale/entity_attribute_source_text',
            'group'         => 'Category Label',
            'label'         => 'Text',
            'note'          => 'You can use predefined values in this field. Please refer to extension manual.',
            'input'         => 'text',
            'class'         => '',
            'global'        => true,
            'visible'       => true,
            'required'      => false,
            'user_defined'  => false,
            'default'       => 'CUSTOM',
            'visible_on_front' => false
        ));
	
	$installer->endSetup();