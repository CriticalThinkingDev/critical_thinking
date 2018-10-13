<?php
class Krishinc_Award_Model_Resource_Eav_Mysql4_Setup
    extends Mage_Eav_Model_Entity_Setup
{
    

    /**
     * Install a new entity
     *
     * It is very important to set the group index value.
     * If you don't - you will not see the attribute in the form in admin
     *
     * Also the attribute array index columns are defined quiet differently
     * between magento versions 1.4 - 1.5 and 1.6
     *
     * In versions 1.4 - 1.5 look at the file
     * app/code/core/Mage/Catalog/Model/Resource/Eav/Mysql4/Setup.php
     * to get an idea of all the indexes that can be defined
     *
     * In versions 1.6+ look at the file
     * app/code/core/Mage/Catalog/Model/Resource/Setup.php
     *
     *
     * @return array entities to install
     * @access public
     */
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes' => array(
                    'award' => array(
                        'type'              => 'text',
                        'backend'           => 'eav/entity_attribute_backend_array', 
                        'frontend'          => '',
                        'label'             => 'Award',
                        'input'             => 'multiselect',
                        'class'             => '',
                        'source'            => 'award/product_attribute_source_award',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => 0,
                        'visible_on_front'  => true,
                        'unique'            => false,
                        'group'             => 'General', 
                    ),
                ),
            ),
        );
 
    } 

  
}