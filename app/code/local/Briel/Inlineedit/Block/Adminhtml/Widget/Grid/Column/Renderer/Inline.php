<?php
	class Briel_Inlineedit_Block_Adminhtml_Widget_Grid_Column_Renderer_Inline extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
	    public function render(Varien_Object $row)
	    {
	        $html = parent::render($row);
			$html = '<input class="input_renderer_qty" type="text" ';
        	$html .= 'name="' . $this->getColumn()->getId() . '" ';
			$html .= 'onFocus="this.select()"';
        	$html .= 'value="' . $row->getData($this->getColumn()->getIndex()) . '"';
        	$html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
	        //$html .= '<button onclick="updateField(this, '. $row->getId() .'); return false">' . Mage::helper('inlineedit')->__('ok') . '</button>';
	        $html .= '<input class="input_renderer_date_submit" type="submit" onclick="updateField(this, '. $row->getId() .'); return false" value="' . Mage::helper('inlineedit')->__('ok') . '"/>';
	        return $html;
	    }
 
	}