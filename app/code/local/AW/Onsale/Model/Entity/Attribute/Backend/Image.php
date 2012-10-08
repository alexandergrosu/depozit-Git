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


class AW_Onsale_Model_Entity_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Enter description here...
     *
     * @param Varien_Object $object
     */
    public function beforeSave($object)
    {
        $value = $object->getData( $this->getAttribute()->getName() );

        if ( is_array($value) && !empty($value['delete']) )
        {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        $path = Mage::getBaseDir('media') . DS . 'onsale' . DS . 'uploaded' . DS;

        try
        {
            $uploader = new Varien_File_Uploader( $this->getAttribute()->getName() );
            $uploader->setAllowedExtensions( array('jpg','jpeg','gif','png') );
            $uploader->setAllowRenameFiles( true );
            $uploader->save( $path );

            $object->setData( $this->getAttribute()->getName(), $uploader->getUploadedFileName() );
            $this->getAttribute()->getEntity()->saveAttribute( $object, $this->getAttribute()->getName() );
        } 
        catch (Exception $e)
        {
            /** @TODO ??? */
            return;
        }
    }
    
    public function afterLoad($object)
    {
        if ( $value =  $object->getData( $this->getAttribute()->getName() ) )
        {
            $object->setData( $this->getAttribute()->getName(), '..' . DS .  '..' . DS . 'onsale' . DS . 'uploaded' . DS . $value );
        }
    }
}
