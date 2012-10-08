<?php

class Company_Web_Adminhtml_WebController extends Mage_Adminhtml_Controller_Action
{
   public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
}
