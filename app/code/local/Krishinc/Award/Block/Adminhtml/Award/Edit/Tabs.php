    <?php
     
    class Krishinc_Award_Block_Adminhtml_Award_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
    {
     
        public function __construct()
        {
            parent::__construct();
            $this->setId('award_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('award')->__('Award Information'));
        }
     
        protected function _beforeToHtml()
        {
            $this->addTab('form_section', array(
                'label'     => Mage::helper('award')->__('Award Information'),
                'title'     => Mage::helper('award')->__('Award Information'),
                'content'   => $this->getLayout()->createBlock('award/adminhtml_award_edit_tab_form')->toHtml(),
            ));
           
            return parent::_beforeToHtml();
        }
    }