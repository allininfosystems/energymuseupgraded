<?php

class Magestore_Faq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('faq/faq')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('FAQ Manager'), Mage::helper('adminhtml')->__('FAQ Manager'));

        return $this;
    }

    public function indexAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        if (!Mage::getModel('faq/category')->getCollection()->getSize()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('faq')->__('Please add category first before adding FAQ'));
        }
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)) {
            return;
        }
        $store_id = $this->getRequest()->getParam('store');
        $stores = Mage::app()->getStores(true);
        if (count($stores) == 1) {
            foreach ($stores as $store) {
                $store_id = $store->getStoreId();
            }
        }



        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('faq/faq')
                ->setStoreId($store_id)
                ->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('faq_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('faq/faq');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('FAQ Manager'), Mage::helper('adminhtml')->__('FAQ Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('FAQ News'), Mage::helper('adminhtml')->__('FAQ News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            // enable wysiwyg
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
            $this->_addContent($this->getLayout()->createBlock('faq/adminhtml_faq_edit'))
                    ->_addLeft($this->getLayout()->createBlock('faq/adminhtml_faq_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        if (!Mage::getModel('faq/category')->getCollection()->getSize()) {
            $this->_redirect('*/category/new');
        }
        $this->editAction();
    }

    public function saveAction() {
        $store_id = $this->getRequest()->getParam('store');
        $id = $this->getRequest()->getParam('id');

        if ($data = $this->getRequest()->getPost()) {

            if (isset($data['url_key']) && $data['url_key']) {
                $data['url_key'] = Mage::helper('faq')->normalizeUrlKey($data['url_key']);
            } elseif (isset($data['title'])) {
                $data['url_key'] = Mage::helper('faq')->normalizeUrlKey($data['title']);
            }
            try {
                $model = Mage::getModel('faq/faq');
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                if (!$store_id) {
                    $str = Mage::helper('faq')->parseTags($data['tag']);
                    $data['tag'] = $str;
                    $model->setData($data)
                            ->setId($this->getRequest()->getParam('id'));
                    $model->save();
                    $id = $model->getId();
                    //save URL KEY
                    $stores = Mage::app()->getStores(true);
                    foreach ($stores as $store) {
                        $model->setStoreId($store->getStoreId())
                                ->updateUrlKey();			
                    }
                } else {
                    foreach ($data as $label => $value) {
                        $faqValue = Mage::getModel('faq/faqvalue');
                        $attribute_code = explode('_', $label);
                        if (count($attribute_code) == 1) {
                            $faqStoreValue = Mage::getModel("faq/faqvalue")->loadByFaqIdStore($id, $store_id, $label);
                            if (!$faqStoreValue->getId()) {
                                $faqValue->setData(array('faq_id' => $id, 'store_id' => $store_id, 'attribute_code' => $label, 'value' => $data[$label]));
                                if (!strcmp($faqValue->getAttributeCode(), 'tag'))
                                    $faqValue->setData('value', Mage::helper('faq')->parseTags($faqValue->getValue()));
                                $faqValue->save();
                            }else {
                                $order = $data[$label];
                                $order = trim($order);
                                if ($label == 'ordering') {
                                    if (is_numeric($order)) {
                                        $data[$label] = $order;
                                    } else {
                                        $data[$label] = 0;
                                    }
                                }
                                $faqStoreValue->addData(array('value' => $data[$label]));
                                if (!strcmp($faqStoreValue->getAttributeCode(), 'tag'))
                                    $faqStoreValue->setData('value', Mage::helper('faq')->parseTags($faqStoreValue->getValue()));
                                $faqStoreValue->save();
                            }
                        }else {
                            $faqStoreValue = Mage::getModel("faq/faqvalue")->loadByFaqIdStore($id, $store_id, $attribute_code[0]);
                            $faqStoreValue->delete();
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
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $store_id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find item to save'));
        $this->_redirect('*/*/', array('id' => $id, 'store' => $store_id));
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('faq/faq');

                $model->load($this->getRequest()->getParam('id'));
                $model->deleteAllUrlKey();
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $faqIds = $this->getRequest()->getParam('faq');
        if (!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = Mage::getModel('faq/faq')->load($faqId);
                    $faq->deleteAllUrlKey();
                    $faq->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($faqIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $faqIds = $this->getRequest()->getParam('faq');
        $store_id = $this->getRequest()->getParam('store');
        $status = $this->getRequest()->getParam('status');
        if($status==2) $status=0;
        if (!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    if (!$store_id) {
                        $faq = Mage::getSingleton('faq/faq')
                                ->load($faqId)
                                ->setStatus($status)
                                ->setIsMassupdate(true)
                                ->save();
                    } else {
                        $faqValue = Mage::getModel('faq/faqvalue');
                        $faqValue_new = Mage::getModel("faq/faqvalue")->loadByFaqIdStore($faqId, $store_id, 'status');
                        if (!$faqValue_new->getId()) {
                            $faqValue->setData(array('faq_id' => $faqId, 'store_id' => $store_id, 'attribute_code' => 'status', 'value' => $status));
                            $faqValue->save();
                        } else {
                            $faqValue_new->addData(array('value' => $status));
                            $faqValue_new->save();
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
        $fileName = 'faq.csv';
        $content = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'faq.xml';
        $content = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('faq/faq');
    }
}
