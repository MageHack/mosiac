<?php

class Meanbee_Mosaic_Block_Product_Mosaic extends Mage_Catalog_Block_Product_List {

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);


            // your custom filter

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}