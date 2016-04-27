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
 * Category helper
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Helper_Category extends Medvslav_News2_Helper_Data
{

    /**
     * Get the selected articles for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Medvslav
     */
    public function getSelectedArticles(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection($category) as $article) {
                $articles[] = $article;
            }
            $category->setSelectedArticles($articles);
        }
        return $category->getData('selected_articles');
    }

    /**
     * Get article collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Medvslav_News2_Model_Resource_Article_Collection
     * @author Medvslav
     */
    public function getSelectedArticlesCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('medvslav_news2/article_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
