<?php
/**
 * Medvslav_News2 extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Medvslav
 * @package        Medvslav_News2
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Newscategory collection resource model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Resource_Newscategory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('medvslav_news2/newscategory');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add filter by store
     *
     * @access public
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!isset($this->_joinedFields['store'])) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            if (!is_array($store)) {
                $store = array($store);
            }
            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            $this->addFilter('store', array('in' => $store), 'public');
            $this->_joinedFields['store'] = true;
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @access protected
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('medvslav_news2/newscategory_store')),
                'main_table.entity_id = store_table.newscategory_id',
                array()
            )
            ->group('main_table.entity_id');
            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Add Id filter
     *
     * @access public
     * @param array $newscategoryIds
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function addIdFilter($newscategoryIds)
    {
        if (is_array($newscategoryIds)) {
            if (empty($newscategoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $newscategoryIds);
            }
        } elseif (is_numeric($newscategoryIds)) {
            $condition = $newscategoryIds;
        } elseif (is_string($newscategoryIds)) {
            $ids = explode(',', $newscategoryIds);
            if (empty($ids)) {
                $condition = $newscategoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Add newscategory path filter
     *
     * @access public
     * @param string $regexp
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Add newscategory path filter
     *
     * @access public
     * @param array|string $paths
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write  = $this->getResource()->getWriteConnection();
        $cond   = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add newscategory level filter
     *
     * @access public
     * @param int|string $level
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root newscategory filter
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @access public
     * @param string $field
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add active newscategory filter
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     */
    public function addStatusFilter($status = 1)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * Get nescategories as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     * @author Medvslav
     */
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                continue;
            }
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }

    /**
     * Get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     * @author Medvslav
     */
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        $res = array();
        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                continue;
            }
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Medvslav
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
