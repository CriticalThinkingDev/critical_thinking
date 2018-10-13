<?php
/**
 * FreestyleSolutions_BizSyncXL_IndexController class
 *
 * @package FreestyleSolutions_BizSyncXL
 * @author Gary MacDougall
 **/

/**
 * Define DocBlock
 **/
define('REQUIRE_SECURE', false);
define('PRODUCE_SAVE_FAILED', -1);
define('LOGIN_FAILED', -2);
define('INVALID_STORE_CODE', -3);
define('SSL_REQUIRED', -4);
define('TESTMODE', false);

// the release version reflects the base
define('MODULE_CUSTOM_VERSION','1.1.9');
ini_set("display_errors", "On");

include_once ('FreestyleSolutions_BizSyncXL_Utils.php');
include_once ('FreestyleSolutions_BizSyncXL.class.php');


class FreestyleSolutions_BizSyncXL_IndexController extends Mage_Core_Controller_Front_Action 
{
	var $ActionValues = array();
	var $Username;
	var $Password;
	var $XLUsername;
	var $XLPassword;
	var $Store;
	var $Sku;
	var $Secure;
	var $Action;
	var $Data;
	var $EnableCompression;
	var $EnableLongDescription;
	var $EnableShortDescription;
	var $EnableProductName;	
	var $EnableReindex;
	var $BizSyncXL;
	var $image_type;
	var $image_name;
	var $image_data;
	var $moduleVersion = MODULE_BASE_VERSION;
        var $is_final;                                        
	/**
	 * FreestyleSolutions_BizSyncXL_IndexController function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
/*
	function FreestyleSolutions_BizSyncXL_IndexController ()
	{
	}
*/

