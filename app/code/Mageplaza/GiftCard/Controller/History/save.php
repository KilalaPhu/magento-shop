<?php
namespace Mageplaza\GiftCard\Controller\History;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mageplaza\GiftCard\Model\CardFactory;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer;


class Save extends Action
{
    protected $_cardfactory;
    protected $_history;
    protected $_session;
    protected $pagefactory;
    protected $_customer;
    protected $_resourcemodel;
    public function __construct(Context $context,
                                CardFactory $cardFactory,
                                PageFactory $pagefactory,
                                Session $session,
                                HistoryFactory $historyFactory,CustomerFactory $customerFactory,Customer $resourcemodel)
    {
        parent::__construct($context);
        $this->_resourcemodel = $resourcemodel;
        $this->_cardfactory = $cardFactory;
        $this->_history = $historyFactory;
        $this->_session = $session;
        $this->pagefactory = $pagefactory;
        $this->_customer = $customerFactory;
    }
    public function execute()
    {
        $data=$this->getRequest()->getParams();
        $code = $data['code'];

        $history = $this->_history->create();
        $collection_his = $history->getCollection();
        $collection_his->getSelect()->join(
            ['giftcard_code' =>$collection_his->getTable("giftcard_code")],
            'main_table.giftcard_id=giftcard_code.giftcard_id',
            ['code'=>'code']
        );
        $collection_his->addFieldToFilter('code',['eq'=>$code]);
        $data_his = $collection_his->getData();

        $card = $this->_cardfactory->create();
        $card->load($code,'code');
        $data_card = $card->getData();


        if (empty($data_card)){
            $this->messageManager->addError('Invalid discount code');
            return $this->_redirect('*/*/customer');
        }
        $card_id = $data_card['giftcard_id'];
        $card_balance = $data_card['balance'];
        $card_amount_ = $data_card['amount_used'];

        $amount = $card_balance + $card_amount_;

        $id = $this->_session->getCustomerId();
        $customer = $this->_customer->create();
        $customer->load($id);
        $balance = $customer->getData()['giftcard_balance'] + $card_balance;



//            create ->reedem
        if(!empty($data_his[0]['action'])){
            if($data_his[0]['action'] == 'create'){
                $id_his = $data_his[0]['history_id'];
                $history->load($id_his)->addData([
                    'customer_id' => $id,
                    'giftcard_id' => $card_id,
                    'amount' => $card_balance,
                    'action' => 'reedem'
                ])->save();

                $card->load($card_id)->setData('amount_used',$card_balance)->save();

                $this->_resourcemodel->getConnection()->update(
                    $this->_resourcemodel->getTable('customer_entity'),
                    [
                        'giftcard_balance' => $customer->getData()['giftcard_balance'] + $card_balance,
                    ],
                    $this->_resourcemodel->getConnection()->quoteInto('entity_id = ?', $id)
                );
                $this->messageManager->addSuccess('successful conversion');
                return $this->_redirect('giftcard/history/customer');
            }

        }
//        end




        if($card_balance < $amount){
            $this->messageManager->addError('Invalid discount code');
            return $this->_redirect('*/*/customer');
        }else{
            $card->load($card_id)->setAmountUsed($amount)->save();
            $this->messageManager->addSuccess('thanh cong');
        }

        $history->addData([
            'customer_id' => $id,
            'giftcard_id' => $card_id,
            'amount' => $card_balance,
            'action' => 'reedem'
        ])->save();


        $customer = $this->_customer->create();
        $customer->load($id);
        $this->_resourcemodel->getConnection()->update(
            $this->_resourcemodel->getTable('customer_entity'),
            [
                'giftcard_balance' => $balance,
            ],
            $this->_resourcemodel->getConnection()->quoteInto('entity_id = ?', $id)
        );

       return $this->_redirect('giftcard/history/customer');
    }



}
