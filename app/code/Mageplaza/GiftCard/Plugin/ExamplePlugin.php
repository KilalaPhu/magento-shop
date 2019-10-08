<?php

namespace Mageplaza\GiftCard\Plugin;
use Magento\Checkout\Model\Session;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\SalesRule\Model\CouponFactory;

class ExamplePlugin
{
    protected $_card;
    protected $_session;
    protected $_coupon;
    public function __construct(Session $session,CardFactory $cardFactory,CouponFactory $couponFactory)
    {
        $this->_session = $session;
        $this->_card = $cardFactory;
        $this->_coupon = $couponFactory;
    }


    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject, $result)
    {
        $model = $this->_card->create();
        if(!empty($this->_session->getData('coupon_code'))){
            $result = $data = $this->_session->getData('coupon_code');
            $model->load($result,'code');
            if(!empty($model->getData())){
                return $result;
            }
            return '';
        }
        return $result;

    }



}

//bắt đầu chạy ngay sau khi phương thức được quan sát kết thúc