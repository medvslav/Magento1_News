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
 * News2 textarea attribute WYSIWYG button
 * @category   Medvslav
 * @package    Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Helper_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        $disabled = ($this->getDisabled() || $this->getReadonly());
        $html .= Mage::getSingleton('core/layout')
            ->createBlock(
                'adminhtml/widget_button',
                '',
                array(
                    'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
                    'type'=> 'button',
                    'disabled' => $disabled,
                    'class' => ($disabled) ? 'disabled btn-wysiwyg' : 'btn-wysiwyg',
                    'onclick' => 'catalogWysiwygEditor.open(\''.
                        Mage::helper('adminhtml')->getUrl('*/*/wysiwyg').'\', \''.
                        $this->getHtmlId().'\')'
                )
            )
            ->toHtml();
        return $html;
    }
}
