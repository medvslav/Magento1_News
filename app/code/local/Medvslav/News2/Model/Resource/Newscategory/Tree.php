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
 * Newscategory tree resource model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Resource_Newscategory_Tree extends Varien_Data_Tree_Dbp
{
    const ID_FIELD        = 'entity_id';
    const PATH_FIELD      = 'path';
    const ORDER_FIELD     = 'order';
    const LEVEL_FIELD     = 'level';

    /**
     * Nescategories resource collection
     *
     * @var Medvslav_News2_Model_Resource_Newscategory_Collection
     */
    protected $_collection;
    protected $_storeId;

    /**
     * Inactive nescategories ids
     * @var array
     */

    protected $_inactiveNewscategoryIds  = null;

    /**
     * Initialize tree
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('medvslav_news2_write'),
            $resource->getTableName('medvslav_news2/newscategory'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD    => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD  => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD => 'level',
            )
        );
    }

    /**
     * Get nescategories collection
     *
     * @access public
     * @param boolean $sorted
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }
    /**
     * Set the collection
     *
     * @access public
     * @param Medvslav_News2_Model_Resource_Newscategory_Collection $collection
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }
    /**
     * Get the default collection
     *
     * @access protected
     * @param boolean $sorted
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = Mage::getModel('medvslav_news2/newscategory')->getCollection();
        if ($sorted) {
            if (is_string($sorted)) {
                $collection->setOrder($sorted);
            } else {
                $collection->setOrder('name');
            }
        }
        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @access public
     * @param unknown_type $newscategory
     * @param unknown_type $newParent
     * @param unknown_type $prevNode
     * @author Medvslav
     */
    public function move($newscategory, $newParent, $prevNode = null)
    {
        Mage::getResourceSingleton('medvslav_news2/newscategory')
            ->move($newscategory->getId(), $newParent->getId());
        parent::move($newscategory, $newParent, $prevNode);
        $this->_afterMove($newscategory, $newParent, $prevNode);
    }

    /**
     * Move tree after
     *
     * @access protected
     * @param unknown_type $newscategory
     * @param Varien_Data_Tree_Node $newParent
     * @param Varien_Data_Tree_Node $prevNode
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     */
    protected function _afterMove($newscategory, $newParent, $prevNode)
    {
        Mage::app()->cleanCache(array(Medvslav_News2_Model_Newscategory::CACHE_TAG));
        return $this;
    }

    /**
     * Load whole newscategory tree, that will include specified nescategories ids.
     *
     * @access public
     * @param array $ids
     * @param bool $addCollectionData
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField  = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where($levelField . ' <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }
        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, array('path', 'level'))
            ->where('entity_id IN (?)', $ids);
        $where = array($levelField . '=0' => true);

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds  = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["$levelField=$level AND $pathField LIKE '$path'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . Varien_Db_Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);
        return $this;
    }

    /**
     * Load array of newscategory parents
     *
     * @access public
     * @param string $path
     * @param bool $addCollectionData
     * @param bool $withRootNode
     * @return array
     * @author Medvslav
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = array();
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select
                ->where('main_table.entity_id IN(?)', $pathIds)
                ->order($this->_conn->getLengthSql('main_table.path') . ' ' . Varien_Db_Select::SQL_ASC);
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }

    /**
     * Obtain select for nescategories
     * By default everything from entity table is selected
     * + name
     *
     * @access public
     * @param bool $sorted
     * @param array $optionalAttributes
     * @return Zend_Db_Select
     * @author Medvslav
     */
    protected function _createCollectionDataSelect($sorted = true)
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)->getSelect();
        $newscategoriesTable = Mage::getSingleton('core/resource')
            ->getTableName('medvslav_news2/newscategory');
        $subConcat = $this->_conn->getConcatSql(array('main_table.path', $this->_conn->quote('/%')));
        $subSelect = $this->_conn->select()
            ->from(array('see' => $newscategoriesTable), null)
            ->where('see.entity_id = main_table.entity_id')
            ->orWhere('see.path LIKE ?', $subConcat);
        return $select;
    }

    /**
     * Get real existing newscategory ids by specified ids
     *
     * @access public
     * @param array $ids
     * @return array
     * @author Medvslav
     */
    public function getExistingNewscategoryIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $select = $this->_conn->select()
            ->from($this->_table, array('entity_id'))
            ->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }

    /**
     * Add collection data
     *
     * @access public
     * @param Medvslav_News2_Model_Resource_Newscategory_Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    public function addCollectionData(
        $collection = null,
        $sorted = false,
        $exclude = array(),
        $toLoad = true,
        $onlyActive = false
    )
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }
        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addFieldToFilter('status', 1);
        }
        if ($toLoad) {
            $collection->load();
            foreach ($collection as $newscategory) {
                if ($this->getNodeById($newscategory->getId())) {
                    $this->getNodeById($newscategory->getId())->addData($newscategory->getData());
                }
            }
            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }
        return $this;
    }

    /**
     * Add inactive nescategories ids
     *
     * @access public
     * @param unknown_type $ids
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    public function addInactiveNewscategoryIds($ids)
    {
        if (!is_array($this->_inactiveNewscategoryIds)) {
            $this->_initInactiveNewscategoryIds();
        }
        $this->_inactiveNewscategoryIds = array_merge($ids, $this->_inactiveNewscategoryIds);
        return $this;
    }

    /**
     * Retrieve inactive nescategories ids
     *
     * @access protected
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    protected function _initInactiveNewscategoryIds()
    {
        $this->_inactiveNewscategoryIds = array();
        return $this;
    }
    /**
     * Retrieve inactive nescategories ids
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getInactiveNewscategoryIds()
    {
        if (!is_array($this->_inactiveNewscategoryIds)) {
            $this->_initInactiveNewscategoryIds();
        }
        return $this->_inactiveNewscategoryIds;
    }

    /**
     * Return disable newscategory ids
     *
     * @access protected
     * @param Medvslav_News2_Model_Resource_Newscategory_Collection $collection
     * @return array
     * @author Medvslav
     */
    protected function _getDisabledIds($collection)
    {
        $this->_inactiveItems = $this->getInactiveNewscategoryIds();
        $this->_inactiveItems = array_merge(
            $this->_getInactiveItemIds($collection),
            $this->_inactiveItems
        );
        $allIds = $collection->getAllIds();
        $disabledIds = array();

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId())) {
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    /**
     * Retrieve inactive newscategory item ids
     *
     * @access protecte
     * @param Medvslav_News2_Model_Resource_Newscategory_Collection $collection
     * @return array
     * @author Medvslav
     */
    protected function _getInactiveItemIds($collection)
    {
        $filter = $collection->getAllIdsSql();
        $table = Mage::getSingleton('core/resource')->getTable('medvslav_news2/newscategory');
        $bind = array(
            'cond' => 0,
        );
        $select = $this->_conn->select()
            ->from(array('d'=>$table), array('d.entity_id'))
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->where('status = :cond');
        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Check is newscategory items active
     *
     * @access protecte
     * @param int $id
     * @return boolean
     * @author Medvslav
     */
    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveItems)) {
            return true;
        }
        return false;
    }
}