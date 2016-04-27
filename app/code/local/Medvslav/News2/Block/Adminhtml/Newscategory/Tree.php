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
 * Newscategory admin tree block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Tree extends Medvslav_News2_Block_Adminhtml_Newscategory_Abstract
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('medvslav_news2/newscategory/tree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = true;
    }

    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Tree
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl(
            "*/*/add",
            array(
                '_current'=>true,
                'id'=>null,
                '_query' => false
            )
        );

        $this->setChild(
            'add_sub_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('medvslav_news2')->__('Add Child Newscategory'),
                        'onclick' => "addNew('".$addUrl."', false)",
                        'class'   => 'add',
                        'id'      => 'add_child_newscategory_button',
                        'style'   => $this->canAddChild() ? '' : 'display: none;'
                    )
                )
        );

        $this->setChild(
            'add_root_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('medvslav_news2')->__('Add Root Newscategory'),
                        'onclick' => "addNew('".$addUrl."', true)",
                        'class'   => 'add',
                        'id'      => 'add_root_newscategory_button'
                    )
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * get the newscategory collection
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Newscategory_Collection
     * @author Medvslav
     */
    public function getNewscategoryCollection()
    {
        $collection = $this->getData('newscategory_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('medvslav_news2/newscategory')->getCollection();
            $this->setData('newscategory_collection', $collection);
        }
        return $collection;
    }

    /**
     * Get html for add root button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * Get html for add child button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * Get html for expand button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * Get html for add collapse button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * Get url for tree load
     *
     * @access public
     * @param mxed $expanded
     * @return string
     * @author Medvslav
     */
    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        if ((is_null($expanded) &&
            Mage::getSingleton('admin/session')->getNewscategoryIsTreeWasExpanded()) ||
            $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/newscategoriesJson', $params);
    }

    /**
     * Get url for loading nodes
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/news2_newscategories/jsonTree');
    }

    /**
     * Check if tree is expanded
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getNewscategoryIsTreeWasExpanded();
    }

    /**
     * Get url for moving newscategory
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/news2_newscategory/move');
    }

    /**
     * Get the tree as json
     *
     * @access public
     * @param mixed $parentNodeNewscategory
     * @return string
     * @author Medvslav
     */
    public function getTree($parentNodeNewscategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeNewscategory));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * Get the tree as json
     *
     * @access public
     * @param mixed $parentNodeNewscategory
     * @return string
     * @author Medvslav
     */
    public function getTreeJson($parentNodeNewscategory = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeNewscategory));
        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of nescategories, that are breadcrumbs for specified newscategory path
     *
     * @access public
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     * @author Medvslav
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $newscategories = Mage::getResourceSingleton('medvslav_news2/newscategory_tree')
            ->loadBreadcrumbsArray($path);
        if (empty($newscategories)) {
            return '';
        }
        foreach ($newscategories as $key => $newscategory) {
            $newscategories[$key] = $this->_getNodeJson($newscategory);
        }
        return
            '<script type="text/javascript">'
            . $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($newscategories) . ';'
            . ($this->canAddChild() ? '$("add_child_newscategory_button").show();' : '$("add_child_newscategory_button").hide();')
            . '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string
     * @author Medvslav
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }
        $item = array();
        $item['text'] = $this->buildNodeName($node);
        $item['id']   = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls']  = 'folder';
        if ($node->getStatus()) {
            $item['cls'] .= ' active-category';
        } else {
            $item['cls'] .= ' no-active-category';
        }
        $item['allowDrop'] = true;
        $item['allowDrag'] = true;
        if ((int)$node->getChildrenCount()>0) {
            $item['children'] = array();
        }
        $isParent = $this->_isParentSelectedNewscategory($node);
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }
        if ($isParent || $node->getLevel() < 1) {
            $item['expanded'] = true;
        }
        return $item;
    }

    /**
     * Get node label
     *
     * @access public
     * @param Varien_Object $node
     * @return string
     * @author Medvslav
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        return $result;
    }

    /**
     * Check if entity is movable
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool
     * @author Medvslav
     */
    protected function _isNewscategoryMoveable($node)
    {
        return true;
    }

    /**
     * Check if parent is selected
     *
     * @access protected
     * @param Varien_Object $node
     * @return bool
     * @author Medvslav
     */
    protected function _isParentSelectedNewscategory($node)
    {
        if ($node && $this->getNewscategory()) {
            $pathIds = $this->getNewscategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if page loaded by outside link to newscategory edit
     *
     * @access public
     * @return boolean
     * @author Medvslav
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root newscategory
     *
     * @access public
     * @return boolean
     * @author Medvslav
     */
    public function canAddRootNewscategory()
    {
        return true;
    }

    /**
     * Check availability of adding child newscategory
     *
     * @access public
     * @return boolean
     */
    public function canAddChild()
    {
        return true;
    }
}
