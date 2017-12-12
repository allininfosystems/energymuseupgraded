<?php

class Magestore_Faq_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('faq/category')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Categories Manager'), Mage::helper('adminhtml')->__('Category Manager'));

        return $this;
    }

    public function indexAction() {

        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }

        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {

        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }

        $id = $this->getRequest()->getParam('id');
        $store_id = $this->getRequest()->getParam('store');
        $stores = Mage::app()->getStores(true);
        if (count($stores) == 1) {
            foreach ($stores as $store) {
                $store_id = $store->getStoreId();
            }
        }


        $model = Mage::getModel('faq/category')->setStoreId($store_id)->load($id);
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        Mage::register('faqcategory_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('faq/category');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category News'), Mage::helper('adminhtml')->__('Category News'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('faq/adminhtml_category_edit'))
                ->_addLeft($this->getLayout()->createBlock('faq/adminhtml_category_edit_tabs'));

        $this->renderLayout();
    }

    public function newAction() {
        if (!Mage::getModel('faq/category')->getCollection()->getSize()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('faq')->__('Please add category first before adding FAQ'));
        }
        $this->editAction();
    }

    public function saveAction() {

        $store_id = $this->getRequest()->getParam('store');
        $id = $this->getRequest()->getParam('id');

        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['url_key']) && $data['url_key']) {
                $data['url_key'] = Mage::helper('faq')->normalizeUrlKey($data['url_key']);
            } elseif (isset($data['name'])) {
                $data['url_key'] = Mage::helper('faq')->normalizeUrlKey($data['name']);
            }
            try {

                if (!$store_id) {
                    $model = Mage::getModel('faq/category');
                    $model->setData($data)
                            ->setId($this->getRequest()->getParam('id'));
                    $model->save();
                    $id = $model->getCategoryId();
                    $stores = Mage::app()->getStores(true);
                } else {
                    foreach ($data as $label => $value) {
                        $categoryValue = Mage::getModel('faq/categoryvalue');
                        $attribute_code = explode('_', $label);
                        if (count($attribute_code) == 1) {
                            $categoryStoreValue = Mage::getModel("faq/categoryvalue")->loadByCatIdStore($id, $store_id, $label);
                            if (!$categoryStoreValue->getId()) {
                                $categoryValue->setData(array('category_id' => $id, 'store_id' => $store_id, 'attribute_code' => $label, 'value' => $data[$label]));
                                $categoryValue->save();
                            } else {
                                $order = $data[$label];
                                $order = trim($order);
                                if ($label == 'ordering') {
                                    if (is_numeric($order)) {
                                        $data[$label] = $order;
                                    } else {
                                        $data[$label] = 0;
                                    }
                                }
                                $categoryStoreValue->addData(array('value' => $data[$label]));
                                $categoryStoreValue->save();
                            }
                        } else {
                            $categoryStoreValue = Mage::getModel("faq/categoryvalue")->loadByCatIdStore($id, $store_id, $attribute_code[0]);
                            $categoryStoreValue->delete();
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $id, 'store' => $store_id));
                    return;
                }
                $this->_redirect('*/*/', array('id' => $id, 'store' => $store_id));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id, 'store' => $store_id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find item to save'));
        $this->_redirect('*/*/', array('id' => $id, 'store' => $store_id));
    }

    //King270612
    public function deleteAction() {
        $categoryId = $this->getRequest()->getParam('id');
        if ($categoryId > 0) {
            try {
                $model = Mage::getModel('faq/category')->load($categoryId);
                $questions = Mage::getModel("faq/faq")->getCollection()
                        ->addFieldToFilter('category_id', $categoryId);
                if (!$questions->getsize()) {
                    $model->delete();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Cannot delete Category that contained FAQs'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Cannot delete Category that contained FAQs'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    //King270612
    public function massDeleteAction() {
        $categoryIds = $this->getRequest()->getParam('category');
        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $count = 0;
            try {
                foreach ($categoryIds as $categoryId) {
                    $model = Mage::getModel('faq/category')->load($categoryId);
                    $questions = Mage::getModel("faq/faq")->getCollection()
                            ->addFieldToFilter('category_id', $categoryId);
                    if (!$questions->getsize()) {
                        $model->delete();
                        $count++;
                    } else {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Cannot delete Category "%s" that contained FAQs', $model->getName()));
                    }
                }
                //Duy Tuan 30/06/2015
                if ($count) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__(
                                    'Total of %d record(s) were successfully deleted', $count
                            )
                    );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Cannot delete Category that contained FAQs'));
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $faqIds = $this->getRequest()->getParam('category');
        $store_id = $this->getRequest()->getParam('store');
        $status = $this->getRequest()->getParam('status');
        if($status==2) $status=0;
        if (!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    if (!$store_id) {
                        $faq = Mage::getSingleton('faq/category')
                                ->load($faqId)
                                ->setStatus($status)
                                ->setIsMassupdate(true)
                                ->save();
                    } else {
                        $categoryValue = Mage::getModel('faq/categoryvalue');
                        $categoryValue_new = Mage::getModel("faq/categoryvalue")->loadByCatIdStore($faqId, $store_id, 'status');
                        if (!$categoryValue_new->getId()) {
                            $categoryValue->setData(array('category_id' => $faqId, 'store_id' => $store_id, 'attribute_code' => 'status', 'value' => $status));
                            $categoryValue->save();
                        } else {
                            $categoryValue_new->addData(array('value' => $status));
                            $categoryValue_new->save();
                        }
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($faqIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $store_id));
    }

    public function exportCsvAction() {
        $fileName = 'faqcategory.csv';
        $content = $this->getLayout()->createBlock('faq/adminhtml_category_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'faqcategory.xml';
        $content = $this->getLayout()->createBlock('faq/adminhtml_category_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('faq/category');
    }
}
