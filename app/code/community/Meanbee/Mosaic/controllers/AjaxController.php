<?php
class Meanbee_Mosaic_AjaxController extends Mage_Core_Controller_Front_Action {

    public function categoryAction() {
        $return = array(
            'status'  => 'error',
            'content' => 'An unknown error occurred'
        );

        $category_id = 3;//$this->getRequest()->getParam('id', false);

        if ($category_id !== false && is_numeric($category_id)) {
            Mage::register('current_category', Mage::getModel('catalog/category')->load($category_id));
            $return = $this->_getReturnContent();
        } else {
            $return['content'] = 'No category provided';
        }

        $this->_setCookie('category', $this->getRequest()->getParam('p', 1));

        $this->getResponse()->setHeader('Content-Type', 'application/json', true)->setBody(Mage::helper('core')->jsonEncode($return));
    }

    protected function _getReturnContent() {
        $this->getLayout()->getUpdate()->addHandle("default");
        $this->getLayout()->getUpdate()->addHandle("meanbee_mosaic_index_view");
        $this->getLayout()->getUpdate()->addHandle("page_empty");
        $this->loadLayout();


        $content_block = $this->_getProductListBlock();

        if ($content_block !== false) {
            $return['status']  = 'success';
            $return['content'] = array(
                "block" => $content_block->toHtml()
            );
        } else {
            $return['status']  = 'error';
            $return['content'] = 'Unable to load appropriate block';
        }

        return $return;
    }

    protected function _getProductListBlock() {
        return $this->getLayout()->getBlock('mosaic.topnav');
    }

    protected function _setCookie($key, $value) {
        /** @var $cookie Mage_Core_Model_Cookie */
        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set(
            'derp',
            $value, null, null, null, null, false
        );
    }



}
