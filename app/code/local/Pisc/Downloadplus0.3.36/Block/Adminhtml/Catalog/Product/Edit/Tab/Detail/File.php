<?php
/**
 * Catalog ProductEdit Tab Detail File block
 *
 * @category    Pisc
 * @package     Pisc_Downloadplus
 * @author      PILLWAX Industrial Solutions Consulting <technology.license@pillwax.com>
 * @version		0.1.1
 */

class Pisc_Downloadplus_Block_Adminhtml_Catalog_Product_Edit_Tab_Detail_File extends Mage_Adminhtml_Block_Template
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadplus/product/edit/detail/file.phtml');
    }

    /**
     * Get product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Get file that is being edited
     */
    public function getDetail()
    {
    	$result = Mage::registry('downloadplus_detail');
    	return $result;
    }

    /**
     * Get files basename
     */
    public function getFileBasename()
    {
    	if ($detail = $this->getDetail()) {
    		return basename($detail->getFile());
    	} else {
    		return '';
    	}
    }

    /**
     * Gets the file with type
     */
    public function getFileWithType()
    {
    	if ($detail = $this->getDetail()) {
    		return $detail->convertFileToType($detail->getFile());
    	} else {
    		return '';
    	}
    }

    /**
     * Gets file key
     */
    public function getDetailKey()
    {
    	$result = Mage::registry('downloadplus_detail_key');
    	return $result;
    }

    /**
     * Gets file area
     */
    public function getDetailArea()
    {
    	$result = Mage::registry('downloadplus_detail_area');
    	return $result;
    }

    /**
     * Returns Selection of Link/Sample Titles
     */
    public function getFileRelation($disabled=null)
    {
    	$form = new Varien_Data_Form();

    	$form->addField('product_downloadplus_detail_relation_'.$this->getDetailKey(), 'select', array(
            'name'  => 'downloadplus[detail][relation]['.$this->getDetailKey().']',
    	    'disabled' => ($disabled==null)?$this->getDetail()->isActive():$disabled,
            'value' => Mage::getModel('downloadplus/system_config_backend_catalog_product_filerelation')
                		->setDetail($this->getDetail())
                		->getValue(),
            'values'=> Mage::getModel('downloadplus/system_config_backend_catalog_product_filerelation')
                		->setProduct($this->getProduct())
                		->setDetail($this->getDetail())
                		->toOptionArray()
        ));

        return $form->toHtml();
    }

    /**
     * Prepare block Layout
     *
     */
     protected function _prepareLayout()
    {
    }

}
