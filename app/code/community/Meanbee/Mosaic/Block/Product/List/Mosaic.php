<?php
/*
 * This class  prepares a product collection and adds sales data to it.
 *
 * @TODO Need to improve the way the collection is loaded.  Struggled to load all products and left join with sales_flat_order_items
 */
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

            // Get the original product colleciton but ensure we have the fields we want
            $collection = parent::_getProductCollection()
                            ->addAttributeToSelect(array('image', 'short_description', 'name', 'price'))
                            ->addAttributeToFilter('visibility', $visibility)->setCurPage($page)->setPageSize(self::PAGE_SIZE)->load();

            // Get ordered qty information from sales reports.
            $collection2 = Mage::getResourceModel('reports/product_collection')
                            ->addOrderedQty();

            $qtys = array();
            // Merge reporting data into the original product collection
            foreach($collection as $item) {
                if ($i = $collection2->getItemById($item->getId())) {
                    $item->setData("ordered_qty", (int) $i->getOrderedQty());
                } else {
                    $item->setData("ordered_qty", 0);
                }

                array_push($qtys, $item->getOrderedQty());
            }

            // The mosaic ranks products on best, moderate, and poor selling
            // Using the ordered qty, assign groups to products
            // @TODO Needs work to do this nicer nad more efficiently.

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

            // Ensure we're ready for layered nav.
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);

            return $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }


}