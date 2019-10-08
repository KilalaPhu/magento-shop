<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit\Tab;
use Mageplaza\GiftCard\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Code extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

   protected $_helper;
    public function __construct(
          Context $context,
          Registry $registry,
          FormFactory $formFactory,
          Data $helper,
          array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_helper = $helper;
    }

    protected function _prepareForm()
    {
        $length = $this->_helper->getLength();
        $data = $this->_coreRegistry->registry('data');

        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('aaaaa', ['legend' => __('GiftCard Information')]);
        if (!empty($data)){
            $fieldset->addField(
                'giftcard_id',
                'hidden',
                [
                    'name' => 'giftcard_id',
                    'value' => $data['giftcard_id']
                ]);
            $fieldset->addField(
                'code',
                'text',
                [
                    'name'     => 'code',
                    'label'    => __('Code'),
                    'title'    => __('Code'),
                    'value' => $data['code'],
                    'disabled' => true
                ]
            );
            $fieldset->addField(
                'balance',
                'text',
                [
                    'name' => 'balance',
                    'label' => __('Balance'),
                    'title' => __('Balance'),
                    'required' => true,
                    'class' => 'validate-number validate-greater-than-zero',
                    'value' => $data['balance']
                ]
            );
            $fieldset->addField(
                'create_from',
                'text',
                [
                    'name'     => 'create_from',
                    'label'    => __('Create From'),
                    'title'    => __('Create From'),
                    'value' => $data['create_from'],
                    'disabled' => true


                ]
            );
        }else{
            $fieldset->addField(
                'code_length',
                'text',
                [
                    'name'     => 'code_length',
                    'label'    => __('Code Length'),
                    'title'    => __('Code Length'),
                    'value'    => $length,
                    'class' => 'validate-number validate-digits validate-digits-range digits-range-1-12 '
                ]
            );
            $fieldset->addField(
                'balance',
                'text',
                [
                    'name' => 'balance',
                    'label' => __('Balance'),
                    'title' => __('Balance'),
                    'required' => true,
                    'class' => 'validate-number validate-greater-than-zero'
                ]
            );
            $fieldset->addField(
                'create_from',
                'hidden',
                [
                    'name'     => 'create_from',
                    'label'    => __('Create From'),
                    'title'    => __('Create From'),
                    'required' => true,
                    'value' => 'admin'
                ]
            );
        }
        $this->setForm($form);

        return parent::_prepareForm();


    }


    public function getTabLabel()
    {
        return __('Gift card information');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
