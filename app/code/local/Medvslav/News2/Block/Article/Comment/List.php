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
 * Article comment list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Article_Comment_List extends Mage_Core_Block_Template
{
    /**
     * Initialize
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $article = $this->getArticle();
        $comments = Mage::getResourceModel('medvslav_news2/article_comment_collection')
            ->addFieldToFilter('article_id', $article->getId())
            ->addStoreFilter(Mage::app()->getStore())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'asc');
        $this->setComments($comments);
    }

    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Article_Comment_List
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'medvslav_news2.article.html.pager'
        )
        ->setCollection($this->getComments());
        $this->setChild('pager', $pager);
        $this->getComments()->load();
        return $this;
    }

    /**
     * Get the pager html
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * Get the current article
     *
     * @access protected
     * @return Medvslav_News2_Model_Article
     * @author Medvslav
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }
}
