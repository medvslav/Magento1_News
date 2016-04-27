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
 * Author front contrller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_AuthorController extends Mage_Core_Controller_Front_Action
{

    /**
      * Default action
      *
      * @access public
      * @return void
      * @author Medvslav
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('medvslav_news2/author')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'authors',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Authors'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('medvslav_news2/author')->getAuthorsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('medvslav_news2/author/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('medvslav_news2/author/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('medvslav_news2/author/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * Init Author
     *
     * @access protected
     * @return Medvslav_News2_Model_Author
     * @author Medvslav
     */
    protected function _initAuthor()
    {
        $authorId   = $this->getRequest()->getParam('id', 0);
        $author     = Mage::getModel('medvslav_news2/author')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($authorId);
        if (!$author->getId()) {
            return false;
        } elseif (!$author->getStatus()) {
            return false;
        }
        return $author;
    }

    /**
     * View author action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function viewAction()
    {
        $author = $this->_initAuthor();
        if (!$author) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_author', $author);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news2-author news2-author' . $author->getId());
        }
        if (Mage::helper('medvslav_news2/author')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('medvslav_news2')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'authors',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Authors'),
                        'link'  => Mage::helper('medvslav_news2/author')->getAuthorsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'author',
                    array(
                        'label' => $author->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $author->getAuthorUrl());
        }
        if ($headBlock) {
            if ($author->getMetaTitle()) {
                $headBlock->setTitle($author->getMetaTitle());
            } else {
                $headBlock->setTitle($author->getName());
            }
            $headBlock->setKeywords($author->getMetaKeywords());
            $headBlock->setDescription($author->getMetaDescription());
        }
        $this->renderLayout();
    }
}
