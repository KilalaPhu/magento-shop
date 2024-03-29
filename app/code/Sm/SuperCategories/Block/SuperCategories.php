<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 2.1.0
# Copyright (c) 2015 YouTech Company. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: YouTech Company
# Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

namespace Sm\SuperCategories\Block;

use Magento\Eav\Model\Config;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Sm\SuperCategories\Block\Cache\Lite;

class SuperCategories extends \Magento\Catalog\Block\Product\AbstractProduct
{
	protected $_config = null;
	protected $_storeId = null;
	protected $_resource;
	protected $_eavConfig;
	protected $_storeManager;
	protected $_objectManager;
	protected $_localeResolver;
	protected $localeDate;
	protected $_scopeConfigInterface;
	protected $_directory;

	public function __construct(
		ResourceConnection $resourceConnection,
		ObjectManagerInterface $objectManager,
		ResolverInterface $localeResolver,
		Config $eavConfig,
		Context $context,
		array $data = [],
		$attr = null
	)
	{
		$this->_eavConfig = $eavConfig;
		$this->_resource = $resourceConnection;
		$this->_objectManager = $objectManager;
		$this->_localeResolver = $localeResolver;
		$this->_storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$this->_scopeConfigInterface = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$this->_storeId = (int)$this->_storeManager->getStore()->getId();
		$this->localeDate = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
		$this->_directory = $this->_objectManager->get('\Magento\Framework\Filesystem');
		if ($context->getRequest() && $context->getRequest()->isAjax()) {
			$_cfg =  $context->getRequest()->getParam('config');
			$this->_config = (array)json_decode(base64_decode(strtr($_cfg, '-_', '+/')));
		} else {
			$this->_config = $this->_getCfg($attr, $data);
		}
		parent::__construct($context, $data);
	}
	
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function _helper()
	{
		return $this->_objectManager->get('\Sm\SuperCategories\Helper\Data');
	}


