<?php
namespace Mageplaza\GiftCard\Model\Quote;
use Magento\Checkout\Model\Session;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Quote\Model\Quote\Address\Total;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    protected $_priceCurrency;
    protected $_total;
    protected $_card;
    protected $_session;
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        Session $session,
        CardFactory $card,Total $total
    ){
        $this->_priceCurrency = $priceCurrency;
        $this->_session = $session;
        $this->_card = $card;
        $this->_total = $total;
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $code = $this->_session->getData('coupon_code');
        $model = $this->_card->create();
        $data = $model->load($code,'code');

        $balance = $data->getBalance();
        $amount_Used = $data->getAmountUsed();
        $conlai = $balance-$amount_Used;

        $sub_tax = $total->getData('base_subtotal_incl_tax');
        $ship = $total->getData('shipping_amount');
        $tong = $sub_tax +$ship;

        $discount = $conlai;

        if($conlai > $tong && !empty($tong)){
            $total->addTotalAmount('custom', -$tong);
            return $this;
        }else{
            $total->addTotalAmount('custom', -$discount);
            return $this;
        }


    }


    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $amount = $this->_session->getData('balance');
        $code = $this->_session->getData('coupon_code');
        $model = $this->_card->create();
        $data = $model->load($code,'code');

        $ship = $total->getData('shipping_amount');
        $sub_tax = $total->getData('base_subtotal_incl_tax');
        $tong = $sub_tax +$ship;
        if(!empty($data->getData())){
            if($amount > $tong && !empty($tong)){
                $totals[] = [
                    'code' => 'customer_discount',
                    'title' => __('Gift Card'.' '.'$'.$amount),
                    'value' => $tong,
                ];
                return $totals;
            }else{
                $totals[] = [
                    'code' => 'customer_discount',
                    'title' => __('Gift Card'),
                    'value' => $amount,
                ];
                return $totals;
            }
        }else{
            $totals[] = [
                'code' => 'customer_discount',
                'title' => __('Gift Card'),
                'value' => 0,
            ];
            return $totals;
        }




    }

}