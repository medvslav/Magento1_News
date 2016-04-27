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
 * Newscategory resource model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Resource_Newscategory extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Newscategory tree object
     * @var Varien_Data_Tree_Db
     */
    protected $_tree;

    /**
     * Constructor
     *
     * @access public
     * @author Medvslav
     */
    public function _construct()
    {
        $this->_init('medvslav_news2/newscategory', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $newscategoryId
     * @return array
     * @author Medvslav
     */
    public function lookupStoreIds($newscategoryId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('medvslav_news2/newscategory_store'), 'store_id')
            ->where('newscategory_id = ?', (int)$newscategoryId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Medvslav_News2_Model_Newscategory $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('news2_newscategory_store' => $this->getTable('medvslav_news2/newscategory_store')),
                $this->getMainTable() . '.entity_id = news2_newscategory_store.newscategory_id',
                array()
            )
            ->where('news2_newscategory_store.store_id IN (?)', $storeIds)
            ->order('news2_newscategory_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Retrieve newscategory tree object
     *
     * @access protected
     * @return Varien_Data_Tree_Db
     * @author Medvslav
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('medvslav_news2/newscategory_tree')->load();
        }
        return $this->_tree;
    }

    /**
     * Process newscategory data before delete
     * update children count for parent newscategory
     * delete child nescategories
     *
     * @access protected
     * @param Varien_Object $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeDelete($object);
        /**
         * Update children count for all parent nescategories
         */
        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1; // +1 is itself
            $data = array('children_count' => new Zend_Db_Expr('children_count - ' . $childDecrease));
            $where = array('entity_id IN(?)' => $parentIds);
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);
        return $this;
    }

    /**
     * Delete children nescategories of specific newscategory
     *
     * @access public
     * @param Varien_Object $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    public function deleteChildren(Varien_Object $object)
    {
        $adapter = $this->_getWriteAdapter();
        $pathField = $adapter->quoteIdentifier('path');
        $select = $adapter->select()
            ->from($this->getMainTable(), array('entity_id'))
            ->where($pathField . ' LIKE :c_path');
        $childrenIds = $adapter->fetchCol($select, array('c_path' => $object->getPath() . '/%'));
        if (!empty($childrenIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('entity_id IN (?)' => $childrenIds)
            );
        }
        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    /**
     * Process newscategory data after save newscategory object
     *
     * @access protected
     * @param Varien_Object $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }


        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('medvslav_news2/newscategory_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'newscategory_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'newscategory_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @access protected
     * @param Medvslav_News2_Model_Newscategory $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
        }
        return $this;
    }

    /**
     * Get maximum position of child nescategories by specific tree path
     *
     * @access protected
     * @param string $path
     * @return int
     * @author Medvslav
     */
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level   = count(explode('/', $path));
        $bind = array(
            'c_level' => $level,
            'c_path'  => $path . '/%'
        );
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'MAX(' . $positionField . ')')
            ->where($adapter->quoteIdentifier('path') . ' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level') . ' = :c_level');

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    /**
     * Get children nescategories count
     *
     * @access public
     * @param int $newscategoryId
     * @return int
     * @author Medvslav
     */
    public function getChildrenCount($newscategoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'children_count')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $newscategoryId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check if newscategory id exist
     *
     * @access public
     * @param int $entityId
     * @return bool
     * @author Medvslav
     */
    public function checkId($entityId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = :entity_id');
        $bind =  array('entity_id' => $entityId);
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Check array of nescategories identifiers
     *
     * @access public
     * @param array $ids
     * @return array
     * @author Medvslav
     */
    public function verifyIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id IN(?)', $ids);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get count of active/not active children nescategories
     *
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @param bool $isActiveFlag
     * @return int
     * @author Medvslav
     */
    public function getChildrenAmount($newscategory, $isActiveFlag = true)
    {
        $bind = array(
            'active_flag'  => $isActiveFlag,
            'c_path'   => $newscategory->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), array('COUNT(m.entity_id)'))
            ->where('m.path LIKE :c_path')
            ->where('status' . ' = :active_flag');
        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    /**
     * Return parent nescategories of newscategory
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @return array
     * @author Medvslav
     */
    public function getParentNewscategories($newscategory)
    {
        $pathIds = array_reverse(explode('/', $newscategory->getPath()));
        $newscategories = Mage::getResourceModel('medvslav_news2/newscategory_collection')
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->load()
            ->getItems();
        return $newscategories;
    }

    /**
     * Return child nescategories
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function getChildrenNewscategories($newscategory)
    {
        $collection = $newscategory->getCollection();
        $collection
            ->addIdFilter($newscategory->getChildNewscategories())
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->load();
        return $collection;
    }
    /**
     * Return children ids of newscategory
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @param boolean $recursive
     * @return array
     * @author Medvslav
     */
    public function getChildren($newscategory, $recursive = true)
    {
        $bind = array(
            'c_path'   => $newscategory->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()
            ->from(array('m' => $this->getMainTable()), 'entity_id')
            ->where('status = ?', 1)
            ->where($this->_getReadAdapter()->quoteIdentifier('path') . ' LIKE :c_path');
        if (!$recursive) {
            $select->where($this->_getReadAdapter()->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $newscategory->getLevel() + 1;
        }
        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Process newscategory data before saving
     * prepare path and increment children count for parent nescategories
     *
     * @access protected
     * @param Varien_Object $object
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);
        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }
        if (!$object->getId() && !$object->getInitialSetupFlag()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');
            $toUpdateChild = explode('/', $object->getPath());
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('children_count'  => new Zend_Db_Expr('children_count+1')),
                array('entity_id IN(?)' => $toUpdateChild)
            );
        }
        return $this;
    }

    /**
     * Retrieve nescategories
     *
     * @access public
     * @param integer $parent
     * @param integer $recursionLevel
     * @param boolean|string $sorted
     * @param boolean $asCollection
     * @param boolean $toLoad
     * @return Varien_Data_Tree_Node_Collection|Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function getNewscategories(
        $parent,
        $recursionLevel = 0,
        $sorted = false,
        $asCollection = false,
        $toLoad = true
    )
    {
        $tree = Mage::getResourceModel('medvslav_news2/newscategory_tree');
        $nodes = $tree->loadNode($parent)
            ->loadChildren($recursionLevel)
            ->getChildren();
        $tree->addCollectionData(null, $sorted, $parent, $toLoad, true);
        if ($asCollection) {
            return $tree->getCollection();
        }
        return $nodes;
    }

    /**
     * Return all children ids of newscategory (with newscategory id)
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @return array
     * @author Medvslav
     */
    public function getAllChildren($newscategory)
    {
        $children = $this->getChildren($newscategory);
        $myId = array($newscategory->getId());
        $children = array_merge($myId, $children);
        return $children;
    }

    /**
     * Check newscategory is forbidden to delete.
     *
     * @access public
     * @param integer $newscategoryId
     * @return boolean
     * @author Medvslav
     */
    public function isForbiddenToDelete($newscategoryId)
    {
        return ($newscategoryId == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId());
    }

    /**
     * Get newscategory path value by its id
     *
     * @access public
     * @param int $newscategoryId
     * @return string
     * @author Medvslav
     */
    public function getNewscategoryPathById($newscategoryId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), array('path'))
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => (int)$newscategoryId);
        return $this->getReadConnection()->fetchOne($select, $bind);
    }

    /**
     * Move newscategory to another parent node
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @param Medvslav_News2_Model_Newscategory $newParent
     * @param null|int $afterNewscategoryId
     * @return Medvslav_News2_Model_Resource_Newscategory
     * @author Medvslav
     */
    public function changeParent(
        Medvslav_News2_Model_Newscategory $newscategory,
        Medvslav_News2_Model_Newscategory $newParent,
        $afterNewscategoryId = null
    )
    {
        $childrenCount  = $this->getChildrenCount($newscategory->getId()) + 1;
        $table          = $this->getMainTable();
        $adapter        = $this->_getWriteAdapter();
        $levelFiled     = $adapter->quoteIdentifier('level');
        $pathField      = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old newscategory parent nescategories
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)),
            array('entity_id IN(?)' => $newscategory->getParentIds())
        );
        /**
         * Increase children count for new newscategory parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($newscategory, $newParent, $afterNewscategoryId);

        $newPath  = sprintf('%s/%s', $newParent->getPath(), $newscategory->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $newscategory->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ','.
                    $adapter->quote($newscategory->getPath() . '/'). ', '.$adapter->quote($newPath . '/').')'
                ),
                'level' => new Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $newscategory->getPath() . '/%')
        );
        /**
         * Update moved newscategory data
         */
        $data = array(
            'path'  => $newPath,
            'level' => $newLevel,
            'position'  =>$position,
            'parent_id' =>$newParent->getId()
        );
        $adapter->update($table, $data, array('entity_id = ?' => $newscategory->getId()));
        // Update newscategory object to new data
        $newscategory->addData($data);
        return $this;
    }

    /**
     * Process positions of old parent newscategory children and new parent newscategory children.
     * Get position for moved newscategory
     *
     * @access protected
     * @param Medvslav_News2_Model_Newscategory $newscategory
     * @param Medvslav_News2_Model_Newscategory $newParent
     * @param null|int $afterNewscategoryId
     * @return int
     * @author Medvslav
     */
    protected function _processPositions($newscategory, $newParent, $afterNewscategoryId)
    {
        $table  = $this->getMainTable();
        $adapter= $this->_getWriteAdapter();
        $positionField  = $adapter->quoteIdentifier('position');

        $bind = array(
            'position' => new Zend_Db_Expr($positionField . ' - 1')
        );
        $where = array(
            'parent_id = ?' => $newscategory->getParentId(),
            $positionField . ' > ?' => $newscategory->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterNewscategoryId) {
            $select = $adapter->select()
                ->from($table, 'position')
                ->where('entity_id = :entity_id');
            $position = $adapter->fetchOne($select, array('entity_id' => $afterNewscategoryId));
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } elseif ($afterNewscategoryId !== null) {
            $position = 0;
            $bind = array(
                'position' => new Zend_Db_Expr($positionField . ' + 1')
            );
            $where = array(
                'parent_id = ?' => $newParent->getId(),
                $positionField . ' > ?' => $position
            );
            $adapter->update($table, $bind, $where);
        } else {
            $select = $adapter->select()
                ->from($table, array('position' => new Zend_Db_Expr('MIN(' . $positionField. ')')))
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, array('parent_id' => $newParent->getId()));
        }
        $position += 1;
        return $position;
    }
}
