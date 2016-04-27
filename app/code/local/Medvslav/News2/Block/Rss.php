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
 * News2 RSS block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Rss extends Mage_Core_Block_Template
{
    /**
     * RSS feeds for this block
     */
    protected $_feeds = array();

    /**
     * Add a new feed
     *
     * @access public
     * @param string $label
     * @param string $url
     * @param bool $prepare
     * @return Medvslav_News2_Block_Rss
     * @author Medvslav
     */
    public function addFeed($label, $url, $prepare = false)
    {
        $link = ($prepare ? $this->getUrl($url) : $url);
        $feed = new Varien_Object();
        $feed->setLabel($label);
        $feed->setUrl($link);
        $this->_feeds[$link] = $feed;
        return $this;
    }

    /**
     * Get the current feeds
     *
     * @access public
     * @return array()
     * @author Medvslav
     */
    public function getFeeds()
    {
        return $this->_feeds;
    }
}
