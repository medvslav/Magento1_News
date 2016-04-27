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
 * Article list on category page block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Catalog_Category_List_Article extends Mage_Core_Block_Template
{
    /**
     * Get the list of articles
     *
     * @access protected
     * @return Medvslav_News2_Model_Resource_Article_Collection
     * @author Medvslav
     */
    public function getArticleCollection()
    {
        if (!$this->hasData('article_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('medvslav_news2/article_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('article_collection', $collection);
        }
        return $this->getData('article_collection');
    }
}
