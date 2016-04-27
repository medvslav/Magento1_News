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
 * Newscategory admin edit tabs
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        $this->setId('newscategory_info_tabs');
        $this->setDestElementId('newscategory_tab_content');
        $this->setTitle(Mage::helper('medvslav_news2')->__('Newscategory'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    /**
     * Prepare Layout Content
     *
     * @access public
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'form_newscategory',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Newscategory'),
                'title'   => Mage::helper('medvslav_news2')->__('Newscategory'),
                'content' => $this->getLayout()->createBlock(
                    'medvslav_news2/adminhtml_newscategory_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_newscategory',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Meta'),
                'title'   => Mage::helper('medvslav_news2')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'medvslav_news2/adminhtml_newscategory_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_newscategory',
                array(
                    'label'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'title'   => Mage::helper('medvslav_news2')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'medvslav_news2/adminhtml_newscategory_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve newscategory entity
     *
     * @access public
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function getNewscategory()
    {
        return Mage::registry('current_newscategory');
    }
}
