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
 * Admin search model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Adminhtml_Search_Article extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Medvslav_News2_Model_Adminhtml_Search_Article
     * @author Medvslav
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('medvslav_news2/article_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $article) {
            $arr[] = array(
                'id'          => 'article/1/'.$article->getId(),
                'type'        => Mage::helper('medvslav_news2')->__('Article'),
                'name'        => $article->getTitle(),
                'description' => $article->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/news2_article/edit',
                    array('id'=>$article->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
