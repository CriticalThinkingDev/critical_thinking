<?php

class Krishinc_Hardcode_Model_Observer
{
    public function cmsField($observer)
    {

        //get CMS model with data
        $model = Mage::registry('cms_page');
        //get form instance
        $form = $observer->getForm();
        //create new custom fieldset 'atwix_content_fieldset'
        $fieldset = $form->addFieldset('atwix_content_fieldset', array('legend'=>Mage::helper('cms')->__('Custom'),'class'=>'fieldset-wide'));
        //add new field
        $fieldset->addField('og_image', 'text', array(
            'name'      => 'og_image',
            'label'     => Mage::helper('cms')->__('Og Image'),
            'title'     => Mage::helper('cms')->__('Og Image'),
            'disabled'  => false,
            //set field value
            'value'     => $model->getOgImage()
        ));
 $fieldset->addField('og_url', 'text', array(
            'name'      => 'og_url',
            'label'     => Mage::helper('cms')->__('Og Url'),
            'title'     => Mage::helper('cms')->__('Og Url'),
            'disabled'  => false,
            //set field value
            'value'     => $model->getOgUrl()
        ));

        $fieldset->addField('og_title', 'text', array(
            'name'      => 'og_title',
            'label'     => Mage::helper('cms')->__('Og Title'),
            'title'     => Mage::helper('cms')->__('Og Title'),
            'disabled'  => false,
            //set field value
            'value'     => $model->getOgTitle()
        ));

        $fieldset->addField('og_type', 'text', array(
            'name'      => 'og_type',
            'label'     => Mage::helper('cms')->__('Og Type'),
            'title'     => Mage::helper('cms')->__('Og Type'),
            'disabled'  => false,
            //set field value
            'value'     => $model->getOgType()
        ));
        $fieldset->addField('og_description', 'text', array(
            'name'      => 'og_description',
            'label'     => Mage::helper('cms')->__('Og Description'),
            'title'     => Mage::helper('cms')->__('Og Description'),
            'disabled'  => false,
            //set field value
            'value'     => $model->getOgDescription()
        ));

    }
}
