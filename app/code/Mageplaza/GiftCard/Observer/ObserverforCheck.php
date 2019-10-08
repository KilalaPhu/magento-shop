<?php
namespace Mageplaza\GiftCard\Observer;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Math\Random;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ProductFactory;

class ObserverforCheck implements \Magento\Framework\Event\ObserverInterface
{
    protected $_random;
    protected $_session;
    protected $_helper;
    protected $_history;
    protected $_card;
    protected $_product;

    public function __construct(Data $helper,
                                HistoryFactory $historyFactory,
                                CardFactory $cardFactory,
                                Random $random,Session $session,ProductFactory $product)
    {
        $this->_random  = $random;
        $this->_session  = $session;
        $this->_helper  = $helper;
        $this->_history  = $historyFactory;
        $this->_card  = $cardFactory;
        $this->_product  = $product;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $id_order = $observer->getData('order')->getIncrementId();

        $quote = $observer->getData('quote')->getAllItems();
//
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info(
//            'test',
//            [
//                'cc' => $observer->getData('order')->getData()
//
//            ]
//        );

        $length = $this->_helper->getLength();
        $product = $this->_product->create();

        foreach($quote as $item) {

            $id = $item->getProductId();

            $qty = $item->getQty();

            for ($i = 0; $i < $qty; $i++){

                if(!empty($product->load($id)->getData()["gift_card_amount"])){
                    $code = $this->_random->getRandomString($length, 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789');
                    $id_cus = $this->_session->getCustomerId();
                    $card = $this->_card->create();
                    $history = $this->_history->create();
                    $amount = $product->load($id)->getData()["gift_card_amount"];
                    $card->addData([
                        'code' => $code,
                        'balance' => $amount,
                        'amount_used' => 0,
                        'create_from' => $id_order
                    ])->save();

                    $id = $card->getId();
                    $card_balance = $card->getBalance();

                    $history->addData([
                        'customer_id' => $id_cus,
                        'giftcard_id' => $id,
                        'amount' => $card_balance,
                        'action' => 'create'
                    ])->save();
                }

            }




        }

    }
}