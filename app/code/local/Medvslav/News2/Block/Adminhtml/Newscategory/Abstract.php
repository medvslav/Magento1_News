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
 * Newscategory admin block abstract
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Get current newscategory
     *
     * @access public
     * @return Medvslav_News2_Model_Entity
     * @author Medvslav
     */
    public function getNewscategory()
    {
        return Mage::registry('newscategory');
    }

    /**
     * Get current newscategory id
     *
     * @access public
     * @return int
     * @author Medvslav
     */
    public function getNewscategoryId()
    {
        if ($this->getNewscategory()) {
            return $this->getNewscategory()->getId();
        }
        return null;
    }

    /**
     * Get current newscategory Name
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getNewscategoryName()
    {
        return $this->getNewscategory()->getName();
    }

    /**
     * Get current newscategory path
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getNewscategoryPath()
    {
        if ($this->getNewscategory()) {
            return $this->getNewscategory()->getPath();
        }
        return Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
    }

    /**
     * Check if there is a root newscategory
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function hasRootNewscategory()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Get the root
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory|null $parentNodeNewscategory
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Medvslav
     */
    public function getRoot($parentNodeNewscategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeNewscategory) && $parentNodeNewscategory->getId()) {
            return $this->getNode($parentNodeNewscategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
            $tree = Mage::getResourceSingleton('medvslav_news2/newscategory_tree')
                ->load(null, $recursionLevel);
            if ($this->getNewscategory()) {
                $tree->loadEnsuredNodes($this->getNewscategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getNewscategoryCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                $root->setName(Mage::helper('medvslav_news2')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register nescategories root by specified nescategories IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @author Medvslav
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $newscategoryTreeResource = Mage::getResourceSingleton('medvslav_news2/newscategory_tree');
            $ids     = $newscategoryTreeResource->getExistingNewscategoryIdsBySpecifiedIds($ids);
            $tree   = $newscategoryTreeResource->loadByIds($ids);
            $rootId = Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
                $root->setName(Mage::helper('medvslav_news2')->__('Root'));
            }
            $tree->addCollectionData($this->getNewscategoryCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get specific node
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory $parentNodeNewscategory
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Medvslav
     */
    public function getNode($parentNodeNewscategory, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('medvslav_news2/newscategory_tree');
        $nodeId     = $parentNodeNewscategory->getId();
        $parentId   = $parentNodeNewscategory->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId()) {
            $node->setName(Mage::helper('medvslav_news2')->__('Root'));
        }
        $tree->addCollectionData($this->getNewscategoryCollection());
        return $node;
    }

    /**
     * Get url for saving data
     *
     * @access public
     * @param array $args
     * @return string
     * @author Medvslav
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * Get url for edit
     *
     * @access public
     * @param array $args
     * @return string
     * @author Medvslav
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/news2_newscategory/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getRootIds()
    {
        return array(Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId());
    }
}
