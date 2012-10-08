<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
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
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontRegular($style, 10);


        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }

			$page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $invoice->getOrder();

			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0));

            // Add image
            //$this->insertLogo($page, $invoice->getStore());

            // Add address
            $this->insertAddress($page, $invoice->getStore());

			//break invoice increment into series and number
			$invoice_serie = substr( $invoice->getIncrementId(), 0, 2 );
			$invoice_number = substr( $invoice->getIncrementId(), 2 );


			/*// Add invoice number
			$this->_setFontRegular($page, 12);
            $page->drawText(Mage::helper('sales')->__('Series ') . ' ' . $invoice_serie, 265, 814, 'UTF-8');*/

            //$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page, 15);
            $page->drawText(Mage::helper('sales')->__('Invoice '), 265, 800, 'UTF-8');


			// Draw invoice number and date
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			$page->drawRectangle(245, 785, 360, 760);

			$this->_setFontRegular($page, 10);
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $page->drawText(Mage::helper('sales')->__('Number '), 250, 775, 'UTF-8');
            $page->drawText($invoice_number, 285, 775, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Date: ') . Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'medium', false), 250, 765, 'UTF-8');


            // Add customer
            $this->insertCustomer($page, $order);
			
			
				
			//--------------------------------------------------TVA TOTAL---------------------------------------------grosu alex
			$this->y  = 218;
			$subrow_y = $this->y -30;
			$color = new Zend_Pdf_Color_Html('#f0f0f0');
			$page->setFillColor($color);
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $subrow_y);	    // nr crt
            
           
            $this->y -=10;
			$this->_setFontRegular($page, 8);
			$font = $page->getFont();
			$size = $page->getFontSize();
            $page->setFillColor(new Zend_Pdf_Color_Html('#000000'));
			$text = 'Total general';
			$page->drawText($text, $this->getAlignCenter($text, 497, 90, $font, $size), $this->y, 'UTF-8');
            $text = 'Total fara TVA';
			$page->drawText($text, $this->getAlignCenter($text, 385, 90, $font, $size), $this->y, 'UTF-8');
			$text = 'TVA total';
			$page->drawText($text, $this->getAlignCenter($text, 445, 90, $font, $size), $this->y, 'UTF-8');
			
		//---------------------------------CONVERT STRING TO FLOAT--------------------------------
		
		//----------------------DISCOUNT AMOUNT------------------------
				$discount = $order->formatPriceTxt($order->getDiscountAmount());
				$discount_int = (preg_replace("/,/",".",$discount));
				$float_value_of_discount = floatval(preg_replace("/^[^0-9\.]/","",$discount_int));
				$discountfaratva = $float_value_of_discount/1.24;
				$tvadiscount = $float_value_of_discount-$float_value_of_discount/1.24;
			//----------------------DISCOUNT AMOUNT------------------------
				
				$subtotal=$order->formatPriceTxt($order->getSubtotal());
				$price_fl_point_subtotal=(preg_replace("/,/",".",$subtotal));
        		$float_value_of_pret_subtotal=floatval(preg_replace("/^[^0-9\.]/","",$price_fl_point_subtotal));
				
				$produsfaratva=$float_value_of_pret_subtotal/1.24;
				$tvaprodus=$float_value_of_pret_subtotal-$float_value_of_pret_subtotal/1.24;
				
							//--------------------------------------CONVERT STRING TO FLOAT TRANSPORT--------------------------grosu alex
							$transport=$order->formatPriceTxt($order->getShippingAmount());
							$price_fl_point_transport=(preg_replace("/,/",".",$transport));
        					$float_value_of_pret_transport=floatval(preg_replace("/^[^0-9\.]/","",$price_fl_point_transport));
			
							$transportfaratva=$float_value_of_pret_transport/1.24;
							$tvatransport=$float_value_of_pret_transport-$float_value_of_pret_transport/1.24;
							//--------------------------------------CONVERT STRING TO FLOAT TRANSPORT--------------------------grosu alex
			$totalfaratva=$transportfaratva+$produsfaratva;
			$tvatotal=$tvatransport+$tvaprodus;
		//---------------------------------CONVERT STRING TO FLOAT--------------------------------
		    $grandtotal=$order->formatPriceTxt($order->getGrandTotal());
            $this->y -=15;
			$this->_setFontRegular($page, 11);
			$font = $page->getFont();
			$size = $page->getFontSize();
            $page->setFillColor(new Zend_Pdf_Color_Html('#000000'));
			$text = $grandtotal;
			$page->drawText($text, $this->getAlignCenter($text, 497, 90, $font, $size), $this->y, 'UTF-8');
            $text = round($totalfaratva-$discountfaratva,2);
			$page->drawText($text, $this->getAlignCenter($text, 385, 90, $font, $size), $this->y, 'UTF-8');
			$text = round($tvatotal-$tvadiscount,2);
			$page->drawText($text, $this->getAlignCenter($text, 445, 90, $font, $size), $this->y, 'UTF-8');
           //-----------------------------------------TVA TOTAL---------------------------------------------------grosu alex
			
			$this->y  = 720;
			
			

			// Add head
			$subrow_y = $this->y - 25;
			$color = new Zend_Pdf_Color_Html('#f0f0f0');
			$page->setFillColor($color);
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	    // nr crt
            $page->drawRectangle(40, $this->y, 280, $subrow_y);	    // product
            $page->drawRectangle(280, $this->y, 300, $subrow_y);	// UM
            $page->drawRectangle(300, $this->y, 340, $subrow_y);	// qty
            $page->drawRectangle(340, $this->y, 400, $subrow_y);	// Pret unitar
            $page->drawRectangle(400, $this->y, 460, $subrow_y);	// subtotal
            $page->drawRectangle(460, $this->y, 515, $subrow_y);	// tva/produs
            $page->drawRectangle(515, $this->y, 570, $subrow_y);	// Pret final/buc

            // Add table head
			$this->y -=10;
			$this->_setFontRegular($page, 7);
			$font = $page->getFont();
			$size = $page->getFontSize();

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));
			$page->drawText(Mage::helper('sales')->__('Nr.'), 27, $this->y, 'UTF-8');
		    $page->drawText(Mage::helper('sales')->__('Crt.'), 27, $this->y-10, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product name'), 50, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('U.M.'), 283, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 304, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Pret unitar(lei)'), 345, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal(lei)'), 410, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('TVA/produs(lei)'), 463, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Pret final/buc'), 520, $this->y, 'UTF-8');
			$this->y -=15;


			$subrow_y = $this->y - 8;
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	// nr crt
            $page->drawRectangle(40, $this->y, 280, $subrow_y);	// product
            $page->drawRectangle(280, $this->y, 300, $subrow_y);	// UM
            $page->drawRectangle(300, $this->y, 340, $subrow_y);	// qty
            $page->drawRectangle(340, $this->y, 400, $subrow_y);	// Pret unitar
            $page->drawRectangle(400, $this->y, 460, $subrow_y);	// subtotal
            $page->drawRectangle(460, $this->y, 515, $subrow_y);	// tva/produs
            $page->drawRectangle(515, $this->y, 570, $subrow_y);	// Pret final/buc


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
			$page->drawText($text, $this->getAlignCenter($text, 25, 290, $font, $size), $this->y, 'UTF-8');
			$text = 2;
			$page->drawText($text, $this->getAlignCenter($text, 280, 20, $font, $size), $this->y, 'UTF-8');
			$text = 3;
			$page->drawText($text, $this->getAlignCenter($text, 295, 50, $font, $size), $this->y, 'UTF-8');
			$text = 4;
			$page->drawText($text, $this->getAlignCenter($text, 330, 80, $font, $size), $this->y, 'UTF-8');
			$text = ' 5 (3 x 4)';
			$page->drawText($text, $this->getAlignCenter($text, 385, 90, $font, $size), $this->y, 'UTF-8');
			$text = 6;
			$page->drawText($text, $this->getAlignCenter($text, 445, 90, $font, $size), $this->y, 'UTF-8');
			$text = '7 (4 + 6)';
			$page->drawText($text, $this->getAlignCenter($text, 497, 90, $font, $size), $this->y, 'UTF-8');
            $this->y -=10;
			$this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

			$body_start_y = $this->y;

            // Add body
			$position = 0;
			$cur_page_count = 0;
			
			
			
			
            foreach ($invoice->getAllItems() as $item){

				if ($item->getOrderItem()->getParentItem()) {
					continue;
                }

				$position++;
                if ($this->y < 190) {
                    $page = $this->newPage(array('table_header' => true));
                } else {
					$cur_page_count++;
				}

                // Draw item
                $page = $this->_drawItem($item, $page, $order, $position);
            }


			// Add shipping as new row
			if (!$order->getIsVirtual()) {
				
//----------------------------DISCOUNT AMOUNT-----------------------------
				if($float_value_of_discount == 0) {
					//nimic
				} else {
				$position++;
				$lineBlock['lines'][] = array(
				array(
						'text' => $position,
						'feed' => 25,
						'width' => 15,
						'align' => 'center',
						),
					array(
						'text' => 'Discount',
						'feed' => 23,
						'width' => 90,
						'align' => 'center',
						),
					array(
					 	'text'  => round(-$discountfaratva,2),
            			'feed'  => 320,
						'width' => 80,
            			'font'  => 'bold',
            			'align' => 'right'
            			),
            		array(
					 	'text'  => round(-$discountfaratva,2),
            			'feed'  => 380,
						'width' => 80,
            			'font'  => 'bold',
            			'align' => 'right'
            			),
            		array(
						'text'  => Mage::helper('sales')->__('buc'),
            			'feed'  => 280,
						'width' => 20,
						'align' => 'center'
						),
					array(
						'text'  => 1,
          				'feed'  => 305,
						'width' => 50,
						'align' => 'center'
						),
					array(
						 'text' => round(-$tvadiscount,2),
            			'feed' => 425,
						'width' => 90,
						'font'  => 'bold',
						'align' => 'right'
						),
					array(
						'text' => $discount,
						'feed' => 480,
						'width' => 90,
						'font' => bold,
						'align' => 'right',
					),
				
				);
				
				}
				  //----------------------------DISCOUNT AMOUNT-----------------------------
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
						'text'  => Mage::helper('sales')->__('buc'),
            			'feed'  => 280,
						'width' => 20,
						'align' => 'center'
						),
					array(
						'text'  => 1,
          				'feed'  => 305,
						'width' => 50,
						'align' => 'center'
						),
					array(
					 	'text'  => round($transportfaratva,2),
            			'feed'  => 320,
						'width' => 80,
            			'font'  => 'bold',
            			'align' => 'right'
            			),
            		array(
					 	'text'  => round($transportfaratva,2),
            			'feed'  => 380,
						'width' => 80,
            			'font'  => 'bold',
            			'align' => 'right'
            			),
					array(
						 'text' => round($tvatransport,2),
            			'feed' => 425,
						'width' => 90,
						'font'  => 'bold',
						'align' => 'right'
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



			$total_pages = count($pdf->pages);

			// Fill page empty
			if($this->y > 120 && $total_pages<2 ){
				$this->y = $cur_y = 190;
			} else {
				$cur_y = $this->y;
			}


			// Add table lines on product listing
			$first_page = $pdf->pages[0];
			$page->setLineWidth(0.5);
			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0));



			/*
			$table_y_end = $cur_y;
			if($total_pages>1){
				$table_y_end = $table_y - $cur_page_count * 20 - 4;

				$table_y_end = 20;

				//draw bottom line on first page
				$first_page->drawLine(25,  $table_y_end, 570,  $table_y_end);
			}

			$first_page->drawLine(25,  $table_y, 25,  $table_y_end);	// nr crt
			$first_page->drawLine(40,  $table_y, 40,  $table_y_end);	// product
			$first_page->drawLine(330, $table_y, 330, $table_y_end);	// UM
			$first_page->drawLine(350, $table_y, 350, $table_y_end);	// qty
			$first_page->drawLine(400, $table_y, 400, $table_y_end);	// Pret unitar
			$first_page->drawLine(480, $table_y, 480, $table_y_end);	// subtotal
			$first_page->drawLine(570, $table_y, 570, $table_y_end);	// total
			*/

			//$table_y = 792;
			for($i = 0; $i < count($pdf->pages); $i++){

				if($i != 0)
					$table_y = 792;

				$table_y_end = 10;
				$_tmp_page = $pdf->pages[$i];

				if($i == count($pdf->pages)-1){
					$cur_page_items = $position - $cur_page_count;
					$table_y_end = $this->y - $cur_page_items * 20;
				}

				//draw bottom border
				$_tmp_page->drawLine(25,  $table_y_end, 570,  $table_y_end);


				$_tmp_page->drawLine(25,  $table_y, 25,  $table_y_end);	// nr crt
				$_tmp_page->drawLine(40,  $table_y, 40,  $table_y_end);	// product
				$_tmp_page->drawLine(280, $table_y, 280, $table_y_end);	// UM
				$_tmp_page->drawLine(300, $table_y, 300, $table_y_end);	// qty
				$_tmp_page->drawLine(340, $table_y, 340, $table_y_end);	// Pret unitar
				$_tmp_page->drawLine(400, $table_y, 400, $table_y_end);	// subtotal
				$_tmp_page->drawLine(460, $table_y, 460, $table_y_end);	// total
				$_tmp_page->drawLine(515, $table_y, 515, $table_y_end);	// valoare tva /produs
				$_tmp_page->drawLine(570, $table_y, 570, $table_y_end);	// Pret final/buc
				
			
				
				

			}

			//exit;

			/*
			if($total_pages>1){
				$second_page = $pdf->pages[1];
				//get table y start and end
				$table_y = 792;
				$second_page_items = $position-$cur_page_count;
				$table_y_end = $this->y - $second_page_items * 20;

				$second_page->drawLine(25,  $table_y, 25,  $table_y_end);	// nr crt
				$second_page->drawLine(40,  $table_y, 40,  $table_y_end);	// product
				$second_page->drawLine(300, $table_y, 320, $table_y_end);	// UM
				$second_page->drawLine(320, $table_y, 350, $table_y_end);	// qty
				$second_page->drawLine(400, $table_y, 400, $table_y_end);	// Pret unitar
				$second_page->drawLine(480, $table_y, 480, $table_y_end);	// subtotal
				$second_page->drawLine(570, $table_y, 570, $table_y_end);	// total
			}
			*/

			// Add legal copy
			$invoice_date = Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'medium', false);
			$this->insertLegal($page, $invoice_date);


			// Add shipment and total
			$this->insertShipmentAndTotals($page, $order, $invoice->getCreatedAt());

            // Add totals
			$this->y +=49;
            $page = $this->insertTotals($page, $invoice);

            if ($invoice->getStoreId()) {
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
        $this->y = 820;

        if (!empty($settings['table_header'])) {

			/*
			$this->_setFontRegular($page, 7);
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));

			$page->drawText(Mage::helper('sales')->__('Nr.'), 27, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Crt.'), 27, $this->y-10, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product name'), 50, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('U.M.'), 335, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 355, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Pret unitar'), 460, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal'), 540, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -=20;
			*/

			$subrow_y = $this->y - 25;
			$color = new Zend_Pdf_Color_Html('#f0f0f0');
			$page->setFillColor($color);
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	// nr crt
            $page->drawRectangle(40, $this->y, 280, $subrow_y);	// product
            $page->drawRectangle(280, $this->y, 300, $subrow_y);	// UM
            $page->drawRectangle(300, $this->y, 340, $subrow_y);	// qty
            $page->drawRectangle(340, $this->y, 400, $subrow_y);	// Pret unitar
            $page->drawRectangle(400, $this->y, 460, $subrow_y);	// subtotal
            $page->drawRectangle(460, $this->y, 515, $subrow_y);	// tva/produs
            $page->drawRectangle(515, $this->y, 570, $subrow_y);	// Pret final/buc

            
            
			$this->_setFontRegular($page, 7);
			$font = $page->getFont();
			$size = $page->getFontSize();

			$this->y -= 10;
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.4, 0.4, 0.4));
		    $page->drawText(Mage::helper('sales')->__('Nr.'), 27, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Crt.'), 27, $this->y-10, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Product name'), 50, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('U.M.'), 283, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 304, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Pret unitar(lei)'), 350, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Subtotal(lei)'), 420, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('TVA/produs(lei)'), 460, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Pret final/buc'), 520, $this->y, 'UTF-8');
			$this->y -= 11;


			$subrow_y = $this->y - 7;
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 40, $subrow_y);	// nr crt
            $page->drawRectangle(40, $this->y, 280, $subrow_y);	// product
            $page->drawRectangle(280, $this->y, 300, $subrow_y);	// UM
            $page->drawRectangle(300, $this->y, 340, $subrow_y);	// qty
            $page->drawRectangle(340, $this->y, 400, $subrow_y);	// Pret unitar
            $page->drawRectangle(400, $this->y, 460, $subrow_y);	// subtotal
            $page->drawRectangle(460, $this->y, 515, $subrow_y);	// tva/produs
            $page->drawRectangle(515, $this->y, 570, $subrow_y);	// Pret final/buc

			//add subrow info
			$this->y -=6;
			$color = new Zend_Pdf_Color_Html('#000000');
			$page->setFillColor($color);
			$this->_setFontRegular($page, 6);
			$page->drawText(0, 28, $this->y, 'UTF-8');
			$table_y = $this->y;	// set start y for table lines

			$text = 1;
			$page->drawText($text, $this->getAlignCenter($text, 40, 290, $font, $size), $this->y, 'UTF-8');
			$text = 2;
			$page->drawText($text, $this->getAlignCenter($text, 300, 20, $font, $size), $this->y, 'UTF-8');
			$text = 3;
			$page->drawText($text, $this->getAlignCenter($text, 320, 50, $font, $size), $this->y, 'UTF-8');
			$text = 4;
			$page->drawText($text, $this->getAlignCenter($text, 360, 80, $font, $size), $this->y, 'UTF-8');
			$text = ' 5 (3 x 4)';
			$page->drawText($text, $this->getAlignCenter($text, 420, 90, $font, $size), $this->y, 'UTF-8');

			$this->y -=10;

        }

        return $page;
    }
}
