<?php

class Magestore_Faq_Block_Adminhtml_Faq_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form {

    public function getFaq() {
        return Mage::registry('faq_data');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getFaqData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFaqData();
            Mage::getSingleton('adminhtml/session')->setFaqData(null);
        } elseif (Mage::registry('faq_data')) {
            $data = Mage::registry('faq_data')->getData();
        }
        $fieldset = $form->addFieldset('meta_form', array('legend' => Mage::helper('faq')->__('Meta Information')));
        $store_id = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('faq')->__('Use Default');
        $defaultTitle = Mage::helper('faq')->__('-- Please Select --');
        $scopeLabel = Mage::helper('faq')->__('STORE VIEW');
        $globalLabel = Mage::helper('faq')->__('GLOBAL');

        $fieldset->addField('metakeyword', 'textarea', array(
            'name' => 'metakeyword',
            'label' => Mage::helper('faq')->__('Meta Keywords'),
            'title' => Mage::helper('faq')->__('Meta Keywords'),
            'style' => 'width:500px',
            'required' => false,
            'disabled' => ($store_id && !$data['metakeyword_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="metakeyword_default" name="metakeyword_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['metakeyword_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="metakeyword_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));

        $fieldset->addField('metadescription', 'textarea', array(
            'name' => 'metadescription',
            'label' => Mage::helper('faq')->__('Meta Description'),
            'title' => Mage::helper('faq')->__('Meta Description'),
            'style' => 'width:500px',
            'required' => false,
            'disabled' => ($store_id && !$data['metadescription_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="metadescription_default" name="metadescription_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['metadescription_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="metadescription_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
