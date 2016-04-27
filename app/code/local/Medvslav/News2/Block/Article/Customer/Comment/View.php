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
 * Article customer comments list
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Article_Customer_Comment_View extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Get current comment
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Comment
     * @author Medvslav
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }

    /**
     * Get current article
     *
     * @access public
     * @return Medvslav_News2_Model_Article
     * @author Medvslav
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }
}
