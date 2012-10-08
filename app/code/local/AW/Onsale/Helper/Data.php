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

/**
 * On Sale Data Helper
 */
class AW_Onsale_Helper_Data extends Mage_Core_Helper_Abstract 
{
    /**
     * Cached collection
     * @var Mage_Catalog_Model_Eav_Resource_Product_Collection
     */
    protected $_collection;

    /**
     * Category id
     * @var int|string
     */
    protected $_categoryId;

    /**
     * Default attributes for select with product
     * @var array
     */
    protected $_attributesToSelect = array(
                    'aw_os_product_display',
                    'aw_os_product_image',
                    'aw_os_product_text',
                    'aw_os_product_position',
                    'aw_os_category_display',
                    'aw_os_category_image',
                    'aw_os_category_text',
                    'aw_os_category_position' );

    /**
     * Retrives Product label html
     * (Deprecated from 2.0 Saved for backfunctionality)
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
	public function getProductOnsaleLabelHtml($product) 
    {
        return $this->getProductLabelHtml( $product );
    }
        
    /**
     * Retrives Category label html
     * (Deprecated from 2.0 Saved for backfunctionality)
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
	public function getCategoryOnsaleLabelHtml($product) 
    {
        return $this->getCategoryLabelHtml( $product );
	}

    /**
     * Retrives Product label html
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductLabelHtml( $product )
    {
        return Mage::getSingleton('core/layout')
                ->createBlock('onsale/product_label')
                ->setTemplate('onsale/product/label.phtml')
                ->setProductFlag()
                ->setProduct($product)
                ->toHtml();
    }

    /**
     * Retrives Category label html
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getCategoryLabelHtml( $product )
    {
        return Mage::getSingleton('core/layout')
                ->createBlock('onsale/product_label')
                ->setTemplate('onsale/category/label.phtml')
                ->setCategoryFlag()
                ->setProduct($product)
                ->toHtml();
    }

    /**
     * Set up category id
     * @param int|string $categoryId
     * @return AW_Onsale_Helper_Data
     */
    public function setCategoryId( $categoryId )
    {
        $this->_categoryId = $categoryId;
        return $this;
    }

    /**
     * Retrives product collection for this category
     * @return Mage_Catalog_Model_Eav_Resource_Product_Collection
     */
    public function getCollection()
    {
        if ( ! $this->_collection )
        {
            $this->_collection = Mage::getModel('catalog/product')
                                 ->getCollection()
                                 ->setStoreId( Mage::app()->getStore()->getId() )
                                 ->addCategoryFilter( Mage::getSingleton('catalog/category')->setId( $this->_categoryId ) )
                                 ->addAttributeToSelect( Mage::getSingleton('catalog/config')->getProductAttributes() );
                                 ;
            if ( count( $this->_attributesToSelect ) )
            {
                foreach ( $this->_attributesToSelect as $code )
                {
                    $this->_collection->addAttributeToSelect( $code );
                }
            }
        }
        return $this->_collection;
    }

    /**
     * Retrives product instance
     * @param int|string $id
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct( $id )
    {
        return Mage::getModel('catalog/product')->load( $id );
    }

    /**
     * Retrives configuration from product attributes
     * @param string $route
     * @param string $name
     * @param int|string $productId
     * @return mixed
     */
    public function confGetEavValue( $route, $name, $productId = null )
    {
        if ( $product = $this->getProduct( $productId ) )
        {
            return $product->getData('aw_os_'.$route.'_'.$name);
        }
        else
        {
            return '';
        }
    }

    /**
     * Retrives configuration from all labels
     * @param string $type
     * @param string $route
     * @param string $name
     * @param int|string $productId
     * @return mixed
     */
    public function confGetCustomValue( $route, $type, $name, $productId = null )
    {
        if ( $route && $type && $name )
        {
            if ( $type === AW_Onsale_Block_Product_Label::TYPE_CUSTOM )
            {
                return $this->confGetEavValue( $route, $name, $productId );
            }
            else
            {
                return Mage::getStoreConfig("onsale/".$route."_".$type."_label/".$name);
            }
            return Mage::getStoreConfig("onsale/".$route."_".$type."_label/".$name);
        }
        else
        {
            return null;
        }
    }

    /**
     * Retrives product attribute
     * @param string $code
     * @param int|string $productId
     * @return mixed
     */
    public function getAttribute( $code, $productId )
    {
        if (($product = $this->getProduct($productId)) && ($attributes = $product->getAttributes()) && count($attributes)){
            foreach($attributes as $attribute){
                if ($attribute->getAttributeCode() == $code){
                    return $attribute->getFrontend()->getValue($product);
                }
            }
        }
        return null;
    }

    /**
     * Retrives stock attribute
     * @param string $code
     * @param int|string $productId
     * @return mixed
     */
    public function getStockAttribute( $code, $productId )
    {
        $stock_item = new Mage_CatalogInventory_Model_Stock_Item();
        $stock_item->assignProduct( $this->getProduct( $productId ) );
        if ( $stock = $this->getAttribute( 'stock_item', $productId ) )
        {
            return $stock->getData($code);
        }
        else
        {
            return null;
        }
    }

    /**
     * Retrives custom product attribute
     * @param string $code
     * @param int|string $productId
     * @return mixed
     */
    public function getCustomAttributeValue( $attribute, $productId )
    {
        $this->getCollection()->addAttributeToSelect( $attribute )->_loadAttributes()   ;
        return $this->getAttribute( $attribute, $productId );
    }
}