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
 * Author edit form tab
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Author_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Author_Edit_Tab_Form
     * @author Medvslav
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('author_');
        $form->setFieldNameSuffix('author');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'author_form',
            array('legend' => Mage::helper('medvslav_news2')->__('Author'))
        );
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Description'),
                'name'  => 'description',
            'config' => $wysiwygConfig,
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Email'),
                'name'  => 'email',
            'required'  => true,
            'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('medvslav_news2')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('medvslav_news2')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('medvslav_news2')->__('Disabled'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_author')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_author')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getAuthorData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getAuthorData());
            Mage::getSingleton('adminhtml/session')->setAuthorData(null);
        } elseif (Mage::registry('current_author')) {
            $formValues = array_merge($formValues, Mage::registry('current_author')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
