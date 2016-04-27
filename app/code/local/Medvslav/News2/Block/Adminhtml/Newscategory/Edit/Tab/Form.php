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
 * Newscategory edit form tab
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Tab_Form
     * @author Medvslav
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('newscategory_');
        $form->setFieldNameSuffix('newscategory');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'newscategory_form',
            array('legend' => Mage::helper('medvslav_news2')->__('Newscategory'))
        );
        $fieldset->addType(
            'editor',
            Mage::getConfig()->getBlockClassName('medvslav_news2/adminhtml_helper_wysiwyg')
        );
        if (!$this->getNewscategory()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
            }
            $fieldset->addField(
                'path',
                'hidden',
                array(
                    'name'  => 'path',
                    'value' => $parentId
                )
            );
        } else {
            $fieldset->addField(
                'id',
                'hidden',
                array(
                    'name'  => 'id',
                    'value' => $this->getNewscategory()->getId()
                )
            );
            $fieldset->addField(
                'path',
                'hidden',
                array(
                    'name'  => 'path',
                    'value' => $this->getNewscategory()->getPath()
                )
            );
        }

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
            Mage::registry('current_newscategory')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $form->addValues($this->getNewscategory()->getData());
        return parent::_prepareForm();
    }

    /**
     * Get the current newscategory
     *
     * @access public
     * @return Medvslav_News2_Model_Newscategory
     */
    public function getNewscategory()
    {
        return Mage::registry('newscategory');
    }
}
