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
class AW_Onsale_Model_System_Config_Backend_Percent extends Mage_Core_Model_Config_Data
{
	/*
	 * Corrects wrong values
	 */
    protected function _beforeSave()
    {
        $value = $this->getValue();
		if ($value === null)
		{
			$value = 0;
		}
		if ($value && !is_numeric($value))
		{
			$value = 0;
		}
		if ($value && is_numeric($value) && ($value < 0) )
		{
			$value = 0;
		}
		if ($value && is_numeric($value) && ($value > 100) )
		{
			$value = 100;
		}
		$this->setValue($value);
        return $this;
    }
}



