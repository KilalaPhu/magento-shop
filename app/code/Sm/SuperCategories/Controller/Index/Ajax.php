<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 2.1.0
# Copyright (c) 2015 YouTech Company. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: YouTech Company
# Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
namespace Sm\SuperCategories\Controller\Index;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;



class Ajax extends \Magento\Framework\App\Action\Action {
	/** @var  \Magento\Framework\View\Result\Page */
	protected $resultPageFactory;
	protected $jsonEncoder;
	protected $_layout;
	
	/**      * @param \Magento\Framework\App\Action\Context $context      */
	public function __construct(
		Context $context, 
		PageFactory $resultPageFactory,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Framework\View\Layout $layout){
		$this->resultPageFactory = $resultPageFactory;
		$this->jsonEncoder = $jsonEncoder;
		$this->_layout = $layout; 
		parent::__construct($context);
	}

	/**
	 * Blog Index, shows a list of recent blog posts.
	 *
	 * @return \Magento\Framework\View\Result\PageFactory
	 */
	public function execute()
	{
	    $isAjax = $this->getRequest()->isAjax();
		if ($isAjax){
			$layout =  $this->_layout;
			$layout->getUpdate()->load(['supercategories_index_ajax']);
			$layout->generateXml();
            $output = $layout->getOutput();
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody($this->jsonEncoder->encode(array('items_markup' => $output)));
        }
	}
}