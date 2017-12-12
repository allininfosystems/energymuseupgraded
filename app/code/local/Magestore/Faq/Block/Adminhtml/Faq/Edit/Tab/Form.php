<?php

class Magestore_Faq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

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
        if(!isset($data['category_id'])) $data['category_id'] = '';
        if(!isset($data['title_in_store'])) $data['title_in_store'] = '';
        if(!isset($data['description_in_store'])) $data['description_in_store'] = '';
        if(!isset($data['tag_in_store'])) $data['tag_in_store'] = '';
        if(!isset($data['ordering_in_store'])) $data['ordering_in_store'] = '';
        if(!isset($data['status_in_store'])) $data['status_in_store'] = '';
        $fieldset = $form->addFieldset('faq_form', array('legend' => Mage::helper('faq')->__('Item Information')));

        $store_id = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('faq')->__('Use Default');
        $defaultTitle = Mage::helper('faq')->__('-- Please Select --');
        $scopeLabel = Mage::helper('faq')->__('STORE VIEW');
        $globalLabel = Mage::helper('faq')->__('GLOBAL');
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('faq')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
            'disabled' => ($store_id && !$data['title_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="title_default" name="title_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['title_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="title_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));
        $config = array(
            'use_container' => true,
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')
        );

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('faq')->__('Description'),
            'title' => Mage::helper('faq')->__('Description'),
            'style' => 'width:500px; height:300px;',
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig($config),
            'wysiwyg' => true,
            'required' => true,
            'disabled' => ($store_id && !$data['description_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="description_default" name="description_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['description_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="description_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));

        $boxTags = Mage::helper('faq')->getHtmlTags($this->getRequest()->getParam('store'));
        $fieldset->addField('tag', 'text', array(
            'label' => Mage::helper('faq')->__('Tags'),
            'required' => false,
            'onclick' => "javascript:openBox()",
            'name' => 'tag',
            'disabled' => ($store_id && !$data['tag_in_store']),
            'after_element_html' => $store_id ? '
			' . $boxTags . '<p class="note"style="float:left;"><span>Separate tags with commas</span></p><td class="value use-default">
			<input id="tag_default" name="tag_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['tag_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="tag_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '</br>' . $boxTags . '<p class="note"style="float:left;"><span>Separate tags with commas</span></p><td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));   
        $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('faq')->__('Category'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'category_id',
            'values' => Mage::helper('faq')->getCategoryOptions2($data['category_id'], $store_id),
            'disabled' => $store_id ? true : false,
            'after_element_html' => $store_id ? '
			<td class="value use-default"></td><td class="scope-label"><span class="nobr">[' . $globalLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $globalLabel . ']</td>',
        ));

        $fieldset->addField('url_key', 'text', array(
            'label' => Mage::helper('faq')->__('Url Key'),
            'required' => false,
            'name' => 'url_key',
            'disabled' => $store_id ? true : false,
            'after_element_html' => $store_id ? '
			<td class="value use-default"></td><td class="scope-label"><span class="nobr">[' . $globalLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $globalLabel . ']</td>',
        ));




        $fieldset->addField('most_frequently', 'select', array(
            'label' => Mage::helper('faq')->__('Most Frequently Asked Question'),
            'name' => 'most_frequently',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('faq')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('faq')->__('Yes'),
                ),
            ),
            'disabled' => $store_id ? true : false,
            'after_element_html' => $store_id ? '
			<td class="value use-default"></td><td class="scope-label"><span class="nobr">[' . $globalLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $globalLabel . ']</td>',
        ));
        $fieldset->addField('ordering', 'text', array(
            'label' => Mage::helper('faq')->__('Sort Order'),
            'required' => false,
            'name' => 'ordering',
            'disabled' => ($store_id && !$data['ordering_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="ordering_default" name="ordering_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['ordering_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="ordering_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));


        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('faq')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('faq')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('faq')->__('Disabled'),
                ),
            ),
            'disabled' => ($store_id && !$data['status_in_store']),
            'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['status_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
          </td><td class="scope-label"><span class="nobr">[' . $scopeLabel . ']</span>
			</td>' : '<td class="scope-label">
			[' . $scopeLabel . ']</td>',
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
