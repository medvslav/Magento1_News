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
 * Newscategory helper
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Helper_Newscategory extends Mage_Core_Helper_Abstract
{

    /**
     * Get the url to the nescategories list page
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getNewscategoriesUrl()
    {
        if ($listKey = Mage::getStoreConfig('medvslav_news2/newscategory/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('medvslav_news2/newscategory/index');
    }

    /**
     * Check if breadcrumbs can be used
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('medvslav_news2/newscategory/breadcrumbs');
    }
    const NEWSCATEGORY_ROOT_ID = 1;
    /**
     * Get the root id
     *
     * @access public
     * @return int
     * @author Medvslav
     */
    public function getRootNewscategoryId()
    {
        return self::NEWSCATEGORY_ROOT_ID;
    }
}
