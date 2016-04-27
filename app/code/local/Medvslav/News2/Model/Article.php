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
 * Article model
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Model_Article extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'medvslav_news2_article';
    const CACHE_TAG = 'medvslav_news2_article';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'medvslav_news2_article';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'article';
    protected $_productInstance = null;
    protected $_categoryInstance = null;

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
        $this->_init('medvslav_news2/article');
    }

    /**
     * Before save article
     *
     * @access protected
     * @return Medvslav_News2_Model_Article
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
     * Get the url to the article details page
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getArticleUrl()
    {
        return Mage::getUrl('medvslav_news2/article/view', array('id'=>$this->getId()));
    }

    /**
     * Get the article Description
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
     * Get the article Content
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getContent()
    {
        $content = $this->getData('content');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($content);
        return $html;
    }

    /**
     * Save article relation
     *
     * @access public
     * @return Medvslav_News2_Model_Article
     * @author Medvslav
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveArticleRelation($this);
        $this->getCategoryInstance()->saveArticleRelation($this);
        return parent::_afterSave();
    }

    /**
     * Get product relation model
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Product
     * @author Medvslav
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('medvslav_news2/article_product');
        }
        return $this->_productInstance;
    }

    /**
     * Get selected products array
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return Medvslav_News2_Resource_Article_Product_Collection
     * @author Medvslav
     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * Get category relation model
     *
     * @access public
     * @return Medvslav_News2_Model_Article_Category
     * @author Medvslav
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('medvslav_news2/article_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * Get selected categories array
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Medvslav_News2_Resource_Article_Category_Collection
     * @author Medvslav
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Medvslav_News2_Model_Author
     * @author Medvslav
     */
    public function getParentAuthor()
    {
        if (!$this->hasData('_parent_author')) {
            if (!$this->getAuthorId()) {
                return null;
            } else {
                $author = Mage::getModel('medvslav_news2/author')
                    ->load($this->getAuthorId());
                if ($author->getId()) {
                    $this->setData('_parent_author', $author);
                } else {
                    $this->setData('_parent_author', null);
                }
            }
        }
        return $this->getData('_parent_author');
    }

    /**
     * Retrieve parent 
     *
     * @access public
     * @return null|Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    public function getParentNewscategory()
    {
        if (!$this->hasData('_parent_newscategory')) {
            if (!$this->getNewscategoryId()) {
                return null;
            } else {
                $newscategory = Mage::getModel('medvslav_news2/newscategory')
                    ->load($this->getNewscategoryId());
                if ($newscategory->getId()) {
                    $this->setData('_parent_newscategory', $newscategory);
                } else {
                    $this->setData('_parent_newscategory', null);
                }
            }
        }
        return $this->getData('_parent_newscategory');
    }

    /**
     * Check if comments are allowed
     *
     * @access public
     * @return array
     * @author Medvslav
     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Medvslav_News2_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Medvslav_News2_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('medvslav_news2/article/allow_comment');
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
        $values['in_rss'] = 1;
        $values['allow_comment'] = Medvslav_News2_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        return $values;
    }
    
}
