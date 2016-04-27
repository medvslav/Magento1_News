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
 * Article edit form tab
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Edit_Tab_Form
     * @author Medvslav
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'article_form',
            array('legend' => Mage::helper('medvslav_news2')->__('Article'))
        );
        $fieldset->addType(
            'image',
            Mage::getConfig()->getBlockClassName('medvslav_news2/adminhtml_article_helper_image')
        );
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $values = Mage::getResourceModel('medvslav_news2/author_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="article_author_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeAuthorIdLink() {
                if ($(\'article_author_id\').value == \'\') {
                    $(\'article_author_id_link\').hide();
                } else {
                    $(\'article_author_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/news2_author/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'article_author_id\').value);
                    $(\'article_author_id_link\').href = realUrl;
                    $(\'article_author_id_link\').innerHTML = text.replace(\'{#name}\', $(\'article_author_id\').options[$(\'article_author_id\').selectedIndex].innerHTML);
                }
            }
            $(\'article_author_id\').observe(\'change\', changeAuthorIdLink);
            changeAuthorIdLink();
            </script>';

        $fieldset->addField(
            'author_id',
            'select',
            array(
                'label'     => Mage::helper('medvslav_news2')->__('Author'),
                'name'      => 'author_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
        );
        $values = Mage::getResourceModel('medvslav_news2/newscategory_collection')
            ->toOptionArray();
        array_unshift($values, array('label' => '', 'value' => ''));

        $html = '<a href="{#url}" id="article_newscategory_id_link" target="_blank"></a>';
        $html .= '<script type="text/javascript">
            function changeNewscategoryIdLink() {
                if ($(\'article_newscategory_id\').value == \'\') {
                    $(\'article_newscategory_id_link\').hide();
                } else {
                    $(\'article_newscategory_id_link\').show();
                    var url = \''.$this->getUrl('adminhtml/news2_newscategory/edit', array('id'=>'{#id}', 'clear'=>1)).'\';
                    var text = \''.Mage::helper('core')->escapeHtml($this->__('View {#name}')).'\';
                    var realUrl = url.replace(\'{#id}\', $(\'article_newscategory_id\').value);
                    $(\'article_newscategory_id_link\').href = realUrl;
                    $(\'article_newscategory_id_link\').innerHTML = text.replace(\'{#name}\', $(\'article_newscategory_id\').options[$(\'article_newscategory_id\').selectedIndex].innerHTML);
                }
            }
            $(\'article_newscategory_id\').observe(\'change\', changeNewscategoryIdLink);
            changeNewscategoryIdLink();
            </script>';

        $fieldset->addField(
            'newscategory_id',
            'select',
            array(
                'label'     => Mage::helper('medvslav_news2')->__('Newscategory'),
                'name'      => 'newscategory_id',
                'required'  => false,
                'values'    => $values,
                'after_element_html' => $html
            )
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Title'),
                'name'  => 'title',
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
            'content',
            'editor',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Content'),
                'name'  => 'content',
            'config' => $wysiwygConfig,
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'publication_date',
            'date',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Publication_date'),
                'name'  => 'publication_date',
            'required'  => true,
            'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
           )
        );

        $fieldset->addField(
            'image',
            'image',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Image'),
                'name'  => 'image',

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
        $fieldset->addField(
            'in_rss',
            'select',
            array(
                'label'  => Mage::helper('medvslav_news2')->__('Show in rss'),
                'name'   => 'in_rss',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('medvslav_news2')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('medvslav_news2')->__('No'),
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
            Mage::registry('current_article')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $fieldset->addField(
            'allow_comment',
            'select',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Allow Comments'),
                'name'  => 'allow_comment',
                'values'=> Mage::getModel('medvslav_news2/adminhtml_source_yesnodefault')->toOptionArray()
            )
        );
        $formValues = Mage::registry('current_article')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getArticleData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getArticleData());
            Mage::getSingleton('adminhtml/session')->setArticleData(null);
        } elseif (Mage::registry('current_article')) {
            $formValues = array_merge($formValues, Mage::registry('current_article')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
