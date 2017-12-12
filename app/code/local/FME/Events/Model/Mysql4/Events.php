<?php

class FME_Events_Model_Mysql4_Events extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct() 
    {
        // Note that the event_id refers to the key field in your database table.
        $this->_init('events/events', 'event_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object) 
    {

        //echo '<pre>';print_r($object->getData());exit;
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('event_start_date', 'event_end_date') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        if ($this->_isReserveWord($object)) {
            Mage::throwException(Mage::helper('events')->__('This prefix is not allowed.'));
        }

        if (!$this->getIsUniqueEventToStores($object)) {
            Mage::throwException(Mage::helper('events')->__('Event URL key for specified store already exists.'));
        }

        /*if (!$this->isValidEventIdentifier($object)) {
            Mage::throwException(Mage::helper('events')->__('The event URL key contains capital letters or disallowed symbols.'));
        }*/

        if ($this->isNumericEventIdentifier($object)) {
            Mage::throwException(Mage::helper('events')->__('The event URL key cannot consist only of numbers.'));
        }

        if ($object->getRecurringOn()) {
            $object->setRecurringOn(implode(',', $object->getRecurringOn()));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreatedTime()) {
            $object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    public function loadImage(Mage_Core_Model_Abstract $object) 
    {
        return $this->__loadImage($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object) 
    {
        if (!empty($object ['images'])) {
            $this->__saveEventImages($object);
        }
        $occurence='';
        $kt='';
        $stackStartIntervalDates=0;       
        $stackEndIntervalDates=0;
        $interval=array();
        $condition = $this->_getWriteAdapter()->quoteInto('event_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('events_store'), $condition);

        $conditionProduct = $this->_getWriteAdapter()->quoteInto('eventid = ?', $object->getId());

        foreach ((array) $object->getData('stores') as $store) {
            $storeArray = array();
            $storeArray ['event_id'] = $object->getId();
            $storeArray ['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('events_store'), $storeArray);
        }

        $links = $object ['links']; // echo '<pre>'; print_r($links);exit;
        if (isset($links ['related'])) {
            $this->_getWriteAdapter()->delete($this->getTable('events_product'), $conditionProduct);
            $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput($links ['related']); // echo '<pre>';print_r($productIds);exit;

            foreach ($productIds as $_p) {
                $objArr = array();
                $objArr ['eventid'] = $object->getId();
                $objArr ['product_id'] = $_p;
                $this->_getWriteAdapter()->insert($this->getTable('events_product'), $objArr);
            }
        }

        if ($object->getIsRecurring() == 1) {
            /* calculate end date for recurring event 
             * after adding occurence by recurring interval 
             */
            $condition = $this->_getWriteAdapter()->quoteInto('event_id = ?', $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('events/recurring'), $condition);

            $date = date('Y-m-d', strtotime($object->getEventStartDate()));

            $recurringBy = Mage::helper('events')->recurringBy($object->getRecurringBy(), $object->getRecurringIntervals()); //intervals

            $stackStartIntervalDates = array(); // recurring event start date after intervals
            $stackEndIntervalDates = array(); // recurring event end date after intervals

            $occurence = $object->getRecurringOccurrences() - 1; // event start date is first occurence of the recurring event.

            $date1 = new DateTime($object->getEventStartDate());
            $date2 = new DateTime($object->getEventEndDate());
            $interval = $date1->diff($date2);

            $difference = $interval->format('%R%a days'); // +n days format  echo $difference;exit;

            $dow = date('l', strtotime($object->getEventStartDate()));
            $dowToLower = strtolower($dow);
            $dowN = date('N', strtotime($object->getEventStartDate()));

            $timeStart = date("H:i:s", strtotime($object->getEventStartDate()));
            $timeEnd = date("H:i:s", strtotime($object->getEventEndDate()));

            /**
             * if recurrence type is weekly and weekdays 
             * are given, occurence will be used here
             */
//            if ($object->getRecurringBy() == 'Weekly') {
//
//
//                $recurringOn = explode(',', $object->getRecurringOn());
//                /**
//                 * first iteration must not repeat
//                 */
////                $key = array_search(strtolower($dow), $recurringOn);
////                if ($key !== false) {
////                    unset($recurringOn[$key]);
////                } //echo '<pre>';print_r($recurringOn);exit;
//                //sort($recurringOn); // sort days #numeric
//
//                $count = count($recurringOn) - 1;
//                $i = 1;
//                while ($occurence > 0) {
//
//
//                    $everySourceDate[] = $date;
//                    //$nextDay = ucwords($repeatOn[$i]); echo $nextDay . '<br/>';
//                    $nextDay = ucwords($recurringOn[$i]);
//                    echo $nextDay . '<br/>';
//                    $nextDayNr = date('N', strtotime($nextDay));
//
//                    $nextDate = date('Y-m-d', strtotime("next {$nextDay}", strtotime($date)));
//                    echo $nextDate . '<br/>';
//                    //$nextDate = date('Y-m-d h:i:s', strtotime("{$dateInterval} {$nextDay} days", strtotime($date)));
//                    $nextEndDate = date('Y-m-d', strtotime($interval, strtotime($nextDate)));
//
//                    $stackStartIntervalDates[] = $nextDate;
//                    $stackEndIntervalDates[] = $nextEndDate;
//
//                    $date = $nextDate;
//
//                    //                 echo date('l', strtotime($date));exit;
//                    $i++;
//
//                    if ($i > $count) {
//                        //echo date('Y-m-d', strtotime("next {$nextDay}", strtotime($date))) . '<br/>';
//                        $date = date('Y-m-d', strtotime("{$recurringBy}", strtotime($nextDate)));
//                        echo $date . '<br/>';
//                        $i = 0;
//                        reset($recurringOn);
//                    }
//
//                    $occurence--;
//                } exit;
//            }
//                foreach ($recurringOn as $d) {
//
//                    //$currDayN = date('N', strtotime($d));
////                        
////                        if (in_array(strtolower($dow), $recurringOn)) {
////                            continue;
////                        }
//                    // skip the rest
//                    if ($occurence == 0) {
//                        continue;
//                    }
//
//                    $nextDayOfWeekStart = date('Y-m-d', strtotime($object->getEventStartDate() . 'next ' . $d));
//
//                    if (in_array($nextDayOfWeekStart, $stackStartIntervalDates) || in_array($nextDayOfWeekStart . ' ' . $timeStart, $stackStartIntervalDates)) {
//                        continue;
//                    }
//                    // add proper date to the stack
//                    $stackStartIntervalDates[$occurence] = $nextDayOfWeekStart . ' ' . $timeStart;
//                    // calculate end date by difference using the start date
//                    $nextDayOfWeekEnd = strtotime("+{$interval->days} day", strtotime($nextDayOfWeekStart));
//                    // add with proper format to the stack
//                    $stackEndIntervalDates[$occurence] = date('Y-m-d', $nextDayOfWeekEnd) . ' ' . $timeEnd;
//                    // collective occurence decrement
//                    $occurence--;
//                }
        }

        /**
         * calculate recurring start dates by intervals 
         * by occurrences, serialize a stack to use for frontend
         * */
        while ($occurence > 0) {
//            $nextRecurring = strtotime("{$day} {$recurringBy}"); // 2nd occurence of the event.
//
//            $formatStartDate = date('Y-m-d', $nextRecurring);
//            if (in_array($formatStartDate, $stackStartIntervalDates) || in_array($formatStartDate . ' ' . $timeStart, $stackStartIntervalDates)) {
//                continue;
//            }
//
//            
//            $stackStartIntervalDates[] = date('Y-m-d', $nextRecurring) . ' ' . $timeStart;
//
//            $day = $stackStartIntervalDates[$occurence];
//
//
//            $nextEndDate = strtotime("+{$interval->days} day", strtotime($day));
//            $stackEndIntervalDates[$occurence] = date('Y-m-d', $nextEndDate) . ' ' . $timeEnd;
//
//            $occurence--;

            $nextDate = date('Y-m-d', strtotime($recurringBy, strtotime($date)));
            $nextEndDate = date('Y-m-d', strtotime($difference, strtotime($nextDate)));
            //$recurrenceDays[$recurrenceLimit] = $nextDate . ',' . $nextEndDate;
            $stackStartIntervalDates[] = $nextDate . ' ' . $timeStart;
            $stackEndIntervalDates[] = $nextEndDate . ' ' . $timeEnd;

            $date = $nextDate;
            $occurence--;
        } //echo '<pre>';print_r($stackEndIntervalDates);exit;
        //$stackStartIntervalDates = array_unique($stackStartIntervalDates);
        //$stackEndIntervalDates = array_unique($stackEndIntervalDates); // echo '<pre>';print_r($stackStartIntervalDates);exit;
        //array_unshift($stackStartIntervalDates, date('Y-m-d h:i:s', strtotime($object->getEventStartDate()))); 
        //array_unshift($stackEndIntervalDates, date('Y-m-d h:i:s', strtotime($object->getEventEndDate()))); 


        if ($object->getRecurringBy() == 'Weekly' && $object->getRecurringOn() != '') {
            $repeatWeekDaysStart = array();
            $repeatWeekDaysEnd = array();
            $recurringOn = explode(',', $object->getRecurringOn());


            /**
             * first iteration must not repeat
             */
//                $key = array_search(strtolower($dow), $recurringOn);
//                if ($key !== false) {
//                    unset($recurringOn[$key]);
//                } //echo '<pre>';print_r($recurringOn);exit;
            //sort($recurringOn); // sort days #numeric

            $repeatCounter = $object->getRecurringOccurrences() - 1;
            $count = count($recurringOn) - 1;
            $i = 1;
            $key = array_search(strtolower($dow), $recurringOn);
            if ($key !== false) {
                unset($recurringOn[$key]);
                //$i = $key;
            }

//echo '<pre>';print_r($recurringOn);exit;
//            while (current($recurringOn) == $dowToLower) {
//                next($recurringOn);
//            }
            //while ($repeatCounter > 0) {
            // calculate weekdays for first occurence
            foreach ($recurringOn as $d) {
                $nextDate = date('Y-m-d', strtotime("next {$d}", strtotime($object->getEventStartDate())));
                $nextEndDate = date('Y-m-d', strtotime($difference, strtotime($nextDate)));
                $repeatWeekDaysStart[] = $nextDate . ' ' . $timeStart;
                $repeatWeekDaysEnd[] = $nextEndDate . ' ' . $timeEnd;
                $repeatCounter--;
                $kt++;
            }


            foreach ($stackStartIntervalDates as $k => $srcdt) {
                if ($repeatCounter < 1) { //echo $repeatCounter;exit;
                    break;
                }

                // source day as it is
                $repeatWeekDaysStart[] = $srcdt;
                $repeatWeekDaysEnd[] = $stackEndIntervalDates[$k];
                $repeatCounter--;
                if ($repeatCounter < 1) { //echo $repeatCounter;exit;
                    break;
                }

                //echo '<pre>';print_r($repeatWeekDaysStart);
                // count for days other than source weekday
                foreach ($recurringOn as $day) {
                    if ($repeatCounter < 1) { //echo $repeatCounter;exit;
                        break;
                    }

                    $nextDate = date('Y-m-d', strtotime("next {$day}", strtotime($srcdt)));
                    $nextEndDate = date('Y-m-d', strtotime($difference, strtotime($nextDate)));
                    $repeatWeekDaysStart[] = $nextDate . ' ' . $timeStart;
                    $repeatWeekDaysEnd[] = $nextEndDate . ' ' . $timeEnd;
                    $repeatCounter--;
                }

                if ($repeatCounter < 1) { //echo $repeatCounter;exit;
                    break;
                }


//                while ($repeatCounter > 0) {
//
//                    //echo $recurringOn[$i];
//                    $nextDate = date('Y-m-d', strtotime("next {$recurringOn[$i]}", strtotime($srcdt)));
//                    $nextEndDate = date('Y-m-d', strtotime($difference, strtotime($nextDate)));
//
//                    $repeatWeekDaysStart[] = $nextDate;
//                    $repeatWeekDaysEnd[] = $nextEndDate;
//                    $i++;
//                    $repeatCounter--;
//
//                    if ($i > $count) {
//                        //reset($recurringOn);
//                        $i = 1;
//                        continue;
//                    }
//                }
            }

            $stackStartIntervalDates = $repeatWeekDaysStart;
            $stackEndIntervalDates = $repeatWeekDaysEnd;

//            echo '<pre>';
//            print_r($repeatWeekDaysStart);
//            exit;
            //}
        }

//echo '<pre>';
//            print_r($stackStartIntervalDates);
//            exit;
        if($stackStartIntervalDates):
        for ($i = 0; $i < count($stackStartIntervalDates); $i++) {
            $_prepData = array(
                'event_id' => $object->getEventId(),
                //'recurring_by' => $object->getRecurringBy(),
                //'recurring_intervals' => $object->getRecurringIntervals(),
                //'recurring_occurrences' => $object->getRecurringOccurrences(),
                'recurring_start_dates' => $stackStartIntervalDates[$i],
                'recurring_end_dates' => $stackEndIntervalDates[$i],
                'difference_days' => $interval->days,
            );

            $this->_getWriteAdapter()
                    ->insert($this->getTable('events/recurring'), $_prepData);
        } endif;


        return parent::_afterSave($object);
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object) 
    {

        if (!$object->getIsMassDelete()) {
            $object = $this->__loadImage($object);
        }

        $products = $this->__listProducts($object);
        if ($products) {
            $object->setData('product_id', $products);
        }

        $selectStores = $this->_getReadAdapter()
                ->select()
                ->from($this->getTable('events/events_store'))
                ->where('event_id = (?)', $object->getId());

        $storesData = $this->_getReadAdapter()->fetchAll($selectStores);
        if ($storesData) {
            $storeIds = array();
            foreach ($storesData as $_row) {
                $storeIds [] = $_row ['store_id'];
            }

            $object->setData('store_id', $storeIds);
        }

        if ($object->getRecurringOn()) {
            $object->setData('recurring_on', explode(',', $object->getRecurringOn()));
        }

        //$this->loadRecurringInfo($object);
        
        return parent::_afterLoad($object);
    }

    public function loadRecurringInfo(Mage_Core_Model_Abstract $object, $date = '') 
    {
        
        $sql = $this->_getReadAdapter()
                ->select()
                ->from($this->getTable('events/recurring'))
                ->where('event_id = (?)', $object->getId());
        
        if (strtotime($date)) {
            $date = date('Y-m-d', strtotime($date));
            $sql->where('DATE(recurring_start_dates) = (?) OR DATE(recurring_end_dates) = (?)', $date)
                ->limit(1);
        }

        //echo $sql;exit;
        $object->setData('recurring_info', $this->_getReadAdapter()->fetchRow($sql));
        
        return $object;
    }
    
    private function __loadImage(Mage_Core_Model_Abstract $object) 
    {
        $_q = $this->_getReadAdapter()
                ->select()
                ->from($this->getTable('events/events_gallery'))
                ->where('events_id = (?)', $object->getId())
                ->order(array('image_order ASC', 'image_name'));

        $object->setData('images_all', $this->_getReadAdapter()->fetchAll($_q));

        return $object;
    }

    /*
     * a pvt function all images to corresponding to current event id will store in separate table. @param Mage_Core_Model_Abstract $object
     */

    private function __saveEventImages(Mage_Core_Model_Abstract $object) 
    {
        $_imgArr = array();
        $_imgArr = Zend_Json::decode($object->getData('images')); // echo '<pre>';print_r($_imgArr);exit;
        if (is_array($_imgArr) and sizeof($_imgArr) > 0) {
            $_imgTable = $this->getTable('events/events_gallery');
            $_adapter = $this->_getWriteAdapter();
            $_adapter->beginTransaction();
            try {
                $_condition = $_adapter->quoteInto('events_id = (?)', $object->getId());
                $this->_getWriteAdapter()->delete($this->getTable('events/events_gallery'), $_condition);
                foreach ($_imgArr as $_i) {
                    if (isset($_i ['removed']) && $_i ['removed'] == '1') {
                        $_adapter->delete($_imgTable, $_adapter->quoteInto('image_id = (?)', $_i ['value_id']), 'INTEGER');
                    } else {
                        $_data = array(
                            'image_file' => $_i ['file'],
                            'image_name' => $_i ['label'],
                            'image_order' => $_i ['position'],
                            'image_status' => $_i ['disabled'],
                            'events_id' => $object->getId()
                        );
                        $_adapter->insert($_imgTable, $_data);
                    }
                }

                $_adapter->commit();
            } catch (Exception $e) {
                $_adapter->rollBack();
               // echo $e->getMessage();
                //exit();
            }
        }
    }

    private function __listProducts(Mage_Core_Model_Abstract $object) 
    {
        $select = $this->_getReadAdapter()
                ->select()
                ->from($this->getTable('events/events_product'))
                ->where('eventid = ?', $object->getId());
        $data = $this->_getReadAdapter()->fetchAll($select);
        if ($data) {
            $productsArr = array();
            foreach ($data as $_i) {
                $productsArr [] = $_i ['product_id'];
            }

            return $productsArr;
        }
    }

    public function loadByPrefix(FME_Events_Model_Events $obj, $prefix) 
    {
        $select = $this->_getReadAdapter()
                ->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.event_url_prefix = (?)', $prefix);
        $id = $this->_getReadAdapter()->fetchOne($select);
        if ($id) {
            $this->load($obj, $id);
        }

        return $this;
    }

    protected function _isReserveWord(Mage_Core_Model_Abstract $object) 
    {
        $is_reserved = false;
        $reserve_words = array('calendar', 'view', 'events');
        if (in_array($object->getData('event_url_prefix'), $reserve_words)) {
            $is_reserved = true;
        }

        return $is_reserved;
    }

    /**
     *  Check whether event event_url_prefix is numeric
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericEventIdentifier(Mage_Core_Model_Abstract $object) 
    {
        return preg_match('/^[0-9]+$/', $object->getData('event_url_prefix'));
    }

    /**
     *  Check whether event event_url_prefix is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidEventIdentifier(Mage_Core_Model_Abstract $object) 
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('event_url_prefix'));
    }

    /**
     * Check for unique of identifier of event to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueEventToStores(Mage_Core_Model_Abstract $object) 
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array) $object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('event_url_prefix'), $stores);

        if ($object->getId()) {
            $select->where('es.event_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Page $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) 
    {

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int) $object->getStoreId());
            $select->join(
                array('es' => $this->getTable('events/events_store')), $this->getMainTable() . '.event_id = es.event_id', array()
            )
                    ->where('event_status = ?', 1)
                    ->where('es.store_id IN (?)', $storeIds)
                    ->order('es.store_id DESC')
                    ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null) 
    {
        $select = $this->_getReadAdapter()->select()
                ->from(array('e' => $this->getMainTable()))
                ->join(
                    array('es' => $this->getTable('events/events_store')), 'e.event_id = es.event_id', array()
                )
                ->where('e.event_url_prefix = ?', $identifier)
                ->where('es.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('e.event_status = ?', $isActive);
        }

        return $select;
    }

    /**
     * Upload and resize image for save
     * @return mix
     */
    protected function _uploadResizeImage() 
    {

        $data = array();
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


                $data['event_image'] = $this->_resizeImage($file);
                $data['event_thumb_image'] = $this->_resizeImage($file, true);

                return $data;
            } catch (Exception $ex) {
                Mage::log($ex->getMessage());
            }
        }
    }

    /**
     * resize and save image
     * @param array $file
     * @param bool $isThumb
     * @return string $filePath
     */
    public function resizeImage($file, $isThumb = false) 
    {

        $width = 465;
        $height = NULL;
        $filePath = '';
        $baseURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'events' . DS . 'resized';
        $pathToSave = 'events' . DS . 'resized';

        $varImg = new Varien_Image($file['path'] . DS . $file['file']);
        $varImg->constrainOnly(TRUE);
        $varImg->keepAspectRatio(FALSE);
        $varImg->keepFrame(FALSE);

        $resizedPath = Mage::getBaseDir('media') . DS . 'events' . DS . 'resized';
        if ($isThumb) {
            $varImg->keeptransparency(FALSE);
            $varImg->backgroundColor(
                array(
                255,
                255,
                255
                )
            ); // WHITE BACKGROUND
            $resizedPath = Mage::getBaseDir('media') . DS . 'events' . DS . 'thumbs';
            $baseURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'events' . DS . 'thumbs';
            $pathToSave = 'events' . DS . 'thumbs';
            $width = 135;
            $height = 135;
        }

        $varImg->resize($width, $height);
        $varImg->save($resizedPath, $file['file']);

        $filePath = $pathToSave . DS . $file['file'];

        return $filePath;
    }

}
