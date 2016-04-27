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
 * Article - product relation resource model collection
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Resource_Article_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * Join the link table
     *
     * @access public
     * @return Medvslav_News2_Model_Resource_Article_Product_Collection
     * @author Medvslav
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('medvslav_news2/article_product')),
                'related.product_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * Add article filter
     *
     * @access public
     * @param Medvslav_News2_Model_Article | int $article
     * @return Medvslav_News2_Model_Resource_Article_Product_Collection
     * @author Medvslav
     */
    public function addArticleFilter($article)
    {
        if ($article instanceof Medvslav_News2_Model_Article) {
            $article = $article->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.article_id = ?', $article);
        return $this;
    }
}
