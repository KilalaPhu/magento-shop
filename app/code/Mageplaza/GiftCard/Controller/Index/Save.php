<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Math\Random;
class Save extends Action
{
    protected $_cardfactory;
    protected $_random;
    protected $_session;
    protected $pagefactory;
    public function __construct(Context $context,
                                CardFactory $cardFactory,
                                PageFactory $pagefactory,
                                Random $random)
    {
        $this->_cardfactory = $cardFactory;
        $this->pagefactory = $pagefactory;
        $this->_random = $random;
        parent::__construct($context);
    }
    public function execute()
    {
        $data=$this->getRequest()->getParams();
        $length=$this->getRequest()->getParam('length');
        $code = $this->_random->getRandomString($length,'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789');
        if(!empty($data['giftcard_id'])){
            $id = $data['giftcard_id'];
            $model = $this->_cardfactory->create();
            $model->load($id);
            $model->addData($data);
            $model->save();
        }else{
            $model = $this->_cardfactory->create();
            $check_code = $model->getCollection()->addFieldToFilter('code',['eq'=>$code]);
            print_r($check_code->getData());
            if(!empty($check_code->getData())){
                $this->messageManager->addError(__('code already exists'));
                return $this->_redirect('giftcard/index/test');
            }else{
                $model->addData($data);
                $model->save();
                $this->messageManager->addSuccess("You have added Gift Card.");
            }
        }
       return $this->_redirect('giftcard/index/test');
    }



}
