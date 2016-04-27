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
 * Article category model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Article_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Medvslav
     */
    protected function _construct()
    {
        $this->_init('medvslav_news2/article_category');
    }

    /**
     * Save data for article-category relation
     *
     * @access public
     * @param  Medvslav_News2_Model_Article $article
     * @return Medvslav_News2_Model_Article_Category
     * @author Medvslav
     */
    public function saveArticleRelation($article)
    {
        $data = $article->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveArticleRelation($article, $data);
        }
        return $this;
    }

    /**
     * Get categories for article
     *
     * @access public
     * @param Medvslav_News2_Model_Article $article
     * @return Medvslav_News2_Model_Resource_Article_Category_Collection
     * @author Medvslav
     */
    public function getCategoryCollection($article)
    {
        $collection = Mage::getResourceModel('medvslav_news2/article_category_collection')
            ->addArticleFilter($article);
        return $collection;
    }
}
