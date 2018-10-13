<?php
class Krishinc_Customlinks_Model_Product_Link_Api extends Mage_Catalog_Model_Product_Link_Api
{
	 /**
     * Product link type mapping, used for references and validation
     *
     * @var array
     */
    protected $_typeMap = array(
        'related'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'up_sell'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'cross_sell'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL, 
        'otherformat'   => Krishinc_Customlinks_Model_Product_Link::LINK_TYPE_OTHERFORMAT,
        'componentsold' => Krishinc_Customlinks_Model_Product_Link::LINK_TYPE_COMPONENTSOLD,
        'bundlecontent' => Krishinc_Customlinks_Model_Product_Link::LINK_TYPE_BUNDLECONTENT,
        'grouped'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED
    ); 
}