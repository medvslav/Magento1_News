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
 * Article view block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Article_View extends Mage_Core_Block_Template
{
    /**
     * Get the current article
     *
     * @access public
     * @return mixed (Medvslav_News2_Model_Article|null)
     * @author Medvslav
     */
    public function getCurrentArticle()
    {
        return Mage::registry('current_article');
    }
}
