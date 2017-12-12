<?php

class Magestore_Megamenu_Block_Item extends Mage_Core_Block_Template
{

    protected $_categoryCollection;
    protected $_productCollection;
    protected $_featuredCategoryCollection;
    protected $_featuredProductCollection;
    protected $_parentCategories;


    /**
     * @return mixed
     */
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    /**
     * get category collection from data of menu item
     * @return category collection
     */
    public function getCategories() {
        if (is_null($this->_categoryCollection)) {
            $item = $this->getItem();
            if ($item && $item->getId()) {
                $collection = $item->getCategoryCollection();
                $this->_categoryCollection = $collection;
            }
        }
        return $this->_categoryCollection;
    }

    /**
     * @param $categoryIds
     */
    public function setParentCategories($categoryIds) {
        if (is_array($categoryIds)) {
            $collection = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $categoryIds))
                ->setOrder('position', 'ASC');
            $this->_parentCategories = $collection;
        }
    }

    /**
     * get product collection from data of menu item
     * @return product collection
     */
    public function getProducts($store = null) {
        if (is_null($this->_productCollection)) {
            $data = $this->getItem()->getData();
            $proIds = array(0);
            if (isset($data['products']) && $data['products']) {
                $proIds = explode(', ', $data['products']);
            }

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $proIds));
            if ($store)
                $collection->addStoreFilter($store);
            $collection->addAttributeToFilter('status', 1);
            $visibleStatus = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
            );
            //$collection->setVisibility(Mage::getModel('catalog/product_visibility')->getVisibleInSiteIds());
            $collection->addAttributeToFilter('visibility', array('in' => $visibleStatus));
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getPriceHtml($product) {
        $block = Mage::getBlockSingleton('catalog/product_list');
        $block->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
        $block->addPriceBlockType('msrp', 'catalog/product_price', 'catalog/product/price_msrp.phtml');
        $block->addPriceBlockType('msrp_item', 'catalog/product_price', 'catalog/product/price_msrp_item.phtml');
        $block->addPriceBlockType('msrp_noform', 'catalog/product_price', 'catalog/product/price_msrp_noform.phtml');
        return $block->getPriceHtml($product, true);
    }

    /**
     * get featured category collection from data of menu item
     * @return category collection
     */
    public function getFeaturedCategories() {
        if (is_null($this->_featuredCategoryCollection)) {
            $data = $this->getItem()->getData();
            $catIds = array(0);
            if (isset($data['featured_categories']) && $data['featured_categories']) {
                $catIds = explode(', ', $data['featured_categories']);
            }

            $collection = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $catIds)
                ->setOrder('position', 'ASC');
            $this->_featuredCategoryCollection = $collection;
        }
        return $this->_featuredCategoryCollection;
    }

    /**
     * get featured product collection from data of menu item
     * @return product collection
     */
    public function getFeaturedProducts() {
        if (is_null($this->_featuredProductCollection)) {
            $data = $this->getItem()->getData();
            $proIds = array(0);
            if (isset($data['featured_products']) && $data['featured_products']) {
                $proIds = explode(', ', $data['featured_products']);
            }

            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $proIds);
            $store = Mage::app()->getStore();
            if ($store)
                $collection->addStoreFilter($store);
            $collection->addAttributeToFilter('status', 1);
            $visibleStatus = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
            );
            $collection->addAttributeToFilter('visibility', array('in' => $visibleStatus));
            $this->_featuredProductCollection = $collection;
        }
        return $this->_featuredProductCollection;
    }

    /**
     * @return int|string
     */
    public function getColumnNumber() {
        $data = $this->getItem()->getData();
        $columnNumber = '';
        if (isset($data['colum']) && $data['colum']) {
            $columnNumber = $data['colum'];
        }
        if ($columnNumber)
            return $columnNumber;
        else
            return 4;
    }

    /**
     * @param $categoryId
     * @param null $childs
     * @return array|null
     */
    public function getChildren($categoryId, $childs = null) {
        $childIdsArray = is_null($childs) ? array() : $childs;
        $category = Mage::getModel('catalog/category')->load($categoryId);
        if (count($category->getChildrenCategories()) > 0) {
            $tmp_array = array();
            foreach ($category->getChildrenCategories() as $cat) {
                array_push($tmp_array, $cat->getId());
                if ($cat->hasChildren()) {
                    $tmp_array = array_merge($tmp_array, $this->getChildren($cat->getId()));
                }
            }
            $childIdsArray = array_merge($childIdsArray, $tmp_array);
        } else {
            return array_unique($childIdsArray);
        }
        return $childIdsArray;
    }

    /**
     * get children category collection of category
     * @param type $categoryId
     * @return category collection
     */
    /**
     * get one featured product of menu item
     * @return array
     */
    public function getAllFeaturedProduct() {
        $featuredProducts = array(0);
        $data = array();
        if ($this->getFeaturedProducts())
            $featuredProducts = $this->getFeaturedProducts()->getAllIds();
        foreach ($featuredProducts as $productId) {
            $data[] = Mage::getModel('catalog/product')->load($productId);
        }
        return $data;
    }

    /**
     * get one featured category of menu item
     * @return array category
     */
    public function getAllFeaturedCategory() {
        $featuredCategories = array(0);
        $data = array();
        if ($this->getFeaturedCategories())
            $featuredCategories = $this->getFeaturedCategories()->getAllIds();
        foreach ($featuredCategories as $featuredCategory) {
            $data[] = $featuredCategory = Mage::getModel('catalog/category')->load($featuredCategory);
        }
        return $data;
    }

    /**
     * check menu item has featured products or no
     * @return boolean
     */
    public function hasFeaturedProducts() {
        $data = $this->getItem()->getData();
        if (isset($data['featured_type']) && $data['featured_type']) {
            if ($data['featured_type'] == '1') {
                if ($this->getFeaturedProducts() && count($this->getFeaturedProducts()))
                    return true;
            }
        }
        return false;
    }

    /**
     * check menu item has featured item or no
     * @return boolean
     */
    public function hasFeaturedItem() {
        if ($this->hasFeaturedProducts() || $this->hasFeaturedCategories() || $this->hasFeaturedContent())
            return true;
        return false;
    }

    /**
     * check menu item has featured categories or no
     * @return boolean
     */
    public function hasFeaturedCategories() {
        $data = $this->getItem()->getData();
        if (isset($data['featured_type']) && $data['featured_type']) {
            if ($data['featured_type'] == '2') {
                if ($this->getFeaturedCategories() && count($this->getFeaturedCategories()))
                    return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasFeaturedContent() {
        $data = $this->getItem()->getData();
        if (isset($data['featured_type']) && $data['featured_type']) {
            if ($data['featured_type'] == '3') {

                return true;
            }
        }
        return false;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function filterCms($text) {
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();
        return $processor->filter($text);
    }

    /**
     * get parent category collection
     * @return category collection
     */
    public function getParentCategories() {
        if (is_null($this->_parentCategories)) {
            $item = $this->getItem();
            if ($item && $item->getId()) {
                $collection = $item->getParentCategories();
                $this->_parentCategories = $collection;
            }
        }
        return $this->_parentCategories;
    }

    /**
     * @return array
     */
    public function getParentCategoriesIds() {
        $parentIds = array();
        $catIds = explode(', ', $this->getItem()->getCategories());
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $catIds))
            ->addFieldToFilter('is_active', 1)
            ->setOrder('position', 'ASC');
        $categoryIds = $categories->getAllIds();
        foreach ($categories as $category) {
            $parents = $category->getParentIds();
            if (count(array_intersect($parents, $categoryIds)) == 0)
                $parentIds[] = $category->getId();
        }
        return $parentIds;
    }

    /**
     * @param $category
     * @return null
     */
    public function getChildrenCollection($category) {
        if (is_object($category)) {
            $item = $this->getItem();
            $childrenIds = $category->getAllChildren();
            $childrenIds = explode(',', $childrenIds);
            $childrenIds = array_intersect($childrenIds, $item->getCategoryIds());
            $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                ->addFieldToFilter('entity_id', array('in' => $childrenIds))
                ->addFieldToFilter('entity_id', array('neq' => $category->getId()))
                ->setOrder('position', 'ASC')
                ->addAttributeToSelect('*');
            return $categoryCollection;
        }
        return null;
    }

    /**
     * @param $columns_number
     * @return array
     */
    public function getAllCategory($columns_number) {
        $collection = $this->getParentCategories();
        $type = $this->getItem()->getCategoryType();
        $item = $this->getItem();
        $data = array();
        $sort = array();
        $categories = array();
        $columns_number = intval($columns_number);
        foreach ($collection as $category) {
            $category->setLevel(1);
            $data[$category->getId()] = $category;
            if ($category->hasChildren()) {
                $children = $this->getChildrenCollection($category);
                foreach ($children as $child) {
                    if (in_array($child, $data)) continue;
                    $child->setLevel(2);
                    $data[$child->getId()] = $child;
                }
                $sort[$category->getId()] = $children->getSize() + 1;
            } else {
                $sort[$category->getId()] = 1;
            }
        }
        if ($type) {
            $add_cat = 0;
            if (count($data) % $columns_number == 0) {
                $number = count($data) / $columns_number;
            } else {
                $number = floor(count($data) / $columns_number) + 1;
                $add_cat = count($data) % $columns_number;
            }
            $i = 1;
            $j = 1;
            foreach ($data as $cat) {
                $categories[$i][] = $cat;

                if ($j >= $number) {
                    $j = 1;
                    if ($add_cat && $i == $add_cat) {
                        $number = count($data) / $columns_number;
                    }
                    $i++;
                } else {
                    $j++;
                }
            }
            return $categories;
        }
        //asort($sort);
        if (array_sum($sort) % $columns_number == 0)
            $tb = array_sum($sort) / $columns_number;
        else
            $tb = floor(array_sum($sort) / $columns_number) + 1;
        $value_group = array();
        $tb_temp = $tb = intval($tb);
        $tmp = array();
        $du = 0;
        foreach ($sort as $key => $value) {
            if (in_array($key, $tmp)) continue;;
            $value_group[$key][] = $key;
            $columns_number--;
            $total = $value;
            unset($sort[$key]);
            foreach ($sort as $key1 => $value1) {
                $temp = $total + $value1;
                if ($temp > $tb_temp) {
                    continue;
                } else {
                    $total += $value1;

                    $value_group[$key][] = $key1;

                    unset($sort[$key1]);
                    $tmp[] = $key1;
                }
            }
            $du += $tb - $total;
            $tb_temp = $tb + $du;
        }
        foreach ($value_group as $groups) {
            $data_temp = array();
            foreach ($groups as $group) {
                $data_temp[] = $data[$group];
                $ids = explode(',', $data[$group]->getAllChildren());
                $ids = array_intersect($ids, $item->getCategoryIds());
                $catCollection = Mage::getResourceModel('catalog/category_collection')
                    ->addFieldToFilter('entity_id', array('in' => $ids))
                    ->addFieldToFilter('entity_id', array('neq' => $data[$group]->getId()))
                    ->addFieldToFilter('is_active', 1)
                    ->setOrder('position', 'ASC')
                    ->addAttributeToSelect('*');
                foreach ($catCollection as $cat) {
                    $data_temp[] = $cat;
                }
            }
            if (isset($data_temp[0]))
                $categories[] = $data_temp;
        }
        return $categories;

    }

    /**
     * @param $category
     * @param $level
     * @return mixed
     */
    public function getChildrenCategoriesByLevel($category, $level) {
        $categoryids = $this->getItem()->getCategoryIds();
        if ($level == 2) {
            $childs = $category->getChildrenCategories();
            if (is_object($childs)) {
                $childrenIds = $childs->getAllIDs();
            }
            if (is_array($childs)) {
                $childrenIds = array();
                foreach ($childs as $child) {
                    $childrenIds[] = $child->getId();
                }
            }
            $childrenIds = array_intersect($childrenIds, $categoryids);
        } elseif ($level == 3) {
            $childrenIds = $category->getAllChildren();
            $childrenIds = explode(',', $childrenIds);
            $childrenIds = array_intersect($childrenIds, $categoryids);

        }
        if(empty($childrenIds) || !is_array($childrenIds)) {
            $childrenIds = array();
        }
        $childrens = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('entity_id', array('in' => $childrenIds))
            ->addFieldToFilter('entity_id', array('neq' => $category->getId()))
            ->setOrder('position', 'ASC')
            ->addAttributeToSelect('*');

        return $childrens;
    }

    /**
     * @param $string
     * @param int $limit
     * @return mixed
     */
    public function limitString($string, $limit = 100) {
        // Return early if the string is already shorter than the limit
        if (strlen($string) < $limit) {
            return $string;
        }

        $regex = "/(.{1,$limit})\b/";
        preg_match($regex, $string, $matches);
        return $matches[1];
    }

    /**
     * @param $product
     * @return bool
     */
    public function hasImage($product) {
        if ($product && $product->getId()) {
            if ($product->getImage() != 'no_selection' || $product->getSmallImage() != 'no_selection' || $product->getThumbnail() != 'no_selection')
                return true;
        }
        return false;
    }


    /**
     * @param $product
     * @param $size
     * @return string
     */
    public function getImagePath($product, $size) {
        try {
            if ($product->getSmallImage() != 'no_selection') {
                return $this->helper('catalog/image')->init($product, 'small_image')->resize($size);
            } elseif ($product->getThumbnail() != 'no_selection') {
                return $this->helper('catalog/image')->init($product, 'thumbnail_image')->resize($size);
            } elseif ($product->getImage() != 'no_selection') {
                return $this->helper('catalog/image')->init($product, 'image')->resize($size);
            } else {
                if (Mage::getStoreConfig('catalog/placeholder/small_image_placeholder'))
                    return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'media/catalog/product/placeholder/' . Mage::getStoreConfig('catalog/placeholder/small_image_placeholder');
                return Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/small_image.jpg', array('_area' => 'frontend'));
            }
        } catch (Exception $e) {
            return Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/small_image.jpg', array('_area' => 'frontend'));
        }
    }

    /**
     * @return mixed
     */
    public function getListingcategories() {
        $categoryIds = explode(', ', $this->getItem()->getCategories());
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $categoryIds))
            ->setOrder('position', 'ASC');
        return $collection;
    }

    /**
     * @param $category
     * @return mixed
     */
    public function getProductbycategory($category) {
        $store = Mage::app()->getStore()->getStoreId();
        $number_products = $this->getItem()->getNumberProducts();
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->setOrder('position', 'ASC')
            ->addCategoryFilter($category);
        $collection->addStoreFilter($store);
        $collection->addAttributeToFilter('status', 1);
        $visibleStatus = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        );
        $collection->addAttributeToFilter('visibility', array('in' => $visibleStatus));
        if (isset($number_products) && $number_products) {
            $collection->setPageSize($number_products);
        }

        return $collection;
    }

}