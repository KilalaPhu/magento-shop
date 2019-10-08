<?php

namespace Mageplaza\GiftCard\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Checkout\Model\Session;



class ObserverforCheckCoupon implements ObserverInterface
{

    protected $request;
    protected $_session;
    protected $ruleFactory;
    protected $_couponHelper;
    protected $_customerSession;
    protected $_card;
    protected $_actionFlag;
    protected $_mess;
    protected $_redirect;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        CardFactory $card,
        ActionFlag $actionFlag,
        ManagerInterface $mess,
        RedirectInterface $redirect,Session $sessionmodel
    ) {
        $this->request = $request;
        $this->_card = $card;
        $this->_customerSession = $customerSession;
        $this->_actionFlag = $actionFlag;
        $this->_mess = $mess;
        $this->_redirect = $redirect;
        $this->_session = $sessionmodel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $code = $this->request->getParam('remove') == 1 ? '' : $this->request->getParam('coupon_code');
        $this->_session->unsetData('coupon_code');
//        unset sử dụng để loại bỏ một phần tử xác định trong mảng.
        $model = $this->_card->create();
        $collection = $model->load($code,'code');
        $balance = $collection->getBalance();
        $amount = $collection->getData('amount_used');

        $this->_session->setData('balance',$balance-$amount);

        if (!empty($collection->getData()) && $balance > $amount){
            $this->_session->setData('coupon_code',$code);
            $this->_mess->addSuccess(__("Apply Gift Card is Success"));
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $observer->getControllerAction()->getResponse()->setRedirect($this->_redirect->getRefererUrl());
        }


    }

}