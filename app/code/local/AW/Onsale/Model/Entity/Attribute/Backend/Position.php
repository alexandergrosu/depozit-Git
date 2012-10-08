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

class AW_Onsale_Model_Entity_Attribute_Backend_Position extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set attribute default value if value empty
     *
     * @param Varien_Object $object
     */
    public function afterLoad($object)
    {
        if( ! $object->hasData( $this->getAttribute()->getAttributeCode() ) )
        {
            $object->setData( $this->getAttribute()->getAttributeCode(), $this->getDefaultValue() );
        }
    }
}