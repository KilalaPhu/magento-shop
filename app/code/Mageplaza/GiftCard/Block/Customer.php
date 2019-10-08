<?php
namespace Mageplaza\GiftCard\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Mageplaza\GiftCard\Helper\Data;
use Magento\Customer\Model\CustomerFactory;

class Customer extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_price;
    protected $_time;
    protected $_data;
    protected $_history;
    protected $_session;
    protected $_helper;
    protected $_customer;
    public function __construct(Context $context,
                                Registry $registry,
                                HistoryFactory $historyFactory,
                                Session $session,
                                TimezoneInterface $time,
                                PriceCurrencyInterface $price,
                                Data $helper,CustomerFactory $customer)
    {
        $this->_customer = $customer;
        $this->_history = $historyFactory;
        $this->_price = $price;
        $this->_helper = $helper;
        $this->_time = $time;
        $this->_session = $session;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    public function getDulieu()
    {
        $id=$this->_session->getData()['visitor_data']['customer_id'];
        $model = $this->_history->create();

        $collection = $model->getCollection()->addFieldToFilter('customer_id',$id);
        $collection->getSelect()->join(
            ['giftcard_code' =>$collection->getTable("giftcard_code")],
            'main_table.giftcard_id=giftcard_code.giftcard_id',
            ['code'=>'code']
        );
        $data = $collection->getData();
        return $data;
    }
    public function getCustomer()
    {
        $id=$this->_session->getData()['visitor_data']['customer_id'];
        $model = $this->_customer->create();
        $model->load($id);
        $data = $model->getData();
        return $data;
    }
    public function getTime($time1)
    {
        $time = $this->_time->date($time1)->format('d-m-Y');
        return $time;
    }
    public function getFormatedPrice($amount)
    {
        return $this->_price->convertAndFormat($amount);
    }

    public function getRedeem()
    {
        $redeem = $this->_helper->getRedeem();
        if($redeem == 1){
            return $redeem;
        }else{
            return '';
        }
    }

}