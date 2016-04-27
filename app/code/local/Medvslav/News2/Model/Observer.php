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
 * Frontend observer
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Observer
{
    /**
     * Add items to main menu
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return array()
     * @author Medvslav
     */
    public function addItemsToTopmenuItems($observer)
    {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        $newscategoryNodeId = 'newscategory';
        $data = array(
            'name' => Mage::helper('medvslav_news2')->__('Nescategories'),
            'id' => $newscategoryNodeId,
            'url' => Mage::helper('medvslav_news2/newscategory')->getNewscategoriesUrl(),
            'is_active' => ($action == 'medvslav_news2_newscategory_index' || $action == 'medvslav_news2_newscategory_view')
        );
        $newscategoryNode = new Varien_Data_Tree_Node($data, 'id', $tree, $menu);
        $menu->addChild($newscategoryNode);
        return $this;
    }
}
