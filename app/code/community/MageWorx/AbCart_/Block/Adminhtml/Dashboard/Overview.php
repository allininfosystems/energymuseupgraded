<?php
/**
 * MageWorx
 * Abandoned Cart Recovery
 *
 * @category   MageWorx
 * @package    MageWorx_AbCart
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_AbCart_Block_Adminhtml_Dashboard_Overview extends Mage_Adminhtml_Block_Abstract
{
    protected $_stats = false;

    protected function _prepareLayout()
    {
        $this->setTemplate('mageworx/abcart/dashboard/overview.phtml');
        parent::_prepareLayout();
    }

    protected function _prepareStatistics()
    {
        $data = array();
        $stats = Mage::getModel('mageworx_abcart/statistics');

        $data['carts_started'] = $stats->getStartedStat()->getCount();

        $abandonedRevenue = $stats->getAbandonedStat(false)->getRevenue();
        $data['abandoned_revenue'] = Mage::helper('core')->currency($abandonedRevenue, true, false);
        $data['carts_abandoned'] = $stats->getAbandonedStat(false)->getCount();

        $recoveredRevenue = $stats->getAbandonedStat(true)->getRevenue();
        $data['recovered_revenue'] = Mage::helper('core')->currency($recoveredRevenue, true, false);
        $data['carts_recovered'] = $stats->getAbandonedStat(true)->getCount();

        $abRate = ($data['carts_started'] > 0) ? ($data['carts_abandoned'] / $data['carts_started']) * 100 : 0;
        $data['abandoned_rate'] = round($abRate, 2) . '%';

        $lostRevenue = $abandonedRevenue - $recoveredRevenue;
        $data['lost_revenue'] = Mage::helper('core')->currency($lostRevenue, true, false);

        $this->_stats = new Varien_Object($data);
    }

    /**
     * @return bool
     */
    public function getStatistics()
    {
        if (!$this->_stats) {
            $this->_prepareStatistics();
        }
        return $this->_stats;
    }
}