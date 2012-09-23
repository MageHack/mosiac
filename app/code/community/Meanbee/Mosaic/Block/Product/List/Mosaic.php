<?php

class Meanbee_Mosaic_Block_Product_List_Mosaic extends Mage_Catalog_Block_Product_List {

    protected $_bestselling_groups = 3;
    const PAGE_SIZE = 30;

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {

            $visibility = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            );
            $page = $this->getRequest()->getParam('p', 1);
            $collection = parent::_getProductCollection()
                            ->addAttributeToSelect(array('image', 'short_description', 'name', 'price'))
                            ->addAttributeToFilter('visibility', $visibility)->setCurPage($page)->setPageSize(self::PAGE_SIZE);

            $collection2 = Mage::getResourceModel('reports/product_collection')
                            ->addOrderedQty();

            $qtys = array();

            foreach($collection as $item) {
                if ($i = $collection2->getItemById($item->getId())) {
                    echo $i->getOrderedQty();
                    $item->setData("ordered_qty", (int) $i->getOrderedQty());
                } else {
                    $item->setData("ordered_qty", 0);
                }

                array_push($qtys, $item->getOrderedQty());
            }

            sort($qtys);
            $total = count($qtys);
            $group_size = (int) floor($total / $this->_bestselling_groups);
            $first_boundary = $qtys[$group_size];
            $second_boundary = $qtys[$group_size * 2];

            foreach ($collection as $item) {
                if ($item->getOrderedQty() <= $first_boundary) {
                    $item->setBestsellerGroup(1);
                } elseif ($item->getOrderedQty() <= $second_boundary) {
                    $item->setBestsellerGroup(2);
                } else {
                    $item->setBestsellerGroup(3);
                }
            }

            Mage::getModel('catalog/layer')->prepareProductCollection($collection);

            return $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }


}