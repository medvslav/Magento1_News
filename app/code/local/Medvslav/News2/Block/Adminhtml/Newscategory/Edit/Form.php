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
 * Newscategory edit form
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Form extends Medvslav_News2_Block_Adminhtml_Newscategory_Abstract
{
    /**
     * Additional buttons on newscategory page
     * @var array
     */
    protected $_additionalButtons = array();
    /**
     * constructor
     *
     * set template
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('medvslav_news2/newscategory/edit/form.phtml');
    }

    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Form
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        $newscategory = $this->getNewscategory();
        $newscategoryId = (int)$newscategory->getId();
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('medvslav_news2/adminhtml_newscategory_edit_tabs', 'tabs')
        );
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('medvslav_news2')->__('Save Newscategory'),
                        'onclick' => "newscategorySubmit('" . $this->getSaveUrl() . "', true)",
                        'class'   => 'save'
                    )
                )
        );
        // Delete button
        if (!in_array($newscategoryId, $this->getRootIds())) {
            $this->setChild(
                'delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(
                        array(
                            'label'   => Mage::helper('medvslav_news2')->__('Delete Newscategory'),
                            'onclick' => "newscategoryDelete('" . $this->getUrl(
                                '*/*/delete',
                                array('_current' => true)
                            )
                            . "', true, {$newscategoryId})",
                            'class'   => 'delete'
                        )
                    )
            );
        }

        // Reset button
        $resetPath = $newscategory ? '*/*/edit' : '*/*/add';
        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Reset'),
                        'onclick'   => "newscategoryReset('".$this->getUrl(
                            $resetPath,
                            array('_current'=>true)
                        )
                        ."',true)"
                    )
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * Get html for delete button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Get html for save button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Get html for reset button
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve additional buttons html
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Form
     * @author Medvslav
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild(
            $alias . '_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config)
        );
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @access public
     * @param string $alias
     * @return Medvslav_News2_Block_Adminhtml_Newscategory_Edit_Form
     * @author Medvslav
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }
        return $this;
    }

    /**
     * Get html for tabs
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * Get the form header
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getHeader()
    {
        if ($this->getNewscategoryId()) {
            return $this->getNewscategoryName();
        } else {
            return Mage::helper('medvslav_news2')->__('New Root Newscategory');
        }
    }

    /**
     * Get the delete url
     *
     * @access public
     * @param array $args
     * @return string
     * @author Medvslav
     */
    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @access public
     * @param array $args
     * @return string
     * @author Medvslav
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    /**
     * Check if request is ajax
     *
     * @access public
     * @return bool
     * @author Medvslav
     */
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
