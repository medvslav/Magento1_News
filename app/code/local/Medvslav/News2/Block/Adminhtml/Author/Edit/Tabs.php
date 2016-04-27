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
 * Author admin edit tabs
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Author_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('author_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('medvslav_news2')->__('Author'));
    }

    /**
     * Before render html
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Author_Edit_Tabs
     * @author Medvslav
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_author',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Author'),
                'title'   => Mage::helper('medvslav_news2')->__('Author'),
                'content' => $this->getLayout()->createBlock(
                    'medvslav_news2/adminhtml_author_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_author',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Meta'),
                'title'   => Mage::helper('medvslav_news2')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'medvslav_news2/adminhtml_author_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_author',
                array(
                    'label'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'title'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'medvslav_news2/adminhtml_author_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve author entity
     *
     * @access public
     * @return Medvslav_News2_Model_Author
     * @author Medvslav
     */
    public function getAuthor()
    {
        return Mage::registry('current_author');
    }
}
