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
 * Article admin edit form
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'medvslav_news2';
        $this->_controller = 'adminhtml_article';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('medvslav_news2')->__('Save Article')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('medvslav_news2')->__('Delete Article')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('medvslav_news2')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get the edit form header
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_article') && Mage::registry('current_article')->getId()) {
            return Mage::helper('medvslav_news2')->__(
                "Edit Article '%s'",
                $this->escapeHtml(Mage::registry('current_article')->getTitle())
            );
        } else {
            return Mage::helper('medvslav_news2')->__('Add Article');
        }
    }
}
