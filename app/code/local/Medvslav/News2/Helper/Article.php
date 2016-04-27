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
 * Article helper
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Helper_Article extends Mage_Core_Helper_Abstract
{

    /**
     * Get the url to the articles list page
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getArticlesUrl()
    {
        if ($listKey = Mage::getStoreConfig('medvslav_news2/article/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('medvslav_news2/article/index');
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
        return Mage::getStoreConfigFlag('medvslav_news2/article/breadcrumbs');
    }

    /**
     * Check if the rss for article is enabled
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('medvslav_news2/article/rss');
    }

    /**
     * Get the link to the article rss list
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getRssUrl()
    {
        return Mage::getUrl('medvslav_news2/article/rss');
    }
}
