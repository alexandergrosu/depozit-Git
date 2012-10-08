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
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
{
    public $y;
    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID = 'sales_pdf/creditmemo/put_order_id';

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * Retrieve PDF
     *
     * @return Zend_Pdf
     */
    abstract public function getPdf();

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param string $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv('UTF-8', 'UTF-16BE//IGNORE', $string) : @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
		return $stringWidth;
    }

	/**
	 * wrapper for widthForStringUsingFontSize
	 *
	 */
	protected function widthForString($string, $page){

		$font = $page->getFont();
		$fontSize = $page->getFontSize();

		return $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);

	}

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @param int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize)
    {
		$width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    protected function insertLogo(&$page, $store = null)
    {
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getStoreConfig('system/filesystem/media', $store) . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image = Zend_Pdf_Image::imageWithPath($image);
                $page->drawImage($image, 25, 800, 125, 825);
            }
        }
        //return $page;
    }

    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$page->setLineWidth(0);
        $this->y = 820;

		$this->_setFontRegular($page, 9);
		
		$page->drawText(Mage::helper('sales')->__('Furnizor'), 25, 820, 'UTF-8');
		/*$page->drawText(Mage::helper('sales')->__('Furnizor'), 25, $this->y, 'UTF-8');*/
		$page->drawText('S.C. Laptop Direct S.R.L.', 25, $this->y-15, 'UTF-8');
		$page->drawText('str. Stiintei nr. 4, ap. 1, Timisoara', 25, $this->y-25, 'UTF-8');
		$page->drawText('CUI: 2854 9948', 25, $this->y-35, 'UTF-8');
		$page->drawText('J35/1293/2011', 25, $this->y-45, 'UTF-8');
		$page->drawText('RO 09 INGB 0000 999902502476 ING BANK ROMANIA', 25, $this->y-55, 'UTF-8');
		

		$this->_setFontRegular($page, 9);
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
            if ($value!=='') {
                $page->drawText(trim(strip_tags($value)), 25, $this->y-20, 'UTF-8');
                $this->y -=10;
            }
        }
        //return $page;
    }

	protected function insertLegal(&$page, $invoice_date)
	{

		if ($this->y < 60) {
			$page = $this->newPage();
		}

		$this->_setFontRegular($page, 6);

		$pay_date = strftime('%d.%m.%Y', strtotime( '+20 day', strtotime($invoice_date) ) );

		$legal_copy = 'Preturile au fost negociate intre furnizor si beneficiar cf. HG206/93;OMF766/93;HG555/94;HG412/92 si acceptate prin semnatura' . "\n" .
					'CERTIFICAT DE CALITATE SI GARANTIE 30 de zile. Conform Legii 499 / 2003 si OG 21 / 92 atestam calitatea produselor livrate in baza certificatelor de calitate a producatorului.' ."\n" .
					'Termen de plata ' . $pay_date . '. In cazul nerespectarii se aplica o penalizare de 0.5 pe zi de intarziere cf. L469/09.07.2002' . "\n" .
					'Conform art. 155 alin. 6 Cod Fiscal, semnarea si stampilarea facturilor nu sunt obligatorii.'
					;

		// Add legal copy box
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 1));
		$page->setLineWidth(0.5);
		$page->drawRectangle(25, $this->y, 570, $this->y -40);

		$this->y -=10;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		foreach (explode("\n", $legal_copy) as $value){
            if ($value!=='') {
				$page->drawText(trim(strip_tags($value)), 28, $this->y, 'UTF-8');
                $this->y -=7;
            }
        }

	}

    protected function insertCustomer(&$page, $obj)
    {

        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }


		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);

        $page->setLineWidth(0);
		$this->y = 820;

		//$page->drawText(Mage::helper('sales')->__('Comanda: ').$order->getRealOrderId(), 400, 810, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Cumparator'), 370, 820, 'UTF-8');

		$this->y = 800;
		$billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
		
		// daca numele FIRMEI apare in $BILLINGADDRESS atunci nu mai printa pe factura NUME + PRENUME
       // in caz contrar printeaza totul
       $companyNme = $order->getBillingAddress()->getCompany();
	   	$this->_setFontRegular($page, 9);
		if ($billingAddress[0] == $companyNme) {
			$page->drawText($billingAddress[0], 370, 800, 'UTF-8');
			$page->drawText($billingAddress[2], 370, 790, 'UTF-8');
			$page->drawText($billingAddress[3], 370, 780, 'UTF-8');
			$page->drawText($billingAddress[4], 370, 770, 'UTF-8');
			$page->drawText($billingAddress[5], 370, 760, 'UTF-8');
			$page->drawText($billingAddress[6], 370, 750, 'UTF-8');
			$page->drawText($billingAddress[7], 370, 740, 'UTF-8');
		} else {
			$page->drawText($billingAddress[0], 370, 800, 'UTF-8');
			$page->drawText($billingAddress[1], 370, 790, 'UTF-8');
			$page->drawText($billingAddress[2], 370, 780, 'UTF-8');
			$page->drawText($billingAddress[3], 370, 770, 'UTF-8');
			$page->drawText($billingAddress[4], 370, 760, 'UTF-8');
			$page->drawText($billingAddress[5], 370, 750, 'UTF-8');
			$page->drawText($billingAddress[6], 370, 740, 'UTF-8');
			$page->drawText($billingAddress[7], 370, 730, 'UTF-8');
		}
		// sfarsit IF

		/*foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 370, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }*/

    }

	protected function insertShipmentAndTotals(&$page, $obj, $invoice_date)
	{
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

		if ($this->y < 110) {
			$page = $this->newPage();
		}

		$this->y -=2;

		$this->insertStamp($page, 25, 80);	//left
		$this->insertStampRight($page, 350, 70);	//right

		//draw delegat box
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRectangle(105, $this->y, 350, $this->y -70);

		//draw delegat copy
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$this->y -=10;
		$copy = Mage::helper('sales')->__('Delegat Curier Rapid');
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), 108, $this->y, 'UTF-8');

		$shippingMethod  = $order->getShippingDescription();
		$shippingMethod = Mage::helper('sales')->__('');
		$page->drawText($shippingMethod, 110 + $copy_width, $this->y, 'UTF-8');

		$this->y -= 9;
		$x = 108;
		$copy = Mage::helper('sales')->__('B.I./C.I.');
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), $x, $this->y, 'UTF-8');
		$dots = 40;
		for($i = 1; $i<=$dots; $i++){
			$dot_copy = '. ';
			$page->drawText('.  ', $x + $copy_width + $i * strlen($dot_copy), $this->y, 'UTF-8');
		}

		$x = 240;
		$copy = Mage::helper('sales')->__('eliberat de:');
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), $x, $this->y, 'UTF-8');
		$dots = 30;
		for($i = 1; $i<=$dots; $i++){
			$dot_copy = '. ';
			$page->drawText('.  ', $x + $copy_width + $i * strlen($dot_copy), $this->y, 'UTF-8');
		}

		$this->y -=10;
		$copy = Mage::helper('sales')->__('');
		$copy .= '';
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), 128, $this->y, 'UTF-8');

		$this->y -=10;
		$copy = Mage::helper('sales')->__('Data') . ': ';
		$invoice_date = Mage::helper('core')->formatDate($invoice_date, 'medium', false);
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), 128, $this->y, 'UTF-8');
		$page->drawText($invoice_date, 130 + $copy_width, $this->y, 'UTF-8');


		$this->y -=10;
		$x = 108;
		$copy = Mage::helper('sales')->__('Semnatura');
		$copy_width = $this->widthForString($copy, $page);
		$page->drawText(trim(strip_tags($copy)), $x, $this->y, 'UTF-8');
		$dots = 90;
		for($i = 1; $i<=$dots; $i++){
			$dot_copy = '. ';
			$page->drawText('.  ', $x + $copy_width + $i * strlen($dot_copy), $this->y, 'UTF-8');
		}
	}

	protected function insertStamp(&$page, $x, $width)
	{

		// Add stamp box
		$cur_y = $this->y;

		$this->_setFontRegular($page);
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->setLineWidth(0.5);
		$page->drawRectangle($x, $this->y, $x + $width, $this->y -70);

		// Add left stamp copy
		$this->y -=10;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$copy = Mage::helper('sales')->__('Supplier \n sign and \n stamp');
		$copy = "Semnatura si \n stampila \n furnizor";
		
		// generam numele actualului admin user care va fi trecut ca emitent al facturii
		$usrEmitent = Mage::getSingleton('admin/session')->getUser()->getName();
		$usrEmitentY = $this->y - 25;
		// printam numele in casuta de semnatura a furnizorului.
		$page->drawText($usrEmitent, $x+2, $usrEmitentY, 'UTF-8');
		
		foreach (explode("\n", $copy) as $value){
            if ($value!=='') {
				$page->drawText(trim(strip_tags($value)), $x+2, $this->y, 'UTF-8');
                $this->y -=7;
            }
        }
		$this->y = $cur_y;

	}

	protected function insertStampRight(&$page, $x, $width)
	{
	
		// Add stamp box
		$cur_y = $this->y;

		$this->_setFontRegular($page);
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->setLineWidth(0.5);
		$page->drawRectangle($x, $this->y, $x + $width, $this->y -70);

		// Add left stamp copy
		$this->y -=10;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$copy = Mage::helper('sales')->__('Supplier \n sign and \n stamp');
		$copy = "Semnatura \n de \n primire";
		foreach (explode("\n", $copy) as $value){
            if ($value!=='') {
				$page->drawText(trim(strip_tags($value)), $x+2, $this->y, 'UTF-8');
                $this->y -=7;
            }
        }
		$this->y = $cur_y;

	}

	/**
     * Format address
     *
     * @param string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 65, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
		if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }


		// @var $order Mage_Sales_Model_Order
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        $page->drawRectangle(25, 790, 570, 755);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);


        if ($putOrderId) {
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770, 'UTF-8');
        }
        $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false), 35, 760, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 755, 275, 730);
        $page->drawRectangle(275, 755, 570, 730);

        // Calculate blocks info

        // Billing Address
        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));

        // Payment
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value))==''){
                unset($payment[$key]);
            }
        }
        reset($payment);



        // Shipping Address and Method
        if (!$order->getIsVirtual()) {
            // Shipping Address
            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));

            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 , 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 , 'UTF-8');
        }
        else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740 , 'UTF-8');
        }

        if (!$order->getIsVirtual()) {
            $y = 730 - (max(count($billingAddress), count($shippingAddress)) * 10 + 5);
        }
        else {
            $y = 730 - (count($billingAddress) * 10 + 5);
        }


        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, 730, 570, $y);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $this->y = 720;



        foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y = 720;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
                    $this->y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);

            $this->y -=15;
            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = 720;
            $paymentLeft = 285;
        }

        foreach ($payment as $value){
            if (trim($value)!=='') {
                $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
                $yPayments -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y -=15;
			$this->_setFontBold($page);
            $page->drawText($shippingMethod, 285, $this->y, 'UTF-8');

            $yShipments = $this->y;


            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments-7, 'UTF-8');
            $yShipments -=10;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(380, $yShipments, 380, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

                $yShipments -=17;
                $this->_setFontRegular($page, 6);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode!='custom')
                    {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                        $carrierTitle = $carrier->getConfigData('title');
                    }
                    else
                    {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $truncatedTitle = substr($track->getTitle(), 0, 45) . (strlen($track->getTitle()) > 45 ? '...' : '');
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 300, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 395, $yShipments , 'UTF-8');
                    $yShipments -=7;
                }
            } else {
                $yShipments -= 7;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $this->y + 15, 25, $currentY);
            $page->drawLine(25, $currentY, 570, $currentY);
            $page->drawLine(570, $currentY, 570, $this->y + 15);

            $this->y = $currentY;
            $this->y -= 15;
        }

	}


    protected function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    protected function _getTotalsList($source)
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();

        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $index => $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    protected function insertShipmentTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );

		//echo '<pre>'; print_r($order);

		//draw delegat box
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRectangle(430, $this->y, 570, $this->y -70);

		$this->y -=10;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$this->_setFontRegular($page, 15);

        /* foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {


                foreach ($total->getTotalsForDisplay() as $totalData) {
					echo '<pre>'; var_export($totalData);

                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 508,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'] + 1,
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 568,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'] + 3,
                            'font'      => 'bold'
                        ),
                    );
                }
            }
        } */


		$totals = array(
				array (
				  'amount' => strip_tags(Mage::helper('core')->currency($order->getSubtotal())),
				  'label' => 'Subtotal:',
				  'font_size' => '7',
				),
				array (
				  'amount' => strip_tags(Mage::helper('core')->currency($order->getGrandTotal())),
				  'label' => 'Total final:',
				  'font_size' => '8',
				)
		);
		foreach($totals as $totalData){
			$lineBlock['lines'][] = array(
				array(
					'text'      => $totalData['label'],
					'feed'      => 510,
					'align'     => 'right',
					'font_size' => $totalData['font_size'] + 1,
					'font'      => 'bold'
				),
				array(
					'text'      => $totalData['amount'],
					'feed'      => 568,
					'align'     => 'right',
					'font_size' => $totalData['font_size'] + 3,
					'font'      => 'bold'
				),
			);
		}

		//echo '<pre>'; print_r($lineBlock); exit;

        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }

    protected function insertTotals($page, $source){
        $order = $source->getOrder();
        $totals = $this->_getTotalsList($source);
        $lineBlock = array(
            'lines'  => array(),
            'height' => 15
        );

		//draw delegat box
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRectangle(420, $this->y, 570, $this->y -70);

		$this->y -=10;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

		$this->_setFontRegular($page, 15);

        foreach ($totals as $total) {
            $total->setOrder($order)
                ->setSource($source);

            if ($total->canDisplay()) {
                foreach ($total->getTotalsForDisplay() as $totalData) {

                    $lineBlock['lines'][] = array(
                        array(
                            'text'      => $totalData['label'],
                            'feed'      => 510,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'] + 1,
                            'font'      => 'bold'
                        ),
                        array(
                            'text'      => $totalData['amount'],
                            'feed'      => 568,
                            'align'     => 'right',
                            'font_size' => $totalData['font_size'] + 3,
                            'font'      => 'bold'
                        ),
                    );
                }

            }

        }

        $page = $this->drawLineBlocks($page, array($lineBlock));
        return $page;
    }

    protected function _parseItemDescription($item)
    {
        $matches = array();
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return array($description);
    }

    /**
     * Before getPdf processing
     *
     */
    protected function _beforeGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * After getPdf processing
     *
     */
    protected function _afterGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    protected function _formatOptionValue($value, $order)
    {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/'.$type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Public method of protected @see _getRenderer()
     *
     * Retrieve renderer model
     *
     * @param string $type
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function getRenderer($type)
    {
        return $this->_getRenderer($type);
    }

    /**
     * Draw Item process
     *
     * @param Varien_Object $item
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $order
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order, $position)
    {

		$type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);

		$renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->setPosition($position);

        $renderer->draw();

        return $renderer->getPage();
    }


    protected function _setFontRegular($object, $size = 9)
    {
		//$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertineC_Re-2.8.0.ttf');
		//$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/DejaVuFont/DejaVuLGCSans.ttf');

        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($object, $size = 9)
    {
		//$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
		//$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/DejaVuFont/DejaVuLGCSerifCondensed-Bold.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($object, $size = 9)
    {
		//$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD_ITALIC);
		$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
		//$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/DejaVuFont/DejaVuLGCSerifCondensed-Italic.ttf');
        $object->setFont($font, $size);
        return $font;
    }


    /**
     * Set PDF object
     *
     * @param Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }

    /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {

		foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 9 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    }
                    else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];

                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }

                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }
}
