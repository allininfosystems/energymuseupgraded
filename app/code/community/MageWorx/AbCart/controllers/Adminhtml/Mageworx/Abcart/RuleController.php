<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Adminhtml_Mageworx_Abcart_RuleController extends  Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/mageworx_abcart/rules');

        $this->_title($this->__('Sales'))
            ->_title($this->__('Abandoned Cart Recovery'))
            ->_title($this->__('Rules'));

        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mageworx_abcart/alert_rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('This rule no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_alert_rule', $model);

        $block = $this->getLayout()->createBlock('mageworx_abcart/adminhtml_alert_rule_edit')
            ->setData('action', $this->getUrl('*/*/save'));

        $name = $model->getName()?$model->getName():$this->__('New Rule');
        $this->_title($this->__('Credit'))->_title($this->__('Manage Rules'))->_title($name);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('mageworx_abcart/adminhtml_alert_rule_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('mageworx_abcart/alert_rule');

                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('salesrule')->__('Wrong rule specified.'));
                    }
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }

                unset($data['rule']);
                $model->loadPost($data);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $ruleIds = $this->getRequest()->getParam('rule_ids');
        if (!is_array($ruleIds)) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('Please select product(s).'));
        } else {
            if (!empty($ruleIds)) {
                try {
                    foreach ($ruleIds as $id) {
                        $rule = Mage::getSingleton('mageworx_abcart/alert_rule')->load($id);
                        $rule->delete();
                    }
                    $this->_getSession()->addSuccess(
                        Mage::helper('catalog')->__('Total of %d record(s) have been deleted.', count($ruleIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ruleIds = $this->getRequest()->getParam('rule_ids');
        $status     = (int)$this->getRequest()->getParam('status');

        if (!is_array($ruleIds)) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('Please select product(s).'));
        } else {
            if (!empty($ruleIds)) {
                try {
                    foreach ($ruleIds as $id) {
                        $rule = Mage::getSingleton('mageworx_abcart/alert_rule')->load($id);
                        $rule->setStatus($status)->save();
                    }
                    $this->_getSession()->addSuccess(
                        Mage::helper('catalog')->__('Total of %d record(s) have been updated.', count($ruleIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/mageworx_abcart/rules');
    }
}