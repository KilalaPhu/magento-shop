<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\CardFactory;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Magento\Customer\Model\Session;

class Index extends Action
{
    protected $_pageFactory;
    protected $_session;
    protected $_history;
    protected $_cardfactory;
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CardFactory $cardFactory,HistoryFactory $history,Session $session)
    {
        $this->_pageFactory = $pageFactory;
        $this->_cardfactory = $cardFactory;
        $this->_history = $history;
        $this->_session = $session;
        return parent::__construct($context);
    }

    public function execute()
    {

        $model = $this->_cardfactory->create();
        $collection = $model->getCollection();

        $data = [
            'code'=>$this->getRequest()->getParam('code'),
            'balance'=>$this->getRequest()->getParam('balance'),
            'create_from' => 'admin'
        ];

        $code = $this->getRequest()->getParam('code');
        $balance = $this->getRequest()->getParam('balance');
        $check = $collection->addFieldToFilter('code',['eq'=>$code]);

                                 //update
        if (!empty($this->getRequest()->getParam('id' ))){
            $id = $this->getRequest()->getParam('id' );

            if(!empty($model->load($id)->getData())){
                if (!empty($check->getData())){
                    echo 'ma code da ton tai';
                }else{
                    if($code != ''){
                        $model->load($id)->setCode($code)->save();
                        echo 'update thanh cong';
                    }
                    if($balance > 0){
                        $model->load($id)->setBalance($balance)->save();
                        echo 'update thanh cong';
                    }
                    if($balance > 0 && $code != ''){
                        $model->load($id)->addData($data)->save();
                        echo 'update thanh cong';
                    }
                }
            }else{
                echo  'khong ton tai gift cart';
            }


        }else{
            if($code != '' && $balance > 0){
                $check = $model->load($code,'code');
                if (!empty($check->getData())){
                    echo 'ma codezzzzzzz da ton tai';
                }else{
                    $model->addData($data)->save();
                    echo 'them moi thanh cong';
                }

            }
        }


        }

    }
