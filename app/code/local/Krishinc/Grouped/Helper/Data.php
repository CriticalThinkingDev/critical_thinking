<?php

class Krishinc_Grouped_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $_associate_products = array();
    
    public function getAssociatedProducts($_product) {
        if ($_product->getTypeId() == 'grouped'){
            $associated_products = (isset($this->_associate_products[$_product->getId()]))?$this->_associate_products[$_product->getId()]:array();
            if(empty($associated_products)) {
                    $this->_associate_products[$_product->getId()] = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
            }
            
            return $this->_associate_products[$_product->getId()];
        } else {
            return array();
        }
    }
    
    public function getAssicatedProductTypes($_product) {
        $associate_products = $this->getAssociatedProducts($_product);
        $product_types = array();
        
        if(count($associate_products) > 0) {
            foreach($associate_products as $product) {
                if($product->getProductType() != "" && !in_array($product->getProductType(),$product_types)) {
                    $product_types[] = $product->getProductType();
                }
            }
        }
        return $product_types;
    }
	
	public function getAssicatedProductTypesAccesories($_product,$findfrom) {


          $associate_products = $this->getAssociatedProducts($_product);
          $product_types = array();
        $product_associated_media_type = array();

          if(count($associate_products) > 0) {
              foreach($associate_products as $product) {
                  if($product->getProductType()=='124' && !in_array($product->getAttributeText('media_type'),$product_associated_media_type)){
                      if($product->getAttributeText('media_type')=='Paperback Book' && !in_array('128',$findfrom)){
                          $product_types[$product->getId()][$product->getAttributeText('media_type')] = $product->getProductType();
                          $product_associated_media_type[] = $product->getAttributeText('media_type');
                      }
                      if($product->getAttributeText('media_type')=='eBook' && !in_array('126',$findfrom)){
                          $product_types[$product->getId()][$product->getAttributeText('media_type')] = $product->getProductType();
                          $product_associated_media_type[] = $product->getAttributeText('media_type');
                      }

                   }

              }
          }


        return $product_types;
    }
    
    public function getSeriesProduct($_product) {
        $className = Mage::getConfig()->getBlockClassName('grouped/product_view_seriesproducts');
        $block = new $className();  //  Krishinc_Grouped_Block_Product_View_Seriesproducts
        $associatedProduct = $block->getSeriesProducts($_product);
        return $associatedProduct;
    }
    
    public function getSoftwaredemolink($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $short_desc = '';
        if ($_product->getIsSoftwareDemos() == 1 && $_product->getSoftwareDemoFlag() != ""):
            if($_product->getSoftwareDemoFlag() == $constant_helper::SOFTWARE_DEMO_FLAG_ONLINE) {
                if($playdemoUrl = $_product->getPlaydemoUrl()) {
                    $short_desc .= '<div class="click_look_box">';
                     $name = (strlen($_product->getName()) > 50) ? substr($_product->getName(),0,47).'...' : $_product->getName();
                    $short_desc .= '<a target="_blank" title="'.$name.'" href="'.$playdemoUrl.'">'.Mage::helper('mastergrouped')->__('Online Software Demo').'</a>';
                    $short_desc .= '</div>';
                }
            } elseif ($_product->getSoftwareDemoFlag() == $constant_helper::SOFTWARE_DEMO_FLAG_DOWNLOAD) {
                $short_desc .= '<div class="click_look_box">';
                $name = (strlen($_product->getName()) > 50) ? substr($_product->getName(),0,47).'...' : $_product->getName();
                $short_desc .= '<a href="#soft_detail" target="_blank" title="'.$name.'" onclick="addPiroclass()" rel="inline-350"  class="pirobox_gall1 cd" >'.Mage::helper('mastergrouped')->__('Download Software Demo').'</a>';
                $short_desc .= '</div>';
                
                $short_desc .= '<div id="soft_detail" class="soft_detail" style="height:150px;display:none; background:white;">
                                <div class="soft_associated_product">
				 <div class="window">';
					$wd='';
                                        if($wd = $_product->getWindowDownload()):
                                            $wpath = Mage::getBaseDir('media').'/'.'blfa_files/'.$wd;
			            	$short_desc .=	'<a href="'.Mage::getBaseUrl('media').'blfa_files/'.$wd.'"  title="'.$wd.'">'.$this->__('WINDOWS').'</a>
				           		<span>('.Mage::helper('softwaredemos')->get_file_size($wpath ,'MB').')<br />
							            '.$_product->getWindowPlatform().'
							            <br/>
							            '.$_product->getWindowRam().'
					            </span>';
					else:
					    $short_desc .= $this->__('Coming Soon');
				    	endif;
					$short_desc .= '</div>
				 <div class="mac">';
					if($md =$_product->getMacDownload()):
		     				$mpath = Mage::getBaseDir('media').'/'.'blfa_files/'.$md;
			        			$short_desc .= '<a href="'.Mage::getBaseUrl('media').'blfa_files/'.$md.'"  title="'.$md.'">'.$this->__('MACINTOSH').'</a>
			            		<span>('.Mage::helper('softwaredemos')->get_file_size($mpath,'MB').')<br />
				            		'.$_product->getMacPlatform().'<br/>
							'.$_product->getMacRam().'
			            		</span>';
		            		
				        else:
                                            $short_desc .=  $this->__('Coming Soon');
		            		endif;
				$short_desc .= '</div>
                                            </div>
                                        </div>';
            }
            
        endif;
        return $short_desc;
    }
    
    public function getAddtocartHtml($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $addtocart_html = '';
        
		if($constant_helper::PRODUCT_TYPE_ANDROID_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="googleplay_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/google_play_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_IOS_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="appstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/itunes_store_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_WIN_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="winstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/win_store_btn.png') .'"/></a></div></div>';
        }
		else {
           
		   //if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType())
            $block_addtocart = Mage::app()->getLayout()->createBlock(
                                'catalog/product_view',
                                'mastergrouped_addtocart',
                                array('template' => 'catalog/product/view/type/mastergrouped/addtocart.phtml')
                        );
            
            $addtocart_html .= $block_addtocart->toHtml();
            
        }
		$addtocart_html .= $this->getWishlistLink($_product);
        return $addtocart_html;
    }
    public function getAddtocartCustomHtml($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $addtocart_html = '';

        if($constant_helper::PRODUCT_TYPE_ANDROID_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="googleplay_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/google_play_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_IOS_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="appstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/itunes_store_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_WIN_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="winstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/win_store_btn.png') .'"/></a></div></div>';
        }
        else {

            //if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType())
            $block_addtocart = Mage::app()->getLayout()->createBlock(
                'catalog/product_view',
                'mastergrouped_addtocart',
                array('template' => 'catalog/product/view/type/mastergrouped/addtocartcustom.phtml')
            );

            $addtocart_html .= $block_addtocart->toHtml();

        }
        $addtocart_html .= $this->getWishlistLink($_product);
        return $addtocart_html;
    }


    public function getWishlistLink($_product) {
        $addtocart_html = '';
        //$constant_helper = Mage::helper('grouped/constants');
        //if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType()) {
		$constant_helper = Mage::helper('grouped/constants');
        $proType = array($constant_helper::PRODUCT_TYPE_ANDROID_APP, $constant_helper::PRODUCT_TYPE_IOS_APP,$constant_helper::PRODUCT_TYPE_WIN_APP );
		if(!in_array($_product->getProductType(),$proType)){
            if (Mage::helper('wishlist')->isAllow()) :
                $_wishlistSubmitUrl = Mage::helper('wishlist')->getAddUrl($_product);
                $addtocart_html .= '<ul class="add-to-links">
                                        <li><a href="'.$_wishlistSubmitUrl.'" onclick="productAddToCartForm.submitLight(this, this.href); return false;" class="link-wishlist">'.Mage::helper('mastergrouped')->__('Add to Wishlist').'</a></li>
                                    </ul>';
            endif;
        //}
		}
        return $addtocart_html;
    }
    
    public function getProductTypeText($_product) {
        $constant_helper = Mage::helper('grouped/constants');
     
        if($_product->getProductType()==124 || $constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType() || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_ANDROID_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_IOS_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WIN_APP)) {

            if($pdffile = $_product->getPdf()) {
                $pdf_filepath = Mage::getModel('fileattributes/attribute_backend_file')->getFileUrl($pdffile); 
                $pdf_filepath1 = Mage::getModel('fileattributes/attribute_backend_file')->getFileDir($pdffile);
                if(file_exists($pdf_filepath1)) {
                    echo '<div class="click_look_box"><a href="'.Mage::getUrl('').'pdfcatalog/product/pdfview/id/'.$_product->getId().'"  rel="iframe-680-505"  class="pirobox_gall1"  title="'. $_product->getName().'">';
                    if($_product->getProductType()==124 || $_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_BOOKS || $_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_EBOOK) {
                        echo $this->__('View Sample Pages');
                    }
                    if($_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE || $_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE) {
                        echo $this->__('Software Screenshots');
                    }
                    if(($_product->getProductType() == $constant_helper::PRODUCT_TYPE_ANDROID_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_IOS_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WIN_APP)) {
                        echo $this->__('App Screenshots');
                    }
                    echo '</a></div>';
                }
            }
        }
    }
    
     public function getProductTabs($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        
        $block1 = Mage::app()->getLayout()->createBlock(
                            'catalog/product_view_tabs',
                            'mastergrouped_info_tabs',
                            array('template' => 'catalog/product/view/tabs.phtml')
                    );
        $block1->addTab('description','Description','catalog/product_view_description','catalog/product/view/description.phtml');
if($_product->getProductType()=='125' || $_product->getProductType()=='413'){
	 $block1->addTab('upsell_products','Bundle Contents','catalog/product_list_upsell','catalog/product/list/upsell.phtml');
       }
        
        //if(isset($params['product']) && $params['product'] != "") {
        if(!$_product->getIsMasterGroupProduct()) {
            $block1->addTab('additional','Details','catalog/product_view_attributes','catalog/product/view/attributes.phtml');
        }
        //}
        if(($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE) || ($_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_ANDROID_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_IOS_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WIN_APP) || ($_product->getProductType() ==  $constant_helper::PRODUCT_TYPE_EBOOK)) {
            $block1->addTab('system_requirements','System Requirements','grouped/product_view_Systemrequirements','catalog/product/view/system_requirements.phtml');
        }
        $block1->addTab('awards','Awards','award/product_view_award','award/product/view/awards.phtml');
        if(($_product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) || ($_product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG))
        {
            $block1->addTab('reviews','Reviews','review/product_view_list','review/product/view/list.phtml');
        }
        $block1->addTab('exam_prep','Exam Prep','catalog/product_view','catalog/product/view/exam_prep.phtml');
        
        $html1 = $block1->toHtml();
        
        return $html1;
    }
    
}
