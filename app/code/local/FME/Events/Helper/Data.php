<?php

class FME_Events_Helper_Data extends Mage_Core_Helper_Abstract
{

    const EVENT_PAGE_TITLE = 'events_options/seo_info/page_title';
    const EVENT_META_DESCRIPTION = 'events_options/seo_info/meta_description';
    const EVENT_META_KEYWORDS = 'events_options/seo_info/meta_keywords';
    const EXT_IDENTIFIER = 'events_options/seo_info/events_url_prefix';
    /* Meta Seo related configurations */
    const OUT_OF_STOCK = 'events_options/event_status_notifications/out_of_stock';
    const EXPIRED_EVENT = 'events_options/event_status_notifications/expired_event';
    /* event notifications messages */
    const HEADER_LINK_TITLE = 'events_options/basic_configs/header_link';
    const BOTTOM_LINK_TITLE = 'events_options/basic_configs/bottom_link';
    /* header footer access links */
    const CALENDAR_LAYOUT = 'events_options/events_pages_layouts/events_calendar_layout';
    const LANDING_LAYOUT = 'events_options/events_pages_layouts/landing_layout';
    const EVENT_VIEW_LAYOUT = 'events_options/events_pages_layouts/events_view_layout';
    const GRID_COLUMNS = 'events_options/events_pages_layouts/grid_columns';
    /* pages layouts */
    const EVENTS_OF_DATE = 'events_options/basic_configs/static_block_events';
    const SHOW_MAP = 'events_options/basic_configs/show_map';
    const BREADCRUMBS_ENABLE = 'events_options/basic_configs/breadcrumb_enable';
    const ERR_EMPTY_COLLECTION = 'events_options/event_status_notifications/err_empty_collection';

    public function linkTitleHeader() 
    {
        return Mage::getStoreConfig(self::HEADER_LINK_TITLE, Mage::app()->getStore()->getId());
    }

    public function linkTitleBottom() 
    {
        return Mage::getStoreConfig(self::BOTTOM_LINK_TITLE, Mage::app()->getStore()->getId());
    }

    public function isEnableMapShow() 
    {
        return Mage::getStoreConfig(self::SHOW_MAP);
    }

    public function isEnableBreadcrumbs() 
    {
        return (int) Mage::getStoreConfig(self::BREADCRUMBS_ENABLE, Mage::app()->getStore()->getId());
    }

    /**
     * to produce a number for columns in landing layout grid
     * follwoing the magento default grid
     * @return int $n the number for columns
     * */
    public function numOfGridColumns() 
    {
        $layout = (string) Mage::getStoreConfig(self::LANDING_LAYOUT, Mage::app()->getStore()->getId());
        $n = 0;

        switch ($layout) {
            case 'empty':
                $n = 6;
                break;
            case 'one_column':
                $n = 5;
                break;
            case 'two_columns_left':
                $n = 4;
                break;
            case 'two_columns_right':
                $n = 4;
                break;
            case 'three_columns':
                $n = 3;
                break;
            default:
                $n = 3;
        }

        return $n;
    }

    public function errMsg() 
    {
        $err = "No event(s) registered under this date!";
        if (Mage::getStoreconfig(self::ERR_EMPTY_COLLECTION, Mage::app()->getStore()->getId()) != '') {
            $err = Mage::getStoreconfig(self::ERR_EMPTY_COLLECTION, Mage::app()->getStore()->getId());
        }

        return $err;
    }

    public function getSeoInfo($info) 
    {
        $data = '';
        switch ($info) {
            case 'title':
                $data = Mage::getStoreConfig(self::EVENT_PAGE_TITLE, Mage::app()->getStore()->getId());
                break;
            case 'description':
                $data = Mage::getStoreConfig(self::EVENT_META_DESCRIPTION, Mage::app()->getStore()->getId());
                break;
            case 'keywords':
                $data = Mage::getStoreConfig(self::EVENT_META_KEYWORDS, Mage::app()->getStore()->getId());
                break;
        }

        return $data;
    }

    /**
     * fetch notifications from store configuration
     * @param int $type 0:out_of_stock, 1:expired_event
     * @return string $data either empty or with value
     * */
    public function getNotificationType($type) 
    {
        $data = '';
        switch ($type) {
            case 0:
                $data = Mage::getStoreConfig(self::OUT_OF_STOCK, Mage::app()->getStore()->getId());
                break;
            case 1:
                $data = Mage::getStoreConfig(self::EXPIRED_EVENT, Mage::app()->getStore()->getId());
                break;
        }

        return $data;
    }

