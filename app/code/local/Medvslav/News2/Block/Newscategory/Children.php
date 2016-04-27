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
 * Newscategory children list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Newscategory_Children extends Medvslav_News2_Block_Newscategory_List
{
    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Newscategory_Children
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        $this->getNewscategories()->addFieldToFilter('parent_id', $this->getCurrentNewscategory()->getId());
        return $this;
    }

    /**
     * Get the current newscategory
     *
     * @access protected
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function getCurrentNewscategory()
    {
        return Mage::registry('current_newscategory');
    }
}
