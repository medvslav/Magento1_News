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
 * Article category list
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Article_Catalog_Category_List extends Mage_Core_Block_Template
{
    /**
     * Get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Category_Collection
     * @author Medvslav
     */
    public function getCategoryCollection()
    {
        $collection = $this->getArticle()->getSelectedCategoriesCollection();
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->order('related.position');
        $collection->addAttributeToFilter('is_active', 1);
        return $collection;
    }

    /**
     * Get current article
     *
     * @access public
     * @return Medvslav_News2_Model_Article
     * @author Medvslav
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }
}
