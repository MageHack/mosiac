<?php

class Meanbee_Mosaic_Block_Product_List_Mosaic extends Mage_Catalog_Block_Product_List {

    protected $_bestselling_groups = 3;

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {

            $visibility = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            );


            $collection = Mage::getResourceModel('catalog/product_collection')
                            ->addAttributeToSelect('image')
                            ->addAttributeToFilter('visibility', $visibility);

            $collection2 = Mage::getResourceModel('reports/product_collection')
                            ->addOrderedQty();


            foreach($collection2 as $item) {
                if($i = $collection->getItemById($item->getId())) {
                    $i->setData("ordered_qty", (int) $item->getOrderedQty());
                }
            }

            foreach($collection as $item) {
                if(!$item->getOrderedQty()) {
                    $item->setOrderedQty(0);
                }
            }

            $collection->getSelect()->order('ordered_qty');

            return $collection;
        }
        return $this->_productCollection;
    }


}


//            /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
//            $collection = Mage::getModel('catalog/product')->getCollection();
//            $collection->joinTable('sales_flat_order_item', 'product_id = entity_id', 'product_id', null, 'LEFT');
//            $collection->getSelect()->columns('COUNT(product_id) AS ordered_qty');
//            $collection->getSelect()->group('sales_flat_order_item.product_id');
//            echo $collection->getSelect()->assemble();