	public function _getCfg($attr = null , $data = null)
	{
		// get default config.xml
		$defaults = [];
		$collection = $this->_scopeConfigInterface->getValue('supercategories');

		if (empty($collection)) return;
		$groups = [];
		foreach ($collection as $def_key => $def_cfg) {
			$groups[] = $def_key;
			foreach ($def_cfg as $_def_key => $cfg) {
				$defaults[$_def_key] = $cfg;
			}
		}

		// get configs after change
		$_configs = $this->_scopeConfigInterface->getValue('supercategories');
		if (empty($_configs)) return;
		$cfgs = [];

		foreach ($groups as $group) {
			$_cfgs = $this->_scopeConfigInterface->getValue('supercategories/'.$group.'',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			foreach ($_cfgs as $_key => $_cfg) {
				$cfgs[$_key] = $_cfg;
			}
		}

		// get output config
		$configs = [];
		foreach ($defaults as $key => $def) {
			if (isset($defaults[$key])) {
				$configs[$key] = $cfgs[$key];
			} else {
				unset($cfgs[$key]);
			}
		}
		$cf = ($attr != null) ? array_merge($configs, $attr) : $configs;
		$this->_config = ($data != null) ? array_merge($cf, $data) : $cf;
		return $this->_config;
	}

	public function _getConfig($name = null, $value_def = null)
	{
		if (is_null($this->_config)) $this->_getCfg();
		if (!is_null($name)) {
			$value_def = isset($this->_config[$name]) ? $this->_config[$name] : $value_def;
			return $value_def;
		}
		return $this->_config;
	}

	public function _setConfig($name, $value = null)
	{

		if (is_null($this->_config)) $this->_getCfg();
		if (is_array($name)) {
			$this->_config = array_merge($this->_config, $name);

			return;
		}
		if (!empty($name) && isset($this->_config[$name])) {
			$this->_config[$name] = $value;
		}
		return true;
	}

	protected function _toHtml()
	{
		if (!$this->_getConfig('isactive', 1)) return;

		$use_cache = (int)$this->_getConfig('use_cache');
		$cache_time = (int)$this->_getConfig('cache_time');
		$folder_cache = $this->_directory->getDirectoryWrite(DirectoryList::CACHE)->getAbsolutePath();
		$folder_cache = $folder_cache.'Sm/SuperCategories/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		
		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new \Sm\SuperCategories\Block\Cache\Lite($options);
		if ($this->_isAjax()) {
			$ajax_reslisting_start = $this->getRequest()->getPost('ajax_reslisting_start', 0);
			$catid = $this->getRequest()->getPost('categoryid');
			if ($use_cache){
				$cacheid_items = md5(serialize([$this->_getConfig(), $this->_storeId ,$this->_storeManager->getStore()->getCurrentCurrencyCode(), $ajax_reslisting_start, $catid]));
				if ( $dataitems = $Cache_Lite->get($cacheid_items)) {
					return  $dataitems;
				} else { 
					$template_file = "default_items.phtml";
					$this->setTemplate($template_file);
					$dataitems = parent::_toHtml();
					$Cache_Lite->save($dataitems);
				}
			}else{
				if(file_exists($folder_cache))
					$Cache_Lite->_cleanDir($folder_cache);
					$template_file = "default_items.phtml";
					$this->setTemplate($template_file);
			}
			
		}		
		else{
			if ($use_cache){
				$hash = md5( serialize([$this->_getConfig(), $this->_storeId ,$this->_storeManager->getStore()->getCurrentCurrencyCode()]) );
				if ($data = $Cache_Lite->get($hash)) {
					return  $data;
				} else { 
					$template_file = $this->getTemplate();
					$template_file = (!empty($template_file)) ? $template_file : "Sm_SuperCategories::default.phtml";
					$this->setTemplate($template_file);
					$data = parent::_toHtml();
					$Cache_Lite->save($data);
				}
			}else{
				if(file_exists($folder_cache))
					$Cache_Lite->_cleanDir($folder_cache);
				$template_file = $this->getTemplate();
				$template_file = (!empty($template_file)) ? $template_file : "Sm_SuperCategories::default.phtml";
				$this->setTemplate($template_file);
			}
		}
		
        return parent::_toHtml();
		
	}

	public function getListCriterionFilter($filter_catid=false)
	{
		$catids = $this->_getConfig('product_category');
		if($catids == null) return;
		$_catids = $this->_getCatActive($catids);
		if(empty($_catids))  return;
		$list = [];
		$include = (int)$this->_getConfig('child_category_products',1);
		$level = (int)$this->_getConfig('max_depth',1);
		$__catids = ($include && $level > 0) ? $this->_childCategory($_catids, true, 0 , $level) : $_catids;
		$filters = explode(',',$this->_getConfig('product_order_by'));
		$articles_filter = [];
		if ($this->_isAjax())
		{
			$filter_preload = $filter_catid;
		}else{
			$filter_preload = $this->_getConfig('field_preload');
		}
		if (empty($filters)) return;
		if (!in_array($filter_preload, $filters)) {
			foreach ($filters as $filter) {
				$filter_preload = $filter;
				break;
			}
		}
		$order_attr  = $this->_objectManager->get('Sm\SuperCategories\Model\Config\Source\OrderBy');
		$order_label = $order_attr->toOptionArray();
		foreach ($filters as $filter) {
			$aritles = [];
			$aritles['count'] = $this->_countProducts($__catids, $filter);
			$aritles['entity_id'] = $filter;

			foreach ($order_label as $title){
				if ( $filter == $title['value'] ){
					$aritles['title'] = $title['label'];
				}
			}
			array_unshift($articles_filter, $aritles);
		}

		foreach ($articles_filter as $filter) {
			if ($filter['count'] > 0) {
				if ($filter['entity_id'] == $filter_preload) {
					$filter['sel'] = 'sel';
					$filter['child'] = $this->_getProductInfor($__catids, $filter_preload);
				}
				$list[$filter['entity_id']] = $filter;
			}
		}
		return $list;
	}
	
	public function _getProductInfor($_catids, $field_order = null)
	{
		$small_image_config = [
			'width' => (int)$this->_getConfig('img_width', 200),
			'height' => $this->_getConfig('img_height', null),
			'background' => (string)$this->_getConfig('img_background'),
			'function' => (int)$this->_getConfig('img_function')
		];

		if ($_catids == '*') {
			$_catids = $this->_getCatIds();
		}
		!is_array($_catids) && settype($_catids, 'array');
		if (!empty($_catids)) {
			$products = $this->_getProductsBasic($_catids, $field_order);
			if ($products != null) {
				$_products = $products->getItems();
				if (!empty($_products)) {
					$helper = $this->_helper();
					foreach ($products as $_product) {
						$_product->setStoreId($this->_storeId);
						$_product->title = $_product->getName();
						$image = $helper->getProductImage($_product, $this->_getConfig());
						$_image = $helper->_resizeImage($image, $small_image_config);
						$_product->_image = $_image;
						$_product->_description = $helper->_cleanText($_product->getDescription());
						$_product->_description = $helper->_trimEncode($_product->_description != '') ? $_product->_description : $helper->_cleanText($_product->getShortDescription());
						$_product->_description = $helper->_trimEncode($_product->_description != '') ? $helper->truncate($_product->_description, $this->_getConfig('product_description_maxlength')) : '';
						$_product->link = $_product->getProductUrl();
						//$_product->num_view_counts = (!isset($_product->num_view_counts) || $_product->num_view_counts == null) ? 0 : $_product->num_view_counts;
					}

					return $_products;
				}
			}
		}
		return null;
	}	
	
	public function _isAjax()
	{
		$isAjax = $this->getRequest()->isAjax();
		$is_ajax_super_categories = $this->getRequest()->getPost('is_ajax_super_categories');
		if ($isAjax && $is_ajax_super_categories == 1) {
			return true;
		} else {
			return false;
		}
	}		

	public function getListCategoriesFilter($categoryId = null,$start=0)
	{
		/*Get Categories infor*/
		$helper = $this->_helper();
		$category_image_config = [
			'width' => (int)$this->_getConfig('imgcfgcat_width', 50),
			'height' => (int)$this->_getConfig('imgcfgcat_height', 50),
			'background' => (string)$this->_getConfig('imgcfgcat_background'),
			'function' => (int)$this->_getConfig('imgcfgcat_function', 0)
		];
		
		$catids = $this->_getConfig('product_category');
		if($catids == null) return;
		$_catids = $this->_getCatActive($catids);
		if(empty($_catids))  return;
		$cats = $this->_getCatinfor($_catids, true);
		$list = [];
		foreach( $cats as $cat){
			$child_list = [];
			$cat_info = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($cat['entity_id']);
			$_image = $cat_info->getImageUrl();
			if($_image)
				$cat['image'] = $helper->_resizeImage($_image, $category_image_config,'category');
			else
				$cat['image'] = '';
			$cat_child = $cat_info->getChildren();
			if( $cat_child != null ){
				$cat_child = explode(',',$cat_child); 
				foreach( $cat_child as $subCatid){ 
					$_sub_cat = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($subCatid);
					if( $_sub_cat->getIsActive() ) {
						$child['link']     = $_sub_cat->getUrl();
						$child['title']     = $_sub_cat->getName();
						$__image = $_sub_cat->getImageUrl();
						if($__image) 
							$child['image'] = $helper->_resizeImage($__image, $category_image_config,'category');
						else
							$child['image'] = '';
						$child_list[] = $child;
					}
					$cat['child'] = $child_list;
				}			
			}						
			$list[] = $cat;
		}
		return $list;
	}
	
	public function _getCatinfor($catids, $orderby = null)
	{
		!is_array($catids) && settype($catids, 'array');
		$list = [];
		if (!empty($catids)) {
			foreach ($catids as $catid) {
				$cat = [];
				$category_model = $this->_objectManager->create('Magento\Catalog\Model\Category');
				$category = $category_model->load((int)$catid);
				$cat['title'] = $category->getName();
				$cat['link'] = $category->getUrl();
				$cat['entity_id'] = $catid;
				$list[$catid] = $cat;
			}
		}
		return $list;
	}
	
	/*
	* return countProduct;
	*/
	protected function _countProducts($catids, $field_order = null)
	{
		!is_array($catids) && settype($catids, 'array');
		$countProduct = $this->_getCountProductsBasic($catids, $field_order, true);
		return $countProduct;
	}

	public function _getCountProductsBasic($catids,$field_order = null, $countProduct = false)
	{
		$collection = [];
		!is_array($catids) && settype($catids, 'array');
		if (!empty($catids)) {
			$attributes = ['name', 'special_to_date', 'description', 'short_description', 'image', 'thumbnail', 'price', 'special_price'];
			$collection = $this->_objectManager->create('Magento\Catalog\Model\Product')
				->getCollection()
				->addAttributeToSelect($attributes)
				->addAttributeToSelect('featured')
				->setStoreId($this->_storeId)
				->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addAttributeToFilter([['attribute' => 'category_id', 'in' => [$catids]]]);
			if ($this->_getFeaturedProduct($collection) == false) return null;
			$this->_getFeaturedProduct($collection);
			$collection->getSelect()->group('entity_id')->distinct(true);
			$this->_getOrder($collection, $field_order);

			$collection->clear();
			if ($countProduct) return count($collection->getAllIds());
			$start = (int)$this->getRequest()->getPost('ajax_reslisting_start');
			if (!$start) $start = 0;
			$_limit = (int)$this->_getConfig('product_limitation', 5);
			$_limit = $_limit <= 0 ? 0 : $_limit;
			if ($_limit >= 0) {
				$collection->getSelect()->limit($_limit, $start);
			}
		}
		return $collection;
	}
	
	public function _getProductCatalog()
	{
		$catids = $this->_getConfig('product_category');
		$inlucde = (int)$this->_getConfig('child_category_products', 1);
		$level = (int)$this->_getConfig('max_depth', 1);
		if ($catids == null) return;
		$_catids = $this->_getCatActive($catids);
		$_catids = ($inlucde && $level > 0) ? $this->_childCategory($_catids, true, 0, (int)$level) : $_catids;
		if (empty($_catids)) return;
		$products = $this->_getProductsBasic($_catids);
		return $products;
	}

	private function _getCatActive($catids = null, $orderby = true)
	{
		if (is_null($catids)) {
			$catids = $this->_getConfig('product_category');
		}
		!is_array($catids) && $catids = preg_split('/[\s|,|;]/', $catids, -1, PREG_SPLIT_NO_EMPTY);
		
		if (empty($catids)) return;
		$categoryIds = ['in' => $catids];
		$collection = $this->_objectManager->create('Magento\Catalog\Model\Category') ->getCollection();
		$collection->addAttributeToSelect('*')
			->setStoreId($this->_storeId)
			->addAttributeToFilter('entity_id', $categoryIds)
			->addIsActiveFilter();

		if ($orderby) {
			$attribute = 'random'; // name | position | entry_id | random
			$dir = 'ASC';
			switch ($attribute) {
				case 'name':
				case 'position':
				case 'entry_id':
					$collection->addAttributeToSort($attribute, $dir);
					break;
				case 'random':
					$collection->getSelect()->order(new \Zend_Db_Expr('RAND()'));
					break;
				default:
			}
		}
		$_catids = [];
		
		if (empty($collection)) return;
		foreach ($collection as $category) {
			$_catids[] = $category->getId();
		}
		return $_catids;
	}

	private function _childCategory($catids, $allcat = true, $limitCat = 0, $levels = 0)
	{
		!is_array($catids) && settype($catids, 'array');
		$additional_catids = [];
		if (!empty($catids)) {

			foreach ($catids as $catid) {
				$_category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($catid);
				$levelCat = $_category->getLevel();
				if ($_category->hasChildren()){
					$catid_childs = $_category->getAllChildren(true);
					foreach ($catid_childs as $cat_child) {
						$_cat_child = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($cat_child);
						$cat_child_level = $_cat_child->getLevel();
						$condition = ($cat_child_level - $levelCat <= $levels);
						if ($condition) {
							$additional_catids[] = $_cat_child->getId();
						}
					}
				}
			}
			$catids = $allcat ? array_unique(array_merge($catids, $additional_catids)) : array_unique($additional_catids);
		}
		return $catids;
	}

	public function _getProductsBasic($catids,$field_order = null, $countProduct = false)
	{
		$collection = [];
		!is_array($catids) && settype($catids, 'array');
		if (!empty($catids)) {
			$attributes = ['name', 'special_to_date', 'description', 'short_description', 'image', 'thumbnail', 'price', 'special_price'];
			$collection = $this->_objectManager->create('Magento\Catalog\Model\Product')
				->getCollection()
				->addAttributeToSelect($attributes)
				->addAttributeToSelect('featured')
				->addMinimalPrice()
				->addFinalPrice()
				->addUrlRewrite()
				->setStoreId($this->_storeId)
				->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addAttributeToFilter([['attribute' => 'category_id', 'in' => [$catids]]]);
			if ($this->_getFeaturedProduct($collection) == false) return null;
			$this->_getFeaturedProduct($collection);
			$collection->setVisibility($this->_objectManager->create('\Magento\Catalog\Model\Product\Visibility')->getVisibleInCatalogIds());
			$this->_addViewsCount($collection);
			$this->_addReviewsCount($collection);
			$collection->getSelect()->group('entity_id')->distinct(true);
			$this->_getOrder($collection, $field_order);
			
			$collection->clear();
			if ($countProduct) return count($collection->getAllIds());
			$start = (int)$this->getRequest()->getPost('ajax_reslisting_start');
			if (!$start) $start = 0;
			$_limit = (int)$this->_getConfig('product_limitation', 5);
			$_limit = $_limit <= 0 ? 0 : $_limit;
			if ($_limit >= 0) {
				$collection->getSelect()->limit($_limit, $start);
			}
			$this->_objectManager->create('Magento\Review\Model\Review')->appendSummary($collection);
		}
		return $collection;
	}

	private function _getFeaturedProduct($collection)
	{
		$filter = (int)$this->_getConfig('product_featured', 0);
		$attributeModel = $this->_eavConfig->getAttribute('catalog_product', 'featured');
		switch ($filter) {
			// Show All
			case 0:
				break;
			// None Featured
			case 1:
				if ($attributeModel->usesSource()) {
					$collection->addAttributeToFilter([['attribute' => 'featured', 'eq' => 0]], null, 'left');
				}
				break;
			// Only Featured
			case 2:
				if ($attributeModel->usesSource()) {
					$collection->addAttributeToFilter([['attribute' => 'featured', 'eq' => 1]]);
				} else {
					return;
				}
				break;
		}
		return $collection;
	}

	/*
     *	Get Order
     */
	private function _getOrder($collection,$attribute)
	{
		$dir = (string)$this->_getConfig('product_order_dir', 'ASC');
		switch ($attribute) {
			case 'entity_id':
			case 'name':
			case 'created_at':
				$collection->setOrder($attribute, $dir);
				break;
			case 'price':
				$collection->getSelect()->order('final_price ' . $dir . '');
				break;
			case 'random':
				$collection->getSelect()->order(new \Zend_Db_Expr('RAND()'));
				break;
			case 'lastest_product':
				$this->_getLastestProduct($collection);
				break;
			case 'top_rating':
				$collection->getSelect()->order('num_rating_summary ' . $dir . '');
				break;
			case 'most_reviewed':
				$collection->getSelect()->order('num_reviews_count ' . $dir . '');
				break;
			case 'most_viewed':
				$collection->getSelect()->order('num_view_counts ' . $dir . '');
				break;
			case 'best_sellers':
				$collection->getSelect()->order('ordered_qty ' . $dir . '');
				break;
			default:
		}
		return $collection;
	}

	/*
     *	Get Lastest Product
     */
	private function _getLastestProduct(&$collection)
	{
		$todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0)->format('Y-m-d H:i:s');
		$todayEndOfDayDate = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

		$collection = $this->_addProductAttributesAndPrices($collection)
			->addStoreFilter()
			->addAttributeToFilter('news_from_date',
				['or' => [
					0 => ['date' => true, 'to' => $todayEndOfDayDate],
					1 => ['is' => new \Zend_Db_Expr('null')]
				]], 'left')
			->addAttributeToFilter('news_to_date',
				['or' => [
					0 => ['date' => true, 'from' => $todayStartOfDayDate],
					1 => ['is' => new \Zend_Db_Expr('null')]
				]], 'left')
			->addAttributeToSort('news_from_date', 'DESC');
		return $collection;
	}

	// add views_count
	private function _addViewsCount(& $collection)
	{
		$reports_event_table = $this->_resource->getTableName('report_event');
		$select = $this->_resource->getConnection('core_read')
			->select()
			->from($reports_event_table, ['*', 'num_view_counts' => 'COUNT(`event_id`)'])
			->where('event_type_id = 1')
			->group('object_id');
		$collection->getSelect()
			->joinLeft(['mv' => $select],
				'mv.object_id = e.entity_id');
		return $collection;
	}

	// add reviews_count and rating_summary
	private function _addReviewsCount(& $collection)
	{
		$review_summary_table = $this->_resource->getTableName('review_entity_summary');
		$collection->getSelect()
			->joinLeft(
				["ra" => $review_summary_table],
				"e.entity_id = ra.entity_pk_value AND ra.store_id=" . $this->_storeId,
				[
					'num_reviews_count' => "ra.reviews_count",
					'num_rating_summary' => "ra.rating_summary"
				]
			);
		return $collection;
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->_objectManager->get('\Magento\Framework\Url\Helper\Data')->getEncodedUrl($url),
            ]
        ];
    }
	
}