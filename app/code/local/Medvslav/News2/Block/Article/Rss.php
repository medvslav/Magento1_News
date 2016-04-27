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
 * Article RSS block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Article_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_news2_article_rss';

    /**
     * Constructor
     *
     * @access protected
     * @return void
     * @author Medvslav
     */
    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('medvslav_news2_article_rss');
        $this->setCacheLifetime(600);
    }

    /**
     * Method toHtml 
     *
     * @access protected
     * @return string
     * @author Medvslav
     */
    protected function _toHtml()
    {
        $url    = Mage::helper('medvslav_news2/article')->getArticlesUrl();
        $title  = Mage::helper('medvslav_news2')->__('Articles');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('medvslav_news2/article')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            $description .= '<div>'.
                Mage::helper('medvslav_news2')->__('Title').': 
                '.$item->getTitle().
                '</div>';
            $description .= '<div>'.
                Mage::helper('medvslav_news2')->__('Description').': 
                '.$item->getDescription().
                '</div>';
            $description .= '<div>'.
                Mage::helper('medvslav_news2')->__('Content').': 
                '.$item->getContent().
                '</div>';
            $description .= '<div>'.Mage::helper('medvslav_news2')->__('Publication_date').': '.Mage::helper('core')->formatDate($item->getPublicationDate(), 'full').'</div>';
            if ($item->getImage()) {
                $description .= '<div>';
                $description .= Mage::helper('medvslav_news2')->__('Image');
                $description .= '<img src="'.Mage::helper('medvslav_news2/article_image')->init($item, 'image')->resize(75).'" alt="'.$this->escapeHtml($item->getTitle()).'" />';
                $description .= '</div>';
            }
            $description .= '</p>';
            $data = array(
                'title'       => $item->getTitle(),
                'link'        => $item->getArticleUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
