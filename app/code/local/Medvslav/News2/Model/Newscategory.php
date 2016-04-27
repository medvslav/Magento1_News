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
 * Newscategory model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Newscategory extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'medvslav_news2_newscategory';
    const CACHE_TAG = 'medvslav_news2_newscategory';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'medvslav_news2_newscategory';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'newscategory';

    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('medvslav_news2/newscategory');
    }

    /**
     * Before save newscategory
     *
     * @access protected
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * Get the url to the newscategory details page
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getNewscategoryUrl()
    {
        return Mage::getUrl('medvslav_news2/newscategory/view', array('id'=>$this->getId()));
    }

    /**
     * Get the newscategory Description
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getDescription()
    {
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }

    /**
     * Save newscategory relation
     *
     * @access public
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Collection
     * @author Medvslav
     */
    public function getSelectedArticlesCollection()
    {
        if (!$this->hasData('_article_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('medvslav_news2/article_collection')
                        ->addFieldToFilter('newscategory_id', $this->getId());
                $this->setData('_article_collection', $collection);
            }
        }
        return $this->getData('_article_collection');
    }

    /**
     * Get the tree model
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('medvslav_news2/newscategory_tree');
    }

    /**
     * Get tree model instance
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Newscategory_Tree
     * @author Medvslav
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('medvslav_news2/newscategory_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move newscategory
     *
     * @access public
     * @param   int $parentId new parent newscategory id
     * @param   int $afterNewscategoryId newscategory id after which we have put current newscategory
     * @return  Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function move($parentId, $afterNewscategoryId)
    {
        $parent = Mage::getModel('medvslav_news2/newscategory')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('medvslav_news2')->__(
                    'Newscategory move operation is not possible: the new parent newscategory was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('medvslav_news2')->__(
                    'Newscategory move operation is not possible: the current newscategory was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('medvslav_news2')->__(
                    'Newscategory move operation is not possible: parent newscategory is equal to child newscategory.'
                )
            );
        }
        $this->setMovedNewscategoryId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'newscategory_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterNewscategoryId);
            $this->_getResource()->commit();
            $this->setAffectedNewscategoryIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Get the parent newscategory
     *
     * @access public
     * @return  Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function getParentNewscategory()
    {
        if (!$this->hasData('parent_newscategory')) {
            $this->setData(
                'parent_newscategory',
                Mage::getModel('medvslav_news2/newscategory')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_newscategory');
    }

    /**
     * Get the parent id
     *
     * @access public
     * @return  int
     * @author Medvslav
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent nescategories ids
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all nescategories children
     *
     * @access public
     * @param bool $asArray
     * @return mixed (array|string)
     * @author Medvslav
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all nescategories children
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getChildNewscategories()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * Check the id
     *
     * @access public
     * @param int $id
     * @return bool
     * @author Medvslav
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array nescategories ids which are part of newscategory path
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @access public
     * @return int
     * @author Medvslav
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify newscategory ids
     *
     * @access public
     * @param array $ids
     * @return bool
     * @author Medvslav
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * Check if newscategory has children
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Check if newscategory can be deleted
     *
     * @access protected
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('medvslav_news2')->__("Can't delete root newscategory."));
        }
        return parent::_beforeDelete();
    }

    /**
     * Get the nescategories
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @author Medvslav
     */
    public function getNewscategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getNewscategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent nescategories of current newscategory
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getParentNewscategories()
    {
        return $this->getResource()->getParentNewscategories($this);
    }

    /**
     * Return children nescategories of current newscategory
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getChildrenNewscategories()
    {
        return $this->getResource()->getChildrenNewscategories($this);
    }

    /**
     * Check if parents are enabled
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function getStatusPath()
    {
        $parents = $this->getParentNewscategories();
        $rootId = Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $this->getStatus();
    }

    /**
     * Get default values
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
