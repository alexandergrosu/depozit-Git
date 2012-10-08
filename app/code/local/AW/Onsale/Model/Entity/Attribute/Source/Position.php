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
class AW_Onsale_Model_Entity_Attribute_Source_Position extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrive all attribute options
     * @return array
     */
    public function getAllOptions()
    {
        if ( ! $this->_options  )
        {
            $this->_options = array(
                array(
                        'value' => 'TL',
                        'label' => Mage::helper('adminhtml')->__('Top-Left')
                ),
                array(
                        'value' => 'TC',
                        'label' => Mage::helper('adminhtml')->__('Top-Center')
                ),
                array(
                        'value' => 'TR',
                        'label' => Mage::helper('adminhtml')->__('Top-Right')
                ),
                array(
                        'value' => 'BL',
                        'label' => Mage::helper('adminhtml')->__('Bottom-Left')
                ),
                array(
                        'value' => 'BC',
                        'label' => Mage::helper('adminhtml')->__('Bottom-Center')
                ),
                array(
                        'value' => 'BR',
                        'label' => Mage::helper('adminhtml')->__('Bottom-Right')
                )
            );
        }
        return $this->_options;        
    }
}
