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
 * Article comment edit form tab
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Comment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form
     *
     * @access protected
     * @return News2_Article_Block_Adminhtml_Article_Comment_Edit_Tab_Form
     * @author Medvslav
     */
    protected function _prepareForm()
    {
        $article = Mage::registry('current_article');
        $comment    = Mage::registry('current_comment');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'comment_form',
            array('legend'=>Mage::helper('medvslav_news2')->__('Comment'))
        );
        $fieldset->addField(
            'article_id',
            'hidden',
            array(
                'name'  => 'article_id',
                'after_element_html' => '<a href="'.
                    Mage::helper('adminhtml')->getUrl(
                        'adminhtml/news2_article/edit',
                        array(
                            'id'=>$article->getId()
                        )
                    ).
                    '" target="_blank">'.
                    Mage::helper('medvslav_news2')->__('Article').
                    ' : '.$article->getTitle().'</a>'
            )
        );
        $fieldset->addField(
            'title',
            'text',
            array(
                'label'    => Mage::helper('medvslav_news2')->__('Title'),
                'name'     => 'title',
                'required' => true,
                'class'    => 'required-entry',
            )
        );
        $fieldset->addField(
            'comment',
            'textarea',
            array(
                'label'    => Mage::helper('medvslav_news2')->__('Comment'),
                'name'     => 'comment',
                'required' => true,
                'class'    => 'required-entry',
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'    => Mage::helper('medvslav_news2')->__('Status'),
                'name'     => 'status',
                'required' => true,
                'class'    => 'required-entry',
                'values'   => array(
                    array(
                        'value' => Medvslav_News2_Model_Article_Comment::STATUS_PENDING,
                        'label' => Mage::helper('medvslav_news2')->__('Pending'),
                    ),
                    array(
                        'value' => Medvslav_News2_Model_Article_Comment::STATUS_APPROVED,
                        'label' => Mage::helper('medvslav_news2')->__('Approved'),
                    ),
                    array(
                        'value' => Medvslav_News2_Model_Article_Comment::STATUS_REJECTED,
                        'label' => Mage::helper('medvslav_news2')->__('Rejected'),
                    ),
                ),
            )
        );
        $configuration = array(
             'label' => Mage::helper('medvslav_news2')->__('Poster name'),
             'name'  => 'name',
             'required'  => true,
             'class' => 'required-entry',
        );
        if ($comment->getCustomerId()) {
            $configuration['after_element_html'] = '<a href="'.
                Mage::helper('adminhtml')->getUrl(
                    'adminhtml/customer/edit',
                    array(
                        'id'=>$comment->getCustomerId()
                    )
                ).
                '" target="_blank">'.
                Mage::helper('medvslav_news2')->__('Customer profile').'</a>';
        }
        $fieldset->addField('name', 'text', $configuration);
        $fieldset->addField(
            'email',
            'text',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Poster e-mail'),
                'name'  => 'email',
                'required'  => true,
                'class' => 'required-entry',
            )
        );
        $fieldset->addField(
            'customer_id',
            'hidden',
            array(
                'name'  => 'customer_id',
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
            Mage::registry('current_comment')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $form->addValues($this->getComment()->getData());
        return parent::_prepareForm();
    }

    /**
     * Get the current comment
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Comment
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
