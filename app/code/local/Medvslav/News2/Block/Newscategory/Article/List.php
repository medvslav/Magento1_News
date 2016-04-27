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
 * Newscategory Articles list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Newscategory_Article_List extends Medvslav_News2_Block_Article_List
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
        $newscategory = $this->getNewscategory();
        if ($newscategory) {
            $this->getArticles()->addFieldToFilter('newscategory_id', $newscategory->getId());
        }
    }

    /**
     * Prepare the layout - actually do nothing
     *
     * @access protected
     * @return Medvslav_News2_Block_Newscategory_Article_List
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Get the current newscategory
     *
     * @access public
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function getNewscategory()
    {
        return Mage::registry('current_newscategory');
    }
}
