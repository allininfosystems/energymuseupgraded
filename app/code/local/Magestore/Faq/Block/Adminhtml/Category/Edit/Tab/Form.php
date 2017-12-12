<?php

class Magestore_Faq_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  
  public function getCategory()
  {
	return Mage::registry('faqcategory_data');
  }
  
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
	   if ( Mage::getSingleton('adminhtml/session')->getFaqcategoryData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getFaqcategoryData();
          Mage::getSingleton('adminhtml/session')->setFaqcategoryData(null);
      } elseif ( Mage::registry('faqcategory_data') ) {
	      $data = Mage::registry('faqcategory_data')->getData() ;
      }
      $fieldset = $form->addFieldset('category_form', array('legend'=>Mage::helper('faq')->__('Category Information')));
        if(!isset($data['name_in_store'])) $data['name_in_store'] = '';
        if(!isset($data['ordering_in_store'])) $data['ordering_in_store'] = '';
        if(!isset($data['status_in_store'])) $data['status_in_store'] = '';	  
       $store_id = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('faq')->__('Use Default');
        $defaultTitle = Mage::helper('faq')->__('-- Please Select --');
        $scopeLabel = Mage::helper('faq')->__('STORE VIEW');
        $globalLabel = Mage::helper('faq')->__('GLOBAL');
	  
	  
	  $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('faq')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
		  
		  'disabled' => ($store_id && !$data['name_in_store']),
          'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="name_default" name="name_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['name_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="name_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label"><span class="nobr">['.$scopeLabel.']</span>
			</td>': '<td class="scope-label">
			['.$scopeLabel.']</td>',
      )); 
	  $fieldset->addField('ordering', 'text', array(
          'label'     => Mage::helper('faq')->__('Sort Order'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ordering',
		  'disabled' => ($store_id && !$data['ordering_in_store']),
          'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="ordering_default" name="ordering_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['ordering_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="ordering_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label"><span class="nobr">['.$scopeLabel.']</span>
			</td>': '<td class="scope-label">
			['.$scopeLabel.']</td>',
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('faq')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('faq')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('faq')->__('Disabled'),
              ),
          ),
		  'disabled' => ($store_id && !$data['status_in_store']),
          'after_element_html' => $store_id ? '
			<td class="value use-default">
			<input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" '.($data['status_in_store'] ? '' : 'checked="checked"').' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="status_default" class="inherit" title="'.$defaultTitle.'">'.$defaultLabel.'</label>
          </td><td class="scope-label"><span class="nobr">['.$scopeLabel.']</span>
			</td>': '<td class="scope-label">
			['.$scopeLabel.']</td>',
      ));	
      $form->setValues($data);
      return parent::_prepareForm();
  }
}