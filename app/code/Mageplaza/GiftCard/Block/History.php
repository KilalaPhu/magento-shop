<?php
namespace Mageplaza\GiftCard\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Mageplaza\GiftCard\Model\CardFactory;
class History extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_history;
    protected $_card;
    public function __construct(Context $context,Registry $registry,HistoryFactory $historyFactory,CardFactory $card)
    {
        $this->_history = $historyFactory;
        $this->_card = $card;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    public function getDulieu()
    {
        $model = $this->_history->create();
        $collection = $model->getCollection();
        $collection->getSelect()->join(
            ['cha' =>$collection->getTable("giftcard_code")],
            'main_table.giftcard_id=cha.giftcard_id',
            ['code'=>'code']
        );
        $data = $collection->getData();
        return $data;
    }
    public function getCard()
    {
        $model = $this->_card->create();
        $collection = $model->getCollection();
        $data = $collection->getData();
        return $data;
    }

}