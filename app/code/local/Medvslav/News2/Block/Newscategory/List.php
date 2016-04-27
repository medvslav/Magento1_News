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
 * Newscategory list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Newscategory_List extends Mage_Core_Block_Template
{
    /**
     * Initialize
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $newscategories = Mage::getResourceModel('medvslav_news2/newscategory_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        ;
        $newscategories->getSelect()->order('main_table.position');
        $this->setNewscategories($newscategories);
    }

    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Newscategory_List
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getNewscategories()->addFieldToFilter('level', 1);
        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock(
                'page/html_pager',
                'medvslav_news2.newscategories.html.pager'
            )
            ->setCollection($this->getNewscategories());
            $this->setChild('pager', $pager);
            $this->getNewscategories()->load();
        }
        return $this;
    }

    /**
     * Get the pager html
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get the display mode
     *
     * @access protected
     * @return int
     * @author Medvslav
     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('medvslav_news2/newscategory/tree');
    }

    /**
     * Draw newscategory
     *
     * @access public
     * @param Medvslav_News2_Model_Newscategory
     * @param int $level
     * @return int
     * @author Medvslav
     */
    public function drawNewscategory($newscategory, $level = 0)
    {
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        $storeIds = Mage::getResourceSingleton(
            'medvslav_news2/newscategory'
        )
        ->lookupStoreIds($newscategory->getId());
        $validStoreIds = array(0, Mage::app()->getStore()->getId());
        if (!array_intersect($storeIds, $validStoreIds)) {
            return '';
        }
        if (!$newscategory->getStatus()) {
            return '';
        }
        $children = $newscategory->getChildrenNewscategories();
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1) {
            foreach ($children as $child) {
                $childStoreIds = Mage::getResourceSingleton(
                    'medvslav_news2/newscategory'
                )
                ->lookupStoreIds($child->getId());
                $validStoreIds = array(0, Mage::app()->getStore()->getId());
                if (!array_intersect($childStoreIds, $validStoreIds)) {
                    continue;
                }
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.$newscategory->getNewscategoryUrl().'">'.$newscategory->getName().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawNewscategory($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * Get recursion
     *
     * @access public
     * @return int
     * @author Medvslav
     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('medvslav_news2/newscategory/recursion'));
        }
        return $this->getData('recursion');
    }
}