    public function getEventsFromCal() 
    {
        return Mage::getStoreConfig(self::EVENTS_OF_DATE, Mage::app()->getStore()->getId());
    }

    public function checkDuplicate($column, $value, $table, $id) 
    {
        $isDuplicate = false;
        $_event = Mage::getModel('events/' . $table)->getCollection();
        $_event->addFieldToFilter($column, $value);

        if ($id) {
            $_event->addFieldToFilter('event_id', array('neq' => $id));
        }

        if ($_event->getData()) {
            $isDuplicate = true;
        }

        return $isDuplicate;
    }

    public function fetchGallery($prefix) 
    {
        $model = Mage::getModel('events/events')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($prefix, 'event_url_prefix');

        $id = $model->getEventId();
        $pick = Mage::getSingleton('core/resource');
        $read = $pick->getConnection('core_read');
        $table = $pick->getTableName('events/events_gallery');

        $select = $read->select()->from(array('eg' => $table))
                ->where('eg.events_id = (?)', (int) $id)
                ->where('eg.image_status != (?)', 1)
                ->order('eg.image_order');
        //echo $select;exit;
        return $read->fetchAll($select);
    }

    /*
     * check event validity
     * @param int $int event id
     * @return bool
     */

    public function isExpiredEvent($id) 
    {
        $model = Mage::getModel('events/events')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($id);
        $eventEndDate = $model->getEventEndDate();
        $currentDate = now();
        $isExpired = false;
        if ($currentDate > $eventEndDate) {
            $isExpired = true;
        }

        return $isExpired;
    }

    public function clientUrl() 
    {
        return Mage::getUrl($this->extIdentifier());
    }

    public function extIdentifier() 
    {
        $identifier = (string) Mage::getStoreConfig(self::EXT_IDENTIFIER, Mage::app()->getStore()->getId());
        if ($identifier == '') {
            $identifier = 'all-events';
        }

        return $identifier;
    }

    public function customUrl($input = '') 
    {
        return Mage::getUrl($this->extIdentifier() . '/' . $input);
    }

    public function calendarLayout() 
    {
        return $this->getMyLayout((string) Mage::getStoreConfig(self::CALENDAR_LAYOUT, Mage::app()->getStore()->getId()));
    }

    public function landingLayout() 
    {
        return $this->getMyLayout((string) Mage::getStoreConfig(self::LANDING_LAYOUT, Mage::app()->getStore()->getId()));
    }

    public function eventViewLayout() 
    {
        return $this->getMyLayout((string) Mage::getStoreConfig(self::EVENT_VIEW_LAYOUT, Mage::app()->getStore()->getId()));
    }

    public function getMyLayout($input) 
    {
        $layout = 'page/1column.phtml';

        switch ($input) {
            case 'empty':
                $layout = 'page/empty.phtml';
                break;
            case 'one_column':
                $layout = "page/1column.phtml";
                break;
            case 'two_columns_left':
                $layout = 'page/2columns-left.phtml';
                break;
            case 'two_columns_right':
                $layout = 'page/2columns-right.phtml';
                break;
            case 'three_columns':
                $layout = 'page/3columns.phtml';
                break;
            default:
                $layout = 'page/1column.phtml';
        }

        return $layout;
    }

