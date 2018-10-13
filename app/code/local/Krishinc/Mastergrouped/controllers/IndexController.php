<?php

class Krishinc_Mastergrouped_IndexController extends Mage_Core_Controller_Front_Action
{
    /*
     *  Get the the result in json format from ajax, for updating html content of master grouped product view page on change of dropdown products.
     */
    public function indexAction() {
        $this->loadLayout(false);
        $result = array();
        $params = $this->getRequest()->getParams();
        $_helper = Mage::helper('catalog/output');
        $constant_helper = Mage::helper('grouped/constants');
        
        /* check that product from the dropdown is selected or "Select media & license" option selected */
        if(isset($params['product']) && $params['product'] != "") {
            $product_arr = explode("_",$params['product']);
            
            //if (!Mage::registry('product') && $product_arr[1]) {
                $product = Mage::getModel('catalog/product')->load($product_arr[1]);
                Mage::register('product', $product);
           // }
            $_product = Mage::registry('product');
            
            $result['product_image'] = $this->getProductImage($_product,true);
        } else {
            /* if "Select media & license" option selected then the params in ajax call will be different and use them to get the data. */
            if (isset($params['original_product']) && $params['original_product'] != "") {
                $product = Mage::getModel('catalog/product')->load($params['original_product']);
                Mage::register('product', $product);
            }
            //$_product = $product;
            $_product = Mage::registry('product');
            
            /* Product detail of the original product but image and pdf/software link would be of first book/software product (if available) */
            if(isset($params['product_image_id']) && $params['product_image_id'] != "") {
                //$product_arr = explode("_",$params['product_image_id']);
                $image_product = Mage::getModel('catalog/product')->load($params['product_image_id']);
                $result['product_image'] = $this->getProductImage($image_product,false);
            } else {
                $result['product_image'] = $this->getProductImage($_product,true);
            }
        }
        
        /* Get all the details of the product */
        if($_product) {
            $grade = $this->getGrade($_product);
	    
            $short_desc = $this->getShortDesc($_product);
            
            $result['product_id'] = $_product->getId();
            $result['product_name'] = '<h1>'.$_helper->productAttribute($_product, $_product->getName(), 'name').'</h1>
                                        <h2>'.$_helper->productAttribute($_product, $_product->getSubTitle(), 'sub_title').'</h2>';
            
            $result['product_grade_subject'] = $grade;
            $result['product_short_description'] = $short_desc;
            
            $result['product_addtocart'] = $this->getAddtocartHtml($_product);
            
            $result['product_related'] = $this->getRelatedProducts($_product);
            
        //    $result['product_series'] = $this->getSeriesProducts($_product);
            
            
            $result['tab_html'] = $this->getProductTabs($_product);
            
            echo Mage::helper('core')->jsonEncode($result);
            $this->renderLayout();
            exit;
        }
    }
    
    public function getShortDesc($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $short_desc = '';
        $params = $this->getRequest()->getParams();
        
        /* if "Select media & license" option selected then the params in ajax call will be product_software_id in which send the first software id. */
        if($params['product'] == '' && isset($params['product_software_id']) && $params['product_software_id'] != "") {
            $product_arr = explode("_",$params['product_software_id']);
            $product_software = Mage::getModel("catalog/product")->load($product_arr[1]);
            $short_desc .= $this->getSoftwaredemolink($product_software);
        } else {
            $short_desc .= $this->getSoftwaredemolink($_product);
        }
        
        
        if ($_product->getShortDescription()):
            $short_desc .= '<div class="short-description">'.
                                Mage::helper('catalog/output')->productAttribute($_product, nl2br($_product->getShortDescription()), 'short_description')
                            .'</div>';
        endif;
        
        if(!$_product->isAvailable() && $_product->getAvailableText()):
            $short_desc .= '<div class="available_text"><font color="#0033CC">'.Mage::helper('catalog/output')->productAttribute($_product, ($_product->getAvailableText()), 'available_text').'</font></div>';
        endif;
        
        return $short_desc;
    }
    
