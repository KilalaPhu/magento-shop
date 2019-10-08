<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\Code;
use Magento\Framework\App\RequestInterface;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;
    protected $_request;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,RequestInterface $request,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Mageplaza_GiftCard';
        $this->_controller = 'adminhtml_code';
        parent::_construct();
          $id = $this->_request->getParam('id');
        if(empty($id)){
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'saveAndContinueEdit',
                                'target' => '#edit_form'
                            ]
                        ]
                    ]
                ],
                -100
            );
        }

        }
}
