<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Shipment Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
		$position = $this->getPosition();


        $lines  = array();
		$_product = Mage::getModel('catalog/product')->load($item->getProductId());
		$item_color = $_product->getColor();
		$show_wahrehouse = false;
		if( $_product->getWarehousePlace() ){
			$show_wahrehouse = true;
			$warehouse_place = $_product->getWarehousePlace();
		}

        // draw increment
		$lines[0][] = array(
            'text' => $position,
            'feed' => 25,
			'width' => 15,
			'align' => 'center',
        );

		$_tmp_name = Mage::helper('core/string')->str_split($item->getName(), 40, true, true);
		$item_name = $_tmp_name[0] . ' ' . $item_color;

        // draw Product name
        $lines[0][] = array(
            'text' => $item_name,
            'feed' => 50,
			'width' => 290,
			'align' => 'left',
        );

        // draw UM
        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('buc'),
            'feed'  => 330,
			'width' => 20,
			'align' => 'center'
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 350,
			'width' => 50,
			'align' => 'center'
        );

        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 400,
			'width' => 80,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt( $item->getQty()* $item->getPrice() * 1 ),
            'feed'  => 480,
			'width' => 90,
            'font'  => 'bold',
            'align' => 'right'
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {

			foreach ($options as $option) {
                // draw options label

				/*
				$lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic1',
                    'feed' => 55
                );
				*/

				$str = Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true);
				$text = $str[0];


                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
						/*
						$lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 60
                        );
						*/

						$text .= ': ';
						$str = Mage::helper('core/string')->str_split($value, 50, true, true);
						$text .= $str[0];
                    }
                }

				$lines[][] = array(
					'text' => $text,
					'font' => 'italic',
					'feed' => 55
				);

            }
        }

        if($show_wahrehouse) {// draw Warehouse Place
			$lines[][] = array(
				'text' => 'Raft ' . $warehouse_place,
				'feed' => 55,
				'font' => 'italic',
				'width' => 290,
				'align' => 'left',
			);
		}

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );


        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}


class Mage_Sales_Model_Order_Pdf_Items_Shipment_Default_1 extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => 60,
        ));

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 35
        );

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 440
        );

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 60
                );

                // draw options value
                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 65
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