    public function getSoftwaredemolink($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $short_desc = '';
        if ($_product->getIsSoftwareDemos() == 1 && $_product->getSoftwareDemoFlag() != ""):
            if($_product->getSoftwareDemoFlag() == $constant_helper::SOFTWARE_DEMO_FLAG_ONLINE) {
                if($playdemoUrl = $_product->getPlaydemoUrl()) {
                    $short_desc .= '<div class="click_look_box">';
                    $short_desc .= '<a target="_blank" title="'.Mage::helper('mastergrouped')->__('Online Software Demo').'" href="'.$playdemoUrl.'">'.Mage::helper('mastergrouped')->__('Online Software Demo').'</a>';
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
    
    public function getGrade($_product) {
        $grade = '';
        if($_product->getGrade()):
            $grade_val = Mage::getModel('catalog/product')->getProductGrade($_product->getGrade());
            $grade .= '<p class="grades"><b>'.Mage::helper('mastergrouped')->__('Grades: ').'</b>'.$grade_val.'</p>';
        endif;
        if($_product->getSubject()):
            $subject = Mage::getModel('catalog/product')->getProductSubject($_product->getSubject());
            $grade .= '<p class="subject subject_area"><b>'.Mage::helper('mastergrouped')->__('Subject(s): ').'</b>'.$subject.'</p>';
        endif;
        
        return $grade;
    }
    
    public function getAddtocartHtml($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $addtocart_html = '';
        if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType()) {
            //if($_product->isSaleable()):
                $block_addtocart = $this->getLayout()->createBlock(
                                    'catalog/product_view',
                                    'mastergrouped_addtocart',
                                    array('template' => 'catalog/product/view/type/mastergrouped/addtocart.phtml')
                            );
                
                $addtocart_html .= $block_addtocart->toHtml();
            //else:
            //    if(is_null($_product->getAvailableText())){
            //        $addtocart_html .= '<p class="availability out-of-stock"><span>'.Mage::helper('mastergrouped')->__('Out Of Stock').'</span></p> ';
            //    } else {
            //        $addtocart_html .= '<p class="availability coming-soon"><span>'.Mage::helper('mastergrouped')->__('Coming soon').'</span></p> ';
            //    }
            //endif;
            
            $addtocart_html .= $this->getWishlistLink($_product);
        }
        elseif($constant_helper::PRODUCT_TYPE_ANDROID_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="googleplay_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/google_play_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_WIN_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="winstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/win_store_btn.png') .'"/></a></div></div>';
        }
        elseif($constant_helper::PRODUCT_TYPE_IOS_APP == $_product->getProductType()) {
            $addtocart_html .= '<div id="appstore_div"><div><a href="'.$_product->getAppUrl().'" target="_blank"><img src="'. Mage::getDesign()->getSkinUrl('images/catalog/itunes_store_btn.png') .'"/></a></div></div>';
        }
        return $addtocart_html;
    }
    
    public function getRelatedProducts($_product) {
        $_related_products_html = '';
        $constant_helper = Mage::helper('grouped/constants');
        
        if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType()) {
            $block_related_products = $this->getLayout()->createBlock(
                                    'relatedproducts/relatedproducts',
                                    'mastergrouped_related_products',
                                    array('template' => 'catalog/product/list/awrelated.phtml')
                            );
            $_related_products_html .= $block_related_products->toHtml();
        }
        return $_related_products_html;
    }
    
    
    //public function getSeriesProducts($_product) {
    //    $_series_products_html = '';
    //    $constant_helper = Mage::helper('grouped/constants');
    //    
    //    if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType()) {
    //        $block_series_products = $this->getLayout()->createBlock(
    //                                'grouped/product_view_seriesproducts',
    //                                'mastergrouped_series_products',
    //                                array('template' => 'catalog/product/view/seriesproducts.phtml')
    //                        );
    //        $_series_products_html .= $block_series_products->toHtml();
    //    }
    //    return $_series_products_html;
    //}
    
    public function getProductImage($_product, $displayproductimagetitle = true) {
        $block = $this->getLayout()->createBlock(
                            'catalog/product_view_media',
                            'mastergrouped_info_media',
                            array('template' => 'catalog/product/view/type/mastergrouped/media.phtml')
                    );
        $block->setProduct($_product);
        $block->setDisplayproductimagetitle($displayproductimagetitle);
        $html = $block->toHtml();
        return $html;
    }
    
    public function getProductTabs($_product) {
        $constant_helper = Mage::helper('grouped/constants');
        $params = $this->getRequest()->getParams();
        
        $block1 = $this->getLayout()->createBlock(
                            'catalog/product_view_tabs',
                            'mastergrouped_info_tabs',
                            array('template' => 'catalog/product/view/tabs.phtml')
                    );
        $block1->addTab('description','Description','catalog/product_view_description','catalog/product/view/description.phtml');
        
        if(isset($params['product']) && $params['product'] != "") {
            $block1->addTab('additional','Details','catalog/product_view_attributes','catalog/product/view/attributes.phtml');
        }
        if(($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_ANDROID_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_IOS_APP) || ($_product->getProductType() == $constant_helper::PRODUCT_TYPE_WIN_APP)) {
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
    
     public function getWishlistLink($_product) {
        $addtocart_html = '';
        $constant_helper = Mage::helper('grouped/constants');
        if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType()) {
            if (Mage::helper('wishlist')->isAllow()) :
                $_wishlistSubmitUrl = Mage::helper('wishlist')->getAddUrl($_product);
                $addtocart_html .= '<ul class="add-to-links">
                                        <li><a href="'.$_wishlistSubmitUrl.'" onclick="productAddToCartForm.submitLight(this, this.href); return false;" class="link-wishlist">'.Mage::helper('mastergrouped')->__('Add to Wishlist').'</a></li>
                                    </ul>';
            endif;
        }
        return $addtocart_html;
    }
    
    public function searchresultAction() {
        $this->loadLayout(false);
        $result = array();
        $params = $this->getRequest()->getParams();
        $_helper = Mage::helper('catalog/output');
        $constant_helper = Mage::helper('grouped/constants');
        
        /* check that product from the dropdown is selected or "Select media & license" option selected */
        if(isset($params['product']) && $params['product'] != "") {
            $product_arr = explode("_",$params['product']);
            
            //if (!Mage::registry('product') && $product_arr[1]) {
                $product = Mage::getModel('catalog/product')->load($product_arr[1]);
                Mage::register('product', $product);
           // }
		    if($product->getProductType()=='124' && $product->getAttributeText('media_type')=='Paperback Book'):
                $result['class_c'] = 'books_btn';
            endif;
            if($product->getProductType()=='124' && $product->getAttributeText('media_type')=='eBook'):
                $result['class_c'] = 'ebook_btn';
            endif;
            $_product = Mage::registry('product');
          
            $result['product_image'] = $this->getProductImageResult($_product);
        } else {
            /* if "Select media & license" option selected then the params in ajax call will be different and use them to get the data. */
            if (isset($params['original_product']) && $params['original_product'] != "") {
                $product = Mage::getModel('catalog/product')->load($params['original_product']);
                Mage::register('product', $product);
            }
            //$_product = $product;
            $_product = Mage::registry('product');
            
            /* Product detail of the original product but image and pdf/software link would be of first book/software product (if available) */
            if(isset($params['product_image_id']) && $params['product_image_id'] != "") {
                //$product_arr = explode("_",$params['product_image_id']);
                $image_product = Mage::getModel('catalog/product')->load($params['product_image_id']);
                $result['product_image'] = $this->getProductImageResult($image_product);
            } else {
                $result['product_image'] = $this->getProductImageResult($_product);
            }
        }
		
        
        $result['product_id'] = $_product->getId();
        $result['product_name'] = $_helper->productAttribute($_product, $_product->getName(), 'name');
		$result['product_url'] = $_product->getProductUrl();
       
        $grade = $this->grade_subject_search($_product);
	$short_desc = $this->getDescSearch($_product);
$avaibilityPreorder = $this->avaibilityPreorder($_product);
        $learnmore = false;
        //if(strlen($_product->getShortDescription()) > 250 && strpos($short_desc,'...') !== false) {
        //    $learnmore = true;
        //    $short_desc .= '<a href="#" class="link-learn">'. Mage::helper('mastergrouped')->__('More') .'</a>';
        //}
        $award_search = $this->getAwardDetailSearch($_product);
        
        $result['product_grade_subject'] = $grade;
 $result['product_avaibilityPreorder'] = $avaibilityPreorder;
        $result['product_short_description'] = $short_desc;
        $result['product_desc_learn_more'] = $learnmore;
        $result['product_award_search'] = $award_search;
        
        $result['product_wishlist'] = $this->getwishlistlinksearch($_product);
        
        $result['product_flagnotification'] = $this->getFlagNotification($_product);
		$result['has_product_flagnotification'] = Mage::helper('mastergrouped')->hasFlagNotification($_product);
        echo Mage::helper('core')->jsonEncode($result);
            $this->renderLayout();
            exit;
    }
    
    public function getProductImageResult($_product) {
        $html = '<img src="'.Mage::helper('catalog/image')->init($_product, 'small_image')->resize(129,138).'" width="129" height="138"
	alt="'.$_product->getName().'" />';
        
        return $html;
    }
    
   /* public function getShortDescSearch($_product) {
        $html = Mage::helper('core/string')->truncate($_product->getShortDescription(),250,'...', $recomnded, false);
		$html .= ' <a href="'.$_product->getProductUrl().'" class="link-learn">'.$this->__('More').'</a>';
		return $html;
    }*/

 public function getShortDescSearch($_product) {
         $html = ' <a href="'.$_product->getProductUrl().'" class="link-learn">'.Mage::helper('core/string')->truncate($_product->getShortDescription(),250,'...', $recomnded, false).'. More»</a>';
       return $html;
    }
     public function getDescSearch($_product) {
        $html = ' <a href="'.$_product->getProductUrl().'" class="link-learn">'.Mage::helper('core/string')->truncate(Mage::helper('core')->stripTags($_product->getDescription()),250,'...', $recomnded, false).'</a>';
      

        return $html;
    }
    public function getAwardDetailSearch($_product) {
        $html = '';
        if ($_product->getAward() != '') {
            $html.= '<ul class="search_result_product_award_list">
                        <li class="award_list_item">	
                            <img src="'.Mage::getDesign()->getSkinUrl('images/award_winner_star.gif').'" width="11" height="10" style="margin-top:2px;" alt="award" />&nbsp;'.((strstr($_product->getAward(),","))?"Multiple Award Winner":"Award Winner").'
                        </li>
                    </ul>';
	}
        return $html;
    }

public function avaibilityPreorder($_product){
        $avaibilityPreorder = '';
        if($_product->getAvaibilityPreorder()):
            $avar = $_product->getAvaibilityPreorder();
            $avaibilityPreorder .= '<p id="avaibility_pre_order" style="color: #CC0000; min-height: 20px; font-size: 12px;"><strong>'.$avar.'</strong></p>';
            endif;
        return $avaibilityPreorder;

    }
    
    public function grade_subject_search($_product) {
        $grade_html = '';
        if($_product->getGrade()):
              $count = count(explode(',',$_product->getGrade()));
                if($count>1){
                    $labelGrade = 'Grades';
                }else{
                    $labelGrade = 'Grade';
                }
	    $grade = Mage::getModel('catalog/product')->getProductGrade($_product->getGrade());
	    $grade_html .= '<p>'.Mage::helper('mastergrouped')->__($labelGrade.': ').$grade.'</p>';
	endif;
        if($_product->getSubject()):
            $subject = Mage::getModel('catalog/product')->getProductSubject($_product->getSubject());
            $grade_html .= '<p class="subject_area">'.$subject.'</p>';
        endif;
        return $grade_html;
    }
    
    public function getwishlistlinksearch($_product) {
		$constant_helper = Mage::helper('grouped/constants');
        $proType = array($constant_helper::PRODUCT_TYPE_ANDROID_APP, $constant_helper::PRODUCT_TYPE_IOS_APP,$constant_helper::PRODUCT_TYPE_WIN_APP );
        if(!in_array($_product->getProductType(),$proType)){
        $html = '';
        //$constant_helper = Mage::helper('grouped/constants');
        //if($constant_helper::PRODUCT_TYPE_BOOKS == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WINMAC_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_WIN_SOFTWARE == $_product->getProductType() || $constant_helper::PRODUCT_TYPE_EBOOK == $_product->getProductType()) {
            if (Mage::helper('wishlist')->isAllow()) :
                $html .= '<p> <a href="'.Mage::helper('wishlist')->getAddUrl($_product).'" class="link-wishlist">'.Mage::helper("mastergrouped")->__("Add to Wishlist").'</a> </p>';
            endif;
        //}
		}
        return $html;
    }
    
    public function getFlagNotification($_product) {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('00:00:00')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
	$todayEndOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('23:59:59')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                
        $html = '';
       /* if(($_product->getIsSale() == 1) &&(!isset($_GET['is_sale']))):
            $html .= '<img width="58" height="40" class="sale-icon" alt="Sale" src="'.Mage::getDesign()->getSkinUrl('images/icon_sale.png').'"/>';
	endif;
	
	if((strtotime($_product->getNewsFromDate()) <= strtotime($todayStartOfDayDate)) && (strtotime($_product->getNewsToDate()) >= strtotime($todayEndOfDayDate))): 
            $html .= '<img width="56" height="40" class="new-icon" alt="New" src="'.Mage::getDesign()->getSkinUrl('images/icon_new.png').'"/>';
	endif;*/
	
        if($_product->getCoreCurriculum() == 1):
           $html .= '<span class="core-curriculum-icon"> Full curriculum</span>';	
	endif;
        
        return $html;
    }
}
