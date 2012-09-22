<?php

class Meanbee_Mosaic_Block_Product_Mosaic extends Mage_Catalog_Block_Product_Abstract {

    protected $_productsCount = null;

    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    const DEFAULT_PRODUCTS_COUNT = '';

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $collection = Mage::getResourceModel('catalog/product_collection\');
        Mage::getSingleton(\'catalog/product_status\')->addVisibleFilterToCollection($collection);
        Mage::getSingleton(\'catalog/product_visibility\')->addVisibleInCatalogFilterToCollection($collection);

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter(\'special_price\', array(\'gt\' => 0), \'left\')
            ->addAttributeToFilter(\'special_from_date\', array(\'date\' => true, \'to\' => $todayDate))
            ->addAttributeToFilter(\'special_to_date\', array(\'or\'=> array(
                0 => array(\'date\' => true, \'from\' => $todayDate),
                1 => array(\'is\' => new Zend_Db_Expr(\'null\')))
            ), \'left\')
            ->addAttributeToSort(\'special_from_date\', \'desc\')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        $this->setProductCollection($collection);

         if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to tollbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild(\'toolbar\', $toolbar);
        Mage::dispatchEvent(\'catalog_block_product_list_collection\', array(
            \'collection\'=>$collection,
        ));

        return parent::_beforeToHtml();
    }

    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }


     public function getMode()
    {
        return $this->getChild(\'toolbar\')->getCurrentMode();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml(\'toolbar\');
    }
}