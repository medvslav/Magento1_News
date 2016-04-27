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
 * Author model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Author extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'medvslav_news2_author';
    const CACHE_TAG = 'medvslav_news2_author';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'medvslav_news2_author';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'author';

    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('medvslav_news2/author');
    }

    /**
     * Before save author
     *
     * @access protected
     * @return Medvslav_News2_Model_Author
     * @author Medvslav
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * Get the url to the author details page
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getAuthorUrl()
    {
        return Mage::getUrl('medvslav_news2/author/view', array('id'=>$this->getId()));
    }

    /**
     * Get the author Description
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getDescription()
    {
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }

    /**
     * Save author relation
     *
     * @access public
     * @return Medvslav_News2_Model_Author
     * @author Medvslav
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * Retrieve  collection
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Collection
     * @author Medvslav
     */
    public function getSelectedArticlesCollection()
    {
        if (!$this->hasData('_article_collection')) {
            if (!$this->getId()) {
                return new Varien_Data_Collection();
            } else {
                $collection = Mage::getResourceModel('medvslav_news2/article_collection')
                        ->addFieldToFilter('author_id', $this->getId());
                $this->setData('_article_collection', $collection);
            }
        }
        return $this->getData('_article_collection');
    }

    /**
     * Get default values
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
