<?php

/**
 * Product:       Xtento_TrackingImport (2.3.1)
 * ID:            sPKee7U2Pf2yLNVVEr3/61bKJloT5kL/MaX0TUxtHj4=
 * Packaged:      2017-11-07T02:06:49+00:00
 * Last Modified: 2017-07-01T00:01:02+02:00
 * File:          app/code/local/Xtento/TrackingImport/Model/Import/Action/Order/Invoice.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_TrackingImport_Model_Import_Action_Order_Invoice extends Xtento_TrackingImport_Model_Import_Action_Abstract
{
    public function invoice()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();
        $updateData = $this->getUpdateData();

        // Prepare items to process
        $itemsToProcess = array();
        if (isset($updateData['items']) && !empty($updateData['items'])) {
            foreach ($updateData['items'] as $itemRecord) {
                $itemRecord['sku'] = strtolower($itemRecord['sku']);
                if (isset($itemsToProcess[$itemRecord['sku']])) {
                    $itemsToProcess[$itemRecord['sku']]['qty'] = $itemsToProcess[$itemRecord['sku']]['qty'] + $itemRecord['qty'];
                } else {
                    $itemsToProcess[$itemRecord['sku']]['sku'] = $itemRecord['sku'];
                    $itemsToProcess[$itemRecord['sku']]['qty'] = $itemRecord['qty'];
                }
            }
        }

        // Customization: Only invoice shipped items
        /*$itemsToProcess = array();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            if ($orderItem->getQtyShipped() > $orderItem->getQtyInvoiced() && $this->canInvoiceItem($orderItem)) {
                $itemsToProcess[strtolower($orderItem->getSku())]['sku'] = strtolower($orderItem->getSku());
                $itemsToProcess[strtolower($orderItem->getSku())]['qty'] = ($orderItem->getQtyShipped() - $orderItem->getQtyInvoiced());
            }
        }*/

        // Check if order is holded and unhold if should be shipped
        if ($order->canUnhold() && $this->getActionSettingByFieldBoolean('invoice_create', 'enabled')) {
            $order->unhold()->save();
            $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s': Order was unholded so it can be invoiced.", $order->getIncrementId()));
        }

        // Create Invoice
        if ($this->getActionSettingByFieldBoolean('invoice_create', 'enabled')) {
            if ($order->canInvoice()) {
                $invoice = false;
                $doInvoiceOrder = true;
                // Partial invoicing support:
                if ($this->getActionSettingByFieldBoolean('invoice_partial_import', 'enabled')) {
                    // Prepare items to invoice for prepareInvoices
                    $qtys = array();
                    foreach ($order->getAllItems() as $orderItem) {
                        // How should the item be identified in the import file?
                        if ($this->getProfileConfiguration()->getProductIdentifier() == 'sku') {
                            $orderItemSku = strtolower(trim($orderItem->getSku()));
                        } else if ($this->getProfileConfiguration()->getProductIdentifier() == 'entity_id') {
                            $orderItemSku = trim($orderItem->getProductId());
                        } else if ($this->getProfileConfiguration()->getProductIdentifier() == 'attribute') {
                            $product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
                            if ($product->getId()) {
                                $orderItemSku = strtolower(trim($product->getData($this->getProfileConfiguration()->getProductIdentifierAttributeCode())));
                            } else {
                                $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s': Product SKU '%s', product does not exist anymore and cannot be matched for importing.", $order->getIncrementId(), $orderItem->getSku()));
                                continue;
                            }
                        } else {
                            $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s': No method found to match products.", $order->getIncrementId()));
                            return true;
                        }
                        // Item matched?
                        if (isset($itemsToProcess[$orderItemSku])) {
                            $orderItemId = $orderItem->getId();
                            $qtyToProcess = $itemsToProcess[$orderItemSku]['qty'];
                            $maxQty = $orderItem->getQtyToInvoice();
                            if ($qtyToProcess > $maxQty) {
                                if (($orderItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE || $orderItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL)
                                    && $orderItem->getParentItem() && $orderItem->getParentItem()->getQtyToInvoice() > 0) {
                                    // Has a parent item that must be invoiced instead
                                    $orderItemId = $orderItem->getParentItem()->getId();
                                    $maxQty = $orderItem->getParentItem()->getQtyToInvoice();
                                    if ($qtyToProcess > $maxQty) {
                                        $qty = round($maxQty);
                                    } else {
                                        $qty = round($qtyToProcess);
                                    }
                                } else {
                                    $qty = round($maxQty);
                                }
                            } else {
                                $qty = round($qtyToProcess);
                            }
                            if ($qty > 0) {
                                $itemsToProcess[$orderItemSku]['qty'] -= $qty;
                                $qtys[$orderItemId] = round($qty);
                            } else {
                                $qtys[$orderItemId] = 0;
                            }
                        } else {
                            $qtys[$orderItem->getId()] = 0;
                        }
                    }
                    if (!empty($qtys)) {
                        $invoice = $order->prepareInvoice($qtys);
                        // Check if proper items have been found in $qtys
                        if (!$invoice->getTotalQty()) {
                            $doInvoiceOrder = false;
                            $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s' has NOT been invoiced. Partial invoicing enabled, however the items specified in the import file couldn't be found in the order. (Could not find any qtys to invoice)", $order->getIncrementId()));
                        }
                    } else {
                        // We're supposed to import partial shipments, but no SKUs were found at all. Do not touch invoice.
                        $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s' has NOT been invoiced. Partial invoicing enabled, however the items specified in the import file couldn't be found in the order.", $order->getIncrementId()));
                        $doInvoiceOrder = false;
                    }
                } else {
                    $invoice = $order->prepareInvoice();
                }

                if ($invoice && $doInvoiceOrder) {
                    if ($this->getActionSettingByFieldBoolean('invoice_capture_payment', 'enabled') && $invoice->canCapture()) {
                        // Capture order online
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                    } else if ($this->getActionSettingByFieldBoolean('invoice_mark_paid', 'enabled')) {
                        // Set invoice status to Paid
                        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
                    }

                    try {
                        $invoice->register();
                    } catch (Exception $e) {
                        Mage::throwException($e->getMessage());
                        return false;
                    }
                    if ($this->getActionSettingByFieldBoolean('invoice_send_email', 'enabled')) {
                        $invoice->setEmailSent(true);
                    }
                    $invoice->getOrder()->setIsInProcess(true);

                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();

                    $this->setHasUpdatedObject(true);

                    if ($this->getActionSettingByFieldBoolean('invoice_send_email', 'enabled')) {
                        $invoice->sendEmail(true, '');
                        $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s' has been invoiced and the customer has been notified.", $order->getIncrementId()));
                    } else {
                        $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s' has been invoiced and the customer has NOT been notified.", $order->getIncrementId()));
                    }

                    unset($invoice);
                }
            } else {
                $this->addDebugMessage(Mage::helper('xtento_trackingimport')->__("Order '%s' has NOT been invoiced. Order already invoiced or order status not allowing invoicing.", $order->getIncrementId()));
            }
        }

        return true;
    }

    protected function canInvoiceItem($item, $qtys=array())
    {
        if ($item->getLockedDoInvoice()) {
            return false;
        }
        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($child->getQtyToInvoice() > 0) {
                            return true;
                        }
                    } else {
                        if (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                            return true;
                        }
                    }
                }
                return false;
            } else if($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToInvoice() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
                }
            }
        } else {
            return $item->getQtyToInvoice() > 0;
        }
    }
}