    public function isValidDate($date) 
    {
//echo date('Y-m-d',strtotime($date));exit;
        $isValid = false;
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", trim(date('Y-m-d', strtotime($date))), $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                $isValid = true;
            }
        }

        return $isValid;
    }

    public function eventsIn($store = null) 
    {

        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }

        $daysOf = (int) Mage::getStoreConfig('events_options/basic_configs/static_block_ndaysevents', Mage::app()->getStore()->getId());

        if ($daysOf > 0) { 
            return $this->eventsByNDays($daysOf);
        }

        /* number of days given will have priority */
        $resource = Mage::getSingleton('core/resource');
        $tbl = $resource->getTableName('events/events');
        $read = $resource->getConnection('core_read');
        $dateDiff = (string) $this->getEventsFromCal();

        $date = new Zend_Db_Expr('CURDATE()');

        $latestEvents = $read->select()->from(array('evt' => $tbl))
                ->join(array('es' => $resource->getTableName('events/events_store')), 'evt.event_id = es.event_id', array())
                ->where('es.store_id IN (?)', array(0, $store))
                ->where('DATE(evt.event_start_date) >= (?)', $date)
                ->where('evt.event_status = (?)', 1);

        $curDay = $read->select()->from(array('evt' => $tbl))
                ->join(array('es' => $resource->getTableName('events/events_store')), 'evt.event_id = es.event_id', array())
                ->where('es.store_id IN (?)', array(0, $store))
                ->where('DATE(evt.event_start_date) = (?)', $date)
                ->where('evt.event_status = (?)', 1);

        $curWeek = "SELECT * FROM " . $tbl . " INNER JOIN {$resource->getTableName('events/events_store')} as es 
        WHERE es.store_id IN (0,{$store}) AND WEEK(event_start_date) = WEEK(CURRENT_DATE) AND event_status = 1
		ORDER BY DATE(event_start_date)";

        $curMonth = "SELECT * FROM " . $tbl . " INNER JOIN {$resource->getTableName('events/events_store')} as es 
        WHERE es.store_id IN (0,{$store}) AND MONTH(event_start_date) = MONTH(CURRENT_DATE) AND event_status = 1
		ORDER BY DATE(event_start_date)";

        $select = '';

        switch ($dateDiff) {
            case 'curr_day':
                $select = $curDay;
                break;
            case 'curr_week':
                $select = $curWeek;
                break;
            case 'curr_month':
                $select = $curMonth;
                break;
            case 'latest_event':
                $select = $latestEvents;
                break;
        }

        //echo $select;exit;
        $q = $read->query($select);

        $result = $q->fetchAll();

        return $result;
    }

    public function eventsByNDays($days = null, $store = null) 
    {

        if (is_null($days)) {
            $days = 5;
        }

        if ($store == null) {
            $store = Mage::app()->getStore()->getId();
        }

        $conn = Mage::getSingleton('core/resource');
        $tbl = $conn->getTableName('events/events');
        $read = $conn->getConnection('core_read');

        $sql = "SELECT e.*
		FROM {$tbl} AS e
        INNER JOIN {$conn->getTableName('events/events_store')} as es 
        WHERE es.store_id IN (0,{$store})
		AND DATE(e.event_start_date) BETWEEN DATE(NOW()) AND DATE_ADD(DATE(NOW()), INTERVAL {$days} DAY)
		AND e.event_status = 1
                GROUP BY e.event_id
		ORDER BY DATE(e.event_start_date)";
        //$where = "e.event_start_date = DATE(DATE_ADD(e.event_start_date, INTERVAL 1 DAY))";
        // reference: http://www.w3schools.com/sql/func_date_add.asp
        $q = $read->query($sql);

        return $q->fetchAll();
    }

    /**
     * to fetch all ids in 0 and current store
     * than same ids will be use to fetch collection henced avoiding the
     * duplication.
     * @param int $storeId current store id
     * */
    public function getSameIdsFromStores($storeId) 
    {
        $m = Mage::getModel('events/events')->getCollection();
        $m->addStoreFilter($storeId);
        $m->addFieldToFilter('main_table.event_status', 1);
        $m->getSelect()->distinct('main_table.event_id');
        $ids = array();
        $_data = $m->getData();
        if (!empty($_data)) {
            foreach ($_data as $i) {
                $ids[] = $i['event_id'];
            }
        }

        return $ids;
    }

    /**
     * to remove gallery images for an event.
     * @param int $id
     * @return unknown
     * */
    public function removeGalleryByEventId($id) 
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $write = $resource->getConnection('core_write');
        $table = $resource->getTableName('events_gallery');
        $eventMediaPath = Mage::getBaseDir('media') . DS . 'events';
        $condition = $write->quoteInto('events_id = ?', $id, 'INTEGER');
        $select = $read->select()
                ->from(array('e_gallery' => $table))
                ->where('e_gallery.events_id = ?', $id);
        $gallery = $read->fetchAll($select); // echo '<pre>';print_r($gallery);exit;
        foreach ($gallery as $g) {
            unlink($eventMediaPath . $g['image_file']);
        }

        $write->delete($table, $condition);
    }

    public function getstores() 
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $pressTable = $resource->getTableName('events/events_store');
        $qry = "select store_id FROM " . $pressTable . " where event_id <> 0"; //query

        $res = $read->fetchAll($qry);
        return $res;
    }

    public function getDateType($type = 'medium') 
    {
        $format = '';
        switch ($type) {
            case 'medium':
                $format = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
                break;
            case 'long':
                $format = Mage_Core_Model_Locale::FORMAT_TYPE_LONG;
                break;
            case 'short':
                $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
                break;
            default:
                $format = Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM;
        }

        return $format;
    }

    public function recurringBy($type, $times) 
    {
        switch ($type) {
            case "Daily":
                $dateCalculation = " +{$times} day";
                break;
            case "Weekly":
                $dateCalculation = " +{$times} weeks";
                break;
            case "Monthly":
                $dateCalculation = " +{$times} months";
                break;
            case "Yearly":
                $dateCalculation = " +{$times} years";
                break;
            default:
                $dateCalculation = "none";
        }
        
        return $dateCalculation;
    }

    /**
     * Resize Image proportionally and return the resized image url
     *
     * @param string $imageName         name of the image file
     * @param integer|null $width       resize width
     * @param integer|null $height      resize height
     * @param string|null $imagePath    directory path of the image present inside media directory
     * @return string               full url path of the image
     */
    public function resizeImage($imageName, $width = NULL, $height = NULL, $imagePath = NULL) 
    {

        $imagePath = str_replace("/", DS, $imagePath);
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $imageName;

        if ($width == NULL && $height == NULL) {
            $width = 100;
            $height = 100;
        }

        $resizePath = $width . 'x' . $height;
        $resizePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $resizePath . DS . $imageName;

        if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imagePathFull);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->resize($width, $height);
            $imageObj->save($resizePathFull);
        }

        $imagePath = str_replace(DS, "/", $imagePath);
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $imagePath . "/" . $resizePath . "/" . $imageName;
    }

     /**
     * Returns the resized Image URL
     *
     * @param string $imgUrl - This is relative to the the media folder (custom/module/images/example.jpg)
     * @param int $x Width
     * @param int $y Height
     */
    public function getResizedUrl($imgUrl,$x,$y=NULL)
    {
        
        $imgPath = $this->splitImageValue($imgUrl, "path");
        $imgName = $this->splitImageValue($imgUrl, "name");

        $imgPathToFull = str_replace("resized", "", $imgPath);
                
        /**
         * Path with Directory Seperator
         */
        $imgPath=str_replace("/", DS, $imgPath);  

        /**
         * Absolute full path of Image
         */
        $imgPathFull = Mage::getBaseDir("media").DS.$imgPathToFull.DS.$imgName;

        /**
         * If Y is not set set it to as X
         */
        $widht = $x;
        $y?$height = $y:$height=$x;

        /**
         *
         * Resize folder is widthXheight
         */
        $resizeFolder=$widht."X".$height;

        /**
         * Image resized path will then be
         */
        $imageResizedPath=Mage::getBaseDir("media").DS.$imgPath.DS.$resizeFolder.DS.$imgName;

        /**
         * First check in cache i.e image resized path
         * If not in cache then create image of the width=X and height = Y
         */
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
            $imageObj = new Varien_Image($imgPathFull);
            $imageObj->constrainOnly(true); // image picture will not be bigger, than it was
            $imageObj->keepAspectRatio(true);  // image picture width/height will not be distorted

            $imageObj->keepFrame(true); // image will have dimensions, set in $width/$height
            $imageObj->keepTransparency(true);
            $imageObj->backgroundColor(array(255,255,255));

            $imageObj->resize($widht, $height);
            $imageObj->save($imageResizedPath);
        endif;

        /**
         * Else image is in cache replace the Image Path with / for http path.
         */
        $imgUrl=str_replace(DS, "/", $imgPath);

        /**
         * Return full http path of the image
         */
        return Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;
    }

    public function splitImageValue($imageValue,$attr="name")
    {
        $imArray=explode("/", $imageValue);

        $name=$imArray[count($imArray)-1];
        $path=implode("/", array_diff($imArray, array($name)));
        if($attr=="path"){
            return $path;
        }
        else
            return $name;

    }


    /*
     * Converts title to URL key according to URL standard
     * @param string @name title
     * @return string URL key
     */
    public function nameToUrlKey($name)
    {
        $_URL_ENCODED_CHARS = array(
            ' ', '+', '(', ')', ';', ':', '@', '&', '`', '\'',
            '=', '!', '$', ',', '/', '?', '#', '[', ']', '%',
        );

        $name = strtolower(trim($name));
        $name = str_replace($_URL_ENCODED_CHARS, '-', $name);
        do {
            $name = $newStr = str_replace('--', '-', $name, $count);
        } while($count);

        return $name;
    }

}
