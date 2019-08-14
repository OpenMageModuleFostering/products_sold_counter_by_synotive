<?php
/**
 * SPL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    SPL
 * @package     SPL_CounterSales
 * @copyright   Copyright (c) 2015 SPL. 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SPL_CounterSales_Model_Observer
{

    
    const ENABLEDSALES = 'catalog/spl_countersales/enabledsales';

    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfigFlag(self::ENABLEDSALES)) {
            $productId = $observer->getProduct()->getId();  
            $collection = Mage::getResourceModel('sales/order_item_collection');
             $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
                 $collection->getSelect()->join( array('order_history'=> sales_flat_order_status_history), 'order_history.parent_id = main_table.order_id', array('order_history.status'))
               ->columns(array('SUM(qty_ordered) as order_quantity'))
               ->where(sprintf("main_table.product_id = %s AND order_history.status = %s", $productId,'"complete"'));
               
            $salesCountArr=$collection->getData();
            $salescount=$salesCountArr[0]['order_quantity'];
            $observer->getProduct()->setSalesCount($salescount);
           
        }
    }

    public function catalogProductCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfigFlag(self::ENABLEDSALES)) {
            $productCollection = $observer->getCollection();
            foreach ($productCollection as $product) {
                $id = $product->getId();
                $collection = Mage::getResourceModel('sales/order_item_collection');
             $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
                 $collection->getSelect()->join( array('order_history'=> sales_flat_order_status_history), 'order_history.parent_id = main_table.order_id', array('order_history.status'))
               ->columns(array('SUM(qty_ordered) as order_quantity'))
               ->where(sprintf("main_table.product_id = %s AND order_history.status = %s", $id,'"complete"'));
            $salesCountArr=$collection->getData();
            $salescount=$salesCountArr[0]['order_quantity'];
                $product->setSalesCount($salescount);
            }
        }
    }
}