	/**
	 * indexAction function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	public function indexAction()
	{
                        //header('Context-Type: application/xml');
                
			// Open the XML output and root
			writeXmlDeclaration();
			writeStartTag("BizSync");

			if (TESTMODE == false)
			{
				if (count ($GLOBALS['HTTP_POST_VARS']))
				{
					$this->ActionValues = $GLOBALS['HTTP_POST_VARS'];
				} else {
					if (count($GLOBALS['HTTP_GET_VARS']))
					{
						$this->ActionValues = $GLOBALS['HTTP_GET_VARS'];
					} else {
						if (count($GLOBALS['_POST']))
						{
							$this->ActionValues = $GLOBALS['_POST'];
						} else {
							RestResultError (1, "No posted variables or data. ", __FUNCTION__);
						}
					}
				}
				try
				{
					if (isset($_SERVER['HTTPS']))
					{
						$this->Secure = intval($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1');
					}
				} catch (Exception $e) {
					RestResultError (1, "Fatal error on service.", __FUNCTION__);
				}

				$this->BizSyncXL = new FreestyleSolutions_BizSyncXL ($this->ActionValues);

				// Set the incoming posted or requested vars.
				$this->Username = $this->ActionValues['Username'];
				$this->Password = $this->ActionValues['Password'];
				$this->XLUsername = $this->ActionValues['XLUsername'];
				$this->XLPassword = $this->ActionValues['XLPassword'];
				$this->Sku = $this->ActionValues['sku'];
				$this->image_data = $this->ActionValues['image_data'];
				$this->image_name = $this->ActionValues['image_name'];
				$this->image_type = $this->ActionValues['image_type'];
				$this->Action = $this->ActionValues['action'];
				$this->Store = $this->ActionValues['store'];
				$this->Data = $this->ActionValues['data'];
				$this->EnableLongDescription = intval($this->ActionValues['sync_product_longdescription']);
				$this->EnableShortDescription = intval($this->ActionValues['sync_product_shortdescription']);
				$this->EnableProductName = intval($this->ActionValues['sync_product_name']);
                                $this->EnableReindex = intval($this->ActionValues['enable_reindex']);
                                $this->is_Final = intval($this->ActionValues['is_final']);
			}
			
			$base_path = Mage::getBaseDir('base');

			// check for SSL
			$secure = false;
			try
			{
		            if (isset($_SERVER['HTTPS']))
		            {
		                $secure = ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1');
		            }
			}
			catch(Exception $e)
			{
				die ("Fatal error on service.");
			}

			// include the Mage engine
			// gjm: 3/18/2016 - not needed.
			//require_once $base_path . '/app/Mage.php';

			// default store code is always 1.
			$this->BizSyncXL->BizSyncStoreID = 1;

			if (isset($_REQUEST['store']))
			{
				$this->BizSyncXL->BizSyncStoreID = intval(trim($_REQUEST['store']));
			} else {
			// see if its posted.
				if (isset($_POST['store']))
				{
					$this->BizSyncXL->BizSyncStoreID = intval(trim($_POST['store']));
				}
			}

			umask(0);
			// using output buffering to get around headers that magento is setting after we've started output
			ob_start();

			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");	

			try
			{
		            // start the mage engine
		            Mage::app($storeCode);
			}
			catch (Mage_Core_Model_Store_Exception $e)
			{
		            RestResultError(100, "Invalid Store Code.");
		            writeCloseTag("BizSync");
		            exit;
			}

			// Enforse SSL
			if (!$secure && REQUIRE_SECURE)
			{
			    RestResultError(10, 'A secure (https://) connection is required.');
			}
			else
			{

			    // If the admin module is installed, we make use of it
			    if ($this->checkAdminLogin())
			    {
					switch (trim(strtolower($this->Action)))
					{
						case 'getconfiguration':
							$this->Action_GetConfiguration();
							break;
						case 'testconnection':
							$this->Action_TestConnection();
							break;
						case 'addattribute':
							$this->Action_AddAttribute();
							break;
						case 'getstore':
							$this->Action_GetStore();
							break;
						case 'getstores':
							$this->Action_GetStores();
							break;
						case 'getcount':
							$this->Action_GetCount();
							break;
						case 'getorders':
							$this->Action_GetOrders();
							break;
						case 'getstatuscodes':
							$this->Action_GetStatusCodes();
							break;
						case 'updateorder':
							$this->Action_UpdateOrder();
							break;
						case 'syncproduct':
							$this->Action_SyncProduct();
							break;
						case 'updateproductquantities':
							$this->Action_UpdateProductQuantities();
							break;
						case 'getshippingmethods' :
							$this->Action_GetShippingMethods();
							break;
						case 'getproductfields' :
							$this->Action_GetProductFields();
							break;
						case 'getpaymentmethods' :
							$this->Action_GetPaymentMethods();
							break;
						case 'getcreditcardtypes' :
							$this->Action_GetCreditCardTypes();
							break;
						case 'syncquantities' :
							$this->Action_SyncQuantities();
							break;
						case 'syncprices' :
							$this->Action_SyncPrices();
							break;
						case 'syncimage' :
							$this->Action_SyncImage();
							break;
						case 'getorderpaymentdata' :
							$this->Action_GetOrderPaymentData ();
							break;
						case 'getproductid' :
							$this->Action_GetProductID ();
							break;
						case 'deleteproduct' :
							$this->Action_DeleteProduct ();
							break;
						case 'synccustomdata' :
							$this->Action_SyncCustomData ();
							break;
						case 'removecreditcard' :
							$this->Action_RemoveCreditCard ();
							break;
						case 'getinfo' :
							$this->Action_GetInfo ();
							break;
						case 'synccustomer' :
							$this->Action_SyncCustomer();
							break;
						case 'synccleanup' :
							$this->Action_SyncCleanup();
							break;
						case 'getshipments' :
							$this->Action_GetShipments();
							break;
						case 'channelcleanup' :
							$this->Action_SyncCleanup();
							break;
					default:
				    	RestResultError (20, "'$this->Action' is not supported.");
				}
			    } else {
			   	 RestResultError (10, 'Invalid username or password.');
			    }
			}
			// Close the output
			writeCloseTag("BizSync");
			ob_flush();
			return;
	}

	/**
	 * Action_GetConfiguration function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function checkAdminLogin()
	{
		return $this->BizSyncXL->checkAdminLogin();
	}

	/**
	 * Action_GetConfiguration function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetConfiguration()
	{
		$this->BizSyncXL->Action_GetConfiguration();
	}

	/**
	 * Action_TestConnection function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_TestConnection()
	{
		$this->BizSyncXL->Action_TestConnection();
	}
                
        /**
	 * Action_AddAttribute function
	 *
	 * @return void
	 * @author Allan Adriano
	 **/
	private function Action_AddAttribute()
	{
		$this->BizSyncXL->Action_AddAttribute();
	}
        
	/**
	 * Action_GetStore function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetStore()
	{
		$this->BizSyncXL->Action_GetStore();
	}

	/**
	 * Action_GetStores function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetStores()
	{
		$this->BizSyncXL->Action_GetStores();
	}	

	/**
	 * Action_GetOrders function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetOrders()
	{
		$this->BizSyncXL->Action_GetOrders();
	}	

	/**
	 * Action_GetStatusCodes function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetStatusCodes()
	{
		$this->BizSyncXL->Action_GetStatusCodes();
	}

	/**
	 * Action_UpdateOrder function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_UpdateOrder()
	{
		$this->BizSyncXL->Action_UpdateOrder();
	}

	/**
	 * Action_SyncProduct function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_SyncProduct()
	{
		$this->BizSyncXL->Action_SyncProduct();
	}

        /**
	 * Action_SyncCleanup function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_SyncCleanup()
	{
		$this->BizSyncXL->Action_SyncCleanup();
	}
        
	/**
	 * Action_UpdateProductQuantities function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_UpdateProductQuantities()
	{
		$this->BizSyncXL->Action_UpdateProductQuantities();
	}

	/**
	 * Action_GetShippingMethods function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetShippingMethods()
	{
		$this->BizSyncXL->Action_GetShippingMethods();
	}

	/**
	 * Action_GetProductFields function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetProductFields()
	{
		$this->BizSyncXL->Action_GetProductFields();
	}

        /**
	* Action_GetInfo function
	*
	* @return void
	* @author Gary MacDougall
	**/
        /* *@AA:10/14/16 11:27:10 AM:BSXL-456 */
	private function Action_GetInfo()
	{
		$this->BizSyncXL->Action_GetInfo();
	}
        
	/**
	 * Action_GetPaymentMethods function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetPaymentMethods()
	{
		$this->BizSyncXL->Action_GetPaymentMethods();
	}
                
	/**
	 * Action_GetCreditCardTypes function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetCreditCardTypes()
	{
		$this->BizSyncXL->Action_GetCreditCardTypes();
	}

	/**
	 * Action_SyncQuantities function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_SyncQuantities()
	{
		$this->BizSyncXL->Action_SyncQuantities();
	}

	/**
	 * Action_SyncPrices function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_SyncPrices()
	{
		$this->BizSyncXL->Action_SyncPrices();
	}

	/**
	 * Action_SyncImage function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_SyncImage()
	{
		$this->BizSyncXL->Action_SyncImage();
	}

	/**
	 * Action_GetOrderPaymentData function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetOrderPaymentData()
	{
		$this->BizSyncXL->Action_GetOrderPaymentData();
	}

	/**
	 * Action_GetProductID function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_GetProductID()
	{
		$this->BizSyncXL->Action_GetProductID();
	}

	/**
	 * Action_DeleteProduct function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_DeleteProduct()
	{
		$this->BizSyncXL->Action_DeleteProduct();
	}
	
	/**
	 * Action_RemoveCreditCard function
	 *
	 * @return void
	 * @author Gary MacDougall
	 **/
	private function Action_RemoveCreditCard()
	{
		$this->BizSyncXL->Action_RemoveCreditCard();
	}
	
}; // END CLASS
?>