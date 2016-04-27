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
 * Article comment admin edit tabs
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('article_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('medvslav_news2')->__('Article Comment'));
    }

    /**
     * Before render html
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Edit_Tabs
     * @author Medvslav
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_article_comment',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Article comment'),
                'title'   => Mage::helper('medvslav_news2')->__('Article comment'),
                'content' => $this->getLayout()->createBlock(
                    'medvslav_news2/adminhtml_article_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_article_comment',
                array(
                    'label'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'title'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'medvslav_news2/adminhtml_article_comment_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Comment
     * @author Medvslav
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
