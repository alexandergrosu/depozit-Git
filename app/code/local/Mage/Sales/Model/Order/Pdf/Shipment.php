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
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);


        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }

			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $shipment->getOrder();

			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0));

            // Add image
            //$this->insertLogo($page, $shipment->getStore());

            // Add address
            $this->insertAddress($page, $shipment->getStore());

			//break invoice increment into series and number
			//$invoice_serie = substr( $shipment->getIncrementId(), 0, 2 );
			//$invoice_number = substr( $shipment->getIncrementId(), 2 );

			// Add invoice number
			$this->_setFontBold($page, 12);
            //$page->drawText(Mage::helper('sales')->__('Series ') . ' ' . $invoice_serie, 265, 814, 'UTF-8');

            //$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

			//$this->_setFontBold($page, 12);
            //$page->drawText(Mage::helper('sales')->__('Invoice '), 265, 800, 'UTF-8');


			// Draw shipment number and date
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			$page->drawRectangle(245, 785, 360, 760);

			$this->_setFontRegular($page, 10);
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $page->drawText(Mage::helper('sales')->__('Number '), 250, 775, 'UTF-8');
            $page->drawText($shipment->getIncrementId(), 285, 775, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 250, 765, 'UTF-8');


            // Add customer
            $this->insertCustomer($page, $order);

			$this->y  = 720;

			// Add head
            //$this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId()));

			// Add table
			//$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            //$page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            //$page->setLineWidth(0.5);
            //$page->drawRectangle(25, $this->y, 570, $this->y -25);


			$subrow_y = $this->y - 25;
			$color = new Zend_Pdf_Color_Html('#f0f0f0');
			$page->setFillColor($color);
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	// nr crt
            $page->drawRectangle(40, $this->y, 330, $subrow_y);	// product
            $page->drawRectangle(330, $this->y, 350, $subrow_y);	// UM
            $page->drawRectangle(350, $this->y, 400, $subrow_y);	// qty
            $page->drawRectangle(400, $this->y, 480, $subrow_y);	// price
            $page->drawRectangle(480, $this->y, 570, $subrow_y);	// subtotal


            // Add table head
			$this->y -=10;
			$this->_setFontRegular($page, 7);
			$font = $page->getFont();
			$size = $page->getFontSize();

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));

			$page->drawText(Mage::helper('sales')->__('Nr.'), 27, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Crt.'), 27, $this->y-10, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product name'), 50, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('U.M.'), 335, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 355, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 460, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 540, $this->y, 'UTF-8');

			$this->y -=15;


			$subrow_y = $this->y - 8;
			//$color = new Zend_Pdf_Color_Html('forestgreen');
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	// nr crt
            $page->drawRectangle(40, $this->y, 330, $subrow_y);	// product
            $page->drawRectangle(330, $this->y, 350, $subrow_y);	// UM
            $page->drawRectangle(350, $this->y, 400, $subrow_y);	// qty
            $page->drawRectangle(400, $this->y, 480, $subrow_y);	// price
            $page->drawRectangle(480, $this->y, 570, $subrow_y);	// subtotal

			//add subrow info
			$this->y -=7;
			$color = new Zend_Pdf_Color_Html('#000000');
			$font = $page->getFont();
			$size = $page->getFontSize();

			$page->setFillColor($color);
			$this->_setFontRegular($page, 6);
			$page->drawText(0, 28, $this->y, 'UTF-8');

			$table_y = $this->y;	// set start y for table lines

			$text = 1;
			$page->drawText($text, $this->getAlignCenter($text, 40, 290, $font, $size), $this->y, 'UTF-8');

			$text = 2;
			$page->drawText($text, $this->getAlignCenter($text, 330, 20, $font, $size), $this->y, 'UTF-8');

			$text = 3;
			$page->drawText($text, $this->getAlignCenter($text, 350, 50, $font, $size), $this->y, 'UTF-8');

			$text = 4;
			$page->drawText($text, $this->getAlignCenter($text, 400, 80, $font, $size), $this->y, 'UTF-8');

			$text = ' 5 (3 x 4)';
			$page->drawText($text, $this->getAlignCenter($text, 480, 90, $font, $size), $this->y, 'UTF-8');

            $this->y -=10;
			$this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            // Add body
			$position = 0;
            foreach ($shipment->getAllItems() as $item){
				if ($item->getOrderItem()->getParentItem()) {
					continue;
                }
				$position++;

                if ($this->y < 100) {
                    $page = $this->newPage(array('table_header' => true));
                }

                // Draw item
                $page = $this->_drawItem($item, $page, $order, $position);
            }

			// Add shipping as new row

			if (!$order->getIsVirtual()) {

				$position++;

				$lineBlock['lines'][] = array(

					array(
						'text' => $position,
						'feed' => 25,
						'width' => 15,
						'align' => 'center',
					),
					array(
						'text'  => $order->getShippingDescription(),
						'feed'  => 50,
						'width' => 290,
						'align' => 'left',
					),
					array(
						'text'  => $order->formatPriceTxt($order->getShippingAmount()),
						'feed'  => 480,
						'width' => 90,
						'font'  => 'bold',
						'align' => 'right'
					),
				);

				$page = $this->drawLineBlocks($page, array($lineBlock));
			}

			// Fill page empty
			if($this->y > 120){
				$this->y = $cur_y = 190;
			} else {
				$cur_y = $this->y;
			}

			// Add table lines on product listing
			$page->setLineWidth(0.5);
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawLine(25,  $table_y, 25,  $cur_y);	// nr crt
            $page->drawLine(40,  $table_y, 40,  $cur_y);	// product
            $page->drawLine(330, $table_y, 330, $cur_y);	// UM
            $page->drawLine(350, $table_y, 350, $cur_y);	// qty
            $page->drawLine(400, $table_y, 400, $cur_y);	// price
            $page->drawLine(480, $table_y, 480, $cur_y);	// subtotal
            $page->drawLine(570, $table_y, 570, $cur_y);	// total

			// Add legal copy
			$invoice_date = Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false);
			$this->insertLegal($page, $invoice_date);


			// Add shipment and total
			$this->insertShipmentAndTotals($page, $order);

            // Add totals
			$this->y +=49;
            $page = $this->insertShipmentTotals($page, $shipment);

            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }

        $this->_afterGetPdf();

        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
		/* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        if (!empty($settings['table_header'])) {
            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));

			$page->drawText(Mage::helper('sales')->__('Nr.'), 27, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Crt.'), 27, $this->y-10, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Products'), 50, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('U.M.'), 335, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 355, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Price'), 460, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 545, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -=20;
        }

        return $page;
    }
}




class Mage_Sales_Model_Order_Pdf_Shipment_1 extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $shipment->getOrder();

            /* Add image */
            $this->insertLogo($page, $shipment->getStore());

            /* Add address */
            $this->insertAddress($page, $shipment->getStore());

            /* Add head */
            $this->insertOrder($page, $shipment, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId()));

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);


            /* Add table head */
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Qty'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Products'), 60, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 470, $this->y, 'UTF-8');

            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($shipment->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                if ($this->y<15) {
                    $page = $this->newPage(array('table_header' => true));
                }

                /* Draw item */
                $page = $this->_drawItem($item, $page, $order);
            }
        }

        $this->_afterGetPdf();

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        if (!empty($settings['table_header'])) {
            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('Qty'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Products'), 60, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('SKU'), 470, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -=20;
        }

        return $page;
    }
}
