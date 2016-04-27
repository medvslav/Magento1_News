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
 * meta information tab
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Edit_Tab_Meta
     * @author Medvslav
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('article');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'article_meta_form',
            array('legend' => Mage::helper('medvslav_news2')->__('Meta information'))
        );
        $fieldset->addField(
            'meta_title',
            'text',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Meta-title'),
                'name'  => 'meta_title',
            )
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            array(
                'name'      => 'meta_description',
                'label'     => Mage::helper('medvslav_news2')->__('Meta-description'),
              )
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            array(
                'name'      => 'meta_keywords',
                'label'     => Mage::helper('medvslav_news2')->__('Meta-keywords'),
            )
        );
        $form->addValues(Mage::registry('current_article')->getData());
        return parent::_prepareForm();
    }
}
