<?php

class FME_Events_Adminhtml_Fme_EventsController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() 
    {
        $this->loadLayout()->_setActiveMenu('events/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Events Manager'), Mage::helper('adminhtml')->__('Events Manager'));

        return $this;
    }

    public function indexAction() 
    {
        $this->_initAction()->renderLayout();
    }

    protected function _initEventsProducts() 
    {
        $events = Mage::getModel('events/events');
        $eventsId = (int) $this->getRequest()->getParam('id');
        if ($eventsId) {
            $events->load($eventsId);
        }

        Mage::register('current_events_products', $events);

        return $events;
    }

    public function productsAction() 
    {
        $this->_initEventsProducts();
        $this->loadLayout();
        $this->getLayout()
                ->getBlock('events.edit.tab.products')
                ->setEventsProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    public function editAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('events/events')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            //echo '<pre>';print_r($data); echo '</pre>';
            
            if (isset($data['images'])) {
                $imagesAll = json_decode($data['images'], true);
                $arr = array();
                foreach ($imagesAll as $i) {
                    $arr[] = array(
                        'image_id' => $i['value_id'],
                        'image_file' => $i['file'],
                        'image_name' => $i['label'],
                        'image_order' => $i['position'],
                        'image_status' => $i['disabled']
                    );
                }
                
                $data['images_all'] = $arr;
            }

            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('events_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('events/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('events/adminhtml_events_edit'))->_addLeft($this->getLayout()->createBlock('events/adminhtml_events_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('events')->__('Event does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() 
    {
        $this->_forward('edit');
    }

    public function saveAction() 
    {

        if ($data = $this->getRequest()->getPost()) {  
        //echo '<pre>';print_r($data);exit;
            // uploading/handling file is a complicated task
            if (isset($_FILES ['event_image'] ['name']) && $_FILES ['event_image'] ['name'] != '') {
                $path = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . 'events';
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('event_image');

                    // Any extention would work
                    $uploader->setAllowedExtensions(
                        array(
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                        )
                    );
                    $uploader->setAllowRenameFiles(false);
                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    // (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    // We set media as the upload dir

                    $file = $uploader->save($path, $_FILES['event_image']['name']);

                    $data['event_image'] = Mage::getResourceModel('events/events')->resizeImage($file);
                    $data['event_thumb_image'] = Mage::getResourceModel('events/events')->resizeImage($file, true);
                } catch (Exception $ex) {
                    Mage::log($ex->getMessage());
                }
            } else if (isset($data['event_image']['delete']) && $data['event_image']['delete'] == 1) {
                $data['event_image'] = '';
            } else {
                if (isset($data['event_image'])) {
                    $data['event_image'] = $data['event_image']['value'];
                }
            }

            // checking idetifier key
            if(!isset($data['event_url_prefix']) ||  !$data['event_url_prefix'])   {
                $data['event_url_prefix'] = $data['event_title'];
            }

            $data['event_url_prefix'] = Mage::helper('events')->nameToUrlKey($data['event_url_prefix']); 


            $model = Mage::getModel('events/events');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('events')->__('Event was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit', array(
                        'id' => $model->getId()
                        )
                    );
                    return;
                }

                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                 
                $this->_redirect(
                    '*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                    )
                );
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('events')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() 
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                Mage::helper('events')->removeGalleryByEventId($this->getRequest()->getParam('id'));
                $model = Mage::getModel('events/events');
                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect(
                    '*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                    )
                );
            }
        }

        $this->_redirect('*/*/');
    }

    public function massDeleteAction() 
    {
        $eventsIds = $this->getRequest()->getParam('events');
        if (!is_array($eventsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($eventsIds as $eventsId) {
                    Mage::helper('events')->removeGalleryByEventId($eventsId);
                    $events = Mage::getModel('events/events')->load($eventsId);
                    $events->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($eventsIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massStatusAction() 
    {
        $eventsIds = $this->getRequest()->getParam('events');
        if (!is_array($eventsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($eventsIds as $eventsId) {
                    $events = Mage::getSingleton('events/events')->load($eventsId)->setEventStatus($this->getRequest()->getParam('event_status'))->setIsMassupdate(true)->save();
                }

                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($eventsIds)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() 
    {
        $fileName = 'events.csv';
        $content = $this->getLayout()->createBlock('events/adminhtml_events_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() 
    {
        $fileName = 'events.xml';
        $content = $this->getLayout()->createBlock('events/adminhtml_events_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') 
    {
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
        return true;
    }

    public function imageAction() 
    {
        $result = array();
        try {
            $uploader = new FME_Events_Media_Uploader('image');
            $uploader->setAllowedExtensions(
                array(
                'jpg',
                'jpeg',
                'gif',
                'png'
                )
            );
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(Mage::getSingleton('events/config')->getBaseMediaPath());

            $result ['url'] = Mage::getSingleton('events/config')->getMediaUrl($result ['file']);
            $result ['cookie'] = array(
                'name' => session_name(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            );
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Get related products grid
     */
    public function productsGridAction() 
    {
        $this->_initEventsProducts();
        // Push Existing Values in Array
        $productsarray = array();
        $eventsId = (int) $this->getRequest()->getParam('id');
        foreach (Mage::registry('current_events_products')->getEventsRelatedProducts($eventsId) as $products) {
            $productsarray = $products ["product_id"];
        }

        if (!empty($this->getRequest()->getPost('products_related')))
            array_push($this->getRequest()->getPost("products_related"), $productsarray);
        Mage::registry('current_events_products')->setEventsProductsRelated($productsarray);

        $this->loadLayout();
        $this->getLayout()->getBlock('events.edit.tab.products')->setEventsProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    public function gridAction() 
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('events/adminhtml_events_edit_tab_products')->toHtml());
    }

    /**
     * Get specified tab grid
     */
    public function gridOnlyAction() 
    {
        //echo 'Function ===> GridOnlyAction';
        $this->_initProduct();
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/events_edit_tab_products')->toHtml());
    }

    /**
     * Filtering posted data.
     * Converting localized data if needed
     *
     * @param
     *            array
     * @return array
     */
    protected function _filterPostData($data) 
    {
        $data = $this->_filterDateTime(
            $data, array(
            'event_start_date',
            'event_end_date'
            )
        );
        return $data;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
