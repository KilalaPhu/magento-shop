<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\Card;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\Card', 'Mageplaza\GiftCard\Model\ResourceModel\Card');
    }

}
