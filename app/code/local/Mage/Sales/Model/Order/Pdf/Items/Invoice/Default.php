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
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
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
		$lines[1][] = array(
            'text' => $position,
            'feed' => 25,
			'width' => 15,
			'align' => 'center',
        );

		$_tmp_name = Mage::helper('core/string')->str_split($item->getName(), 40, true, true);
		$item_name = $_tmp_name[0] . ' ' . $item_color;

		//split prod name
		//$name = Mage::helper('core/string')->splitWords($item->getName(), true);

		//-----------------------------------CONVERSION FROM STRING TO FLOAT-------------------------------grosu alex
		$pret=$order->formatPriceTxt($item->getPrice());
		$price_fl_point=(preg_replace("/,/",".",$pret));
        $float_value_of_pret=floatval(preg_replace("/^[^0-9\.]/","",$price_fl_point));
		/*$float_value_of_pret = floatval(ereg_replace("[^-0-9\.]","",$pret));*/
		$pret_rand=$order->formatPriceTxt($item->getRowTotal());
		$price_fl_point_rand=(preg_replace("/,/",".",$pret_rand));
        $float_value_of_pret_rand=floatval(preg_replace("/^[^0-9\.]/","",$price_fl_point_rand));
		//-----------------------------------CONVERSION FROM STRING TO FLOAT-------------------------------grosu alex

        // draw Product name
        $lines[1][] = array(
            'text' => $item_name,
            'feed' => 50,
			'width' => 280,
			'align' => 'left',
        );

        // draw UM
        $lines[1][] = array(
            'text'  => Mage::helper('sales')->__('buc'),
            'feed'  => 280,
			'width' => 20,
			'align' => 'center'
        );

        // draw QTY
        $lines[1][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 305,
			'width' => 50,
			'align' => 'center'
        );

        // draw Pret unitar
        $lines[1][] = array(
            'text'  => round($float_value_of_pret/1.24,2),
            'feed'  => 320,
			'width' => 80,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Subtotal
        $lines[1][] = array(
            'text'  => round($float_value_of_pret_rand/1.24,2),
            'feed'  => 370,
			'width' => 90,
            'font'  => 'bold',
            'align' => 'right'
        );
		
		//draw tva
		$lines[1][] = array(
            'text' => round($float_value_of_pret-$float_value_of_pret/1.24,2),
            'feed' => 425,
			'width' => 90,
			'font'  => 'bold',
			'align' => 'right',
        );
		
		//draw pret final
		$lines[1][] = array(
            'text' => $order->formatPriceTxt($item->getPrice()),
            'feed' => 480,
			'width' => 90,
			'font'  => 'bold',
			'align' => 'right',
        );

        //make row bold if qty > 1
        if($item->getQty()*1 > 1){
            foreach($lines[0] as $k=>$v){
                if(is_array($v)){
                    $lines[0][$k]['font'] = 'bold';
                }
            }
        }

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
