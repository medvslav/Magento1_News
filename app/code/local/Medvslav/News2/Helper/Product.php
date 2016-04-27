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
 * Product helper
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Helper_Product extends Medvslav_News2_Helper_Data
{

    /**
     * Get the selected articles for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     * @author Medvslav
     */
    public function getSelectedArticles(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection($product) as $article) {
                $articles[] = $article;
            }
            $product->setSelectedArticles($articles);
        }
        return $product->getData('selected_articles');
    }

    /**
     * Get article collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Medvslav_News2_Model_Resource_Article_Collection
     * @author Medvslav
     */
    public function getSelectedArticlesCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('medvslav_news2/article_collection')
            ->addProductFilter($product);
        return $collection;
    }
}
