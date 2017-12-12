<?php

class FME_Events_Block_Adminhtml_Events_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

// implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _prepareForm() 
    {

        $model = Mage::registry('events_data'); //echo '<pre>';print_r($model->getData());echo '</pre>';exit;

        $form = new Varien_Data_Form();
        $_helper = Mage::helper('events');
//        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'events_form', array(
            'legend' => Mage::helper('events')->__('Event information')
            )
        );

        $fieldset->addField(
            'event_title', 'text', array(
            'label' => Mage::helper('events')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'event_title'
            )
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField(
            'event_start_date', 'date', array(
            'name' => 'event_start_date',
            'label' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            // 'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
            'style' => 'width:161px',
            'time' => true,
            'class' => 'validate-date validate-date-range date-range-event_date-from'
            )
        );

        $fieldset->addField(
            'event_end_date', 'date', array(
            'name' => 'event_end_date',
            'label' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            // 'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format' => $dateFormatIso,
            'time' => true,
            'style' => 'width:161px',
            'required' => true,
            'class' => 'validate-date validate-date-range date-range-event_date-to'
            )
        );

        $isRecurr = $fieldset->addField(
            'is_recurring', 'select', array(
            'label' => Mage::helper('events')->__('Recurring'),
            'name' => 'is_recurring',
            'values' => Mage::getModel('events/status')->getOptionArray(),
            'note' => $_helper->__('Repeat event.'),
            'onchange' => "if ($('chkboxes_recurring') && this.value == 2) {"
                . "$('chkboxes_recurring').hide();"
            . "} else if ($('recurring_by').value == 'Weekly') {"
                . "$('chkboxes_recurring').show();"
            . "};"
            )
        );

        $type = $fieldset->addField(
            'recurring_by', 'select', array(
            'label' => Mage::helper('events')->__('Repeat'),
            'name' => 'recurring_by',
            'values' => Mage::getModel('events/repeats')->getOptionArray(),
            'onchange' => "showWeekDays(this);",
                //'readonly' => true,
            )
        );
        // version 0.1.2
        $type->setAfterElementHtml(
            "<script type='text/javascript'> "
            . "function showWeekDays(e) { "
                . "if (e.value == 'Weekly') { "
                    
                    . "var weekdays = new Array(7);
                        weekdays[0]=  'Sunday';
                        weekdays[1] = 'Monday';
                        weekdays[2] = 'Tuesday';
                        weekdays[3] = 'Wednesday';
                        weekdays[4] = 'Thursday';
                        weekdays[5] = 'Friday';
                        weekdays[6] = 'Saturday';"
                    . "$('chkboxes_recurring').show();"
                    . "var startdate = new Date($('event_start_date').value); "
                    . "var weekday = weekdays[startdate.getDay()];"
                    . "$('recurring_on_' + weekday.toLowerCase()).disable();"
                . "} else { "
                    . "$('chkboxes_recurring').hide();"
                . "}"
            . "}"
            . "</script>"
        );
        
        $on = $fieldset->addField(
            'recurring_on', 'checkboxes', array(
            'label' => Mage::helper('events')->__('Repeat On'),
            'name' => 'recurring_on[]',
            'values' => Mage::getModel('events/repeats')->getWeekDaysArray(),
                //'readonly' => true,
            'container_id' => 'chkboxes_recurring',
            'after_element_html' => "<script type='text/javascript'>if ($('recurring_by').value != 'Weekly') { $('chkboxes_recurring').hide(); }</script>",
            'note' => $this->__('Weekday on whcih event actually started will always be selected!'),
            )
        );
        // version 0.1.2 ends
        $intervals = $fieldset->addField(
            'recurring_intervals', 'select', array(
            'label' => Mage::helper('events')->__('Repeat Every'),
            'name' => 'recurring_intervals',
            'class' => 'validate-digits required-entry validate-greater-than-zero ',
            'values' => array_slice(range(0, 30), 1, null, true),
            'required' => true,
            'note' => $_helper->__('Repeats every (n) weeks, days, months or years'),
            'style' => 'width:50px;'
            )
        );

        $occurrences = $fieldset->addField(
            'recurring_occurrences', 'text', array(
            'label' => Mage::helper('events')->__('Occurrences'),
            'name' => 'recurring_occurrences',
            'class' => 'validate-digits validate-greater-than-zero ',
            'note' => $_helper->__('Ends after (n) occurrences')
            )
        );

        $fieldset->addField(
            'event_venu', 'text', array(
            'label' => Mage::helper('events')->__('Venue'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'event_venu',
            'note' => $_helper->__('The input will be used for goolge map.')
            )
        );

        $fieldset->addField(
            'event_url_prefix', 'text', array(
            'label' => Mage::helper('events')->__('Url Prefix'),
            'class' => '',
            'name' => 'event_url_prefix'
            )
        );

        $fieldset->addField(
            'event_image', 'image', array(
            'label' => Mage::helper('events')->__('Image'),
            'required' => false,
            'name' => 'event_image',
            'note' => Mage::helper('events')->__('upload image file only')
            )
        );

        $fieldset->addField(
            'event_thumb_image', 'hidden', array(
            'name' => 'event_thumb_image',
            )
        );

        $fieldset->addField(
            'event_video', 'text', array(
            'label' => Mage::helper('events')->__('Youtube Video Url'),
            'required' => false,
            'name' => 'event_video',
            // 'class' => 'validate-url',
            'note' => $_helper->__("Paste in video url from Youtube.")
            )
        );

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('events')->__('Store View(s)'),
                'title' => Mage::helper('events')->__('Store View(s)'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
                )
            );
            $renderer = $this->getLayout()
                    ->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
                )
            );
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
            'tab_id' => 'form_section'
            )
        );

        $wysiwygConfig ["files_[]browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
        $wysiwygConfig ["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig ["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig ["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
        $wysiwygConfig ["files_browser_window_width"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width');
        $wysiwygConfig ["files_browser_window_height"] = (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height');
        $plugins = $wysiwygConfig->getData("plugins");
        $plugins [0] ["options"] ["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
        $plugins [0] ["options"] ["onclick"] ["subject"] = "MagentovariablePlugin.loadChooser('" . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin') . "', '{{html_id}}');";
        $plugins = $wysiwygConfig->setData("plugins", $plugins);

        $fieldset->addField(
            'event_content', 'editor', array(
            'name' => 'event_content',
            'label' => Mage::helper('events')->__('Content'),
            'title' => Mage::helper('events')->__('Content'),
            'style' => 'width:600px; height:500px;',
            'config' => $wysiwygConfig,
            'required' => true
            )
        );

        $fieldset->addField(
            'event_status', 'select', array(
            'label' => Mage::helper('events')->__('Status'),
            'name' => 'event_status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('events')->__('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('events')->__('Disabled')
                )
            )
            )
        );

        $this->setChild(
            'form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap($isRecurr->getHtmlId(), $isRecurr->getName())
                        ->addFieldMap($intervals->getHtmlId(), $intervals->getName())
                        ->addFieldMap($occurrences->getHtmlId(), $occurrences->getName())
                        ->addFieldMap($type->getHtmlId(), $type->getName())
                        //->addFieldMap($on->getContainerId(), $on->getName())

                        ->addFieldDependence(
                            $intervals->getName(), $isRecurr->getName(), 1
                        )
                        ->addFieldDependence(
                            $occurrences->getName(), $isRecurr->getName(), 1
                        )
                        ->addFieldDependence(
                            $type->getName(), $isRecurr->getName(), 1
                        )
            //                        ->addFieldDependence(
            //                                $on->getName(), $isRecurr->getName(), 1
            //                        )
        );

        //$model->setRecurringBy('Weekly');
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
