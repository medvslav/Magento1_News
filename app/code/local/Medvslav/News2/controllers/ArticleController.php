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
 * Article front contrller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_ArticleController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('medvslav_news2/article')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'articles',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Articles'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('medvslav_news2/article')->getArticlesUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('medvslav_news2/article/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('medvslav_news2/article/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('medvslav_news2/article/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * Init Article
     *
     * @access protected
     * @return Medvslav_News2_Model_Article
     * @author Medvslav
     */
    protected function _initArticle()
    {
        $articleId   = $this->getRequest()->getParam('id', 0);
        $article     = Mage::getModel('medvslav_news2/article')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($articleId);
        if (!$article->getId()) {
            return false;
        } elseif (!$article->getStatus()) {
            return false;
        }
        return $article;
    }

    /**
     * View article action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function viewAction()
    {
        $article = $this->_initArticle();
        if (!$article) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_article', $article);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news2-article news2-article' . $article->getId());
        }
        if (Mage::helper('medvslav_news2/article')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('medvslav_news2')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'articles',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Articles'),
                        'link'  => Mage::helper('medvslav_news2/article')->getArticlesUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'article',
                    array(
                        'label' => $article->getTitle(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $article->getArticleUrl());
        }
        if ($headBlock) {
            if ($article->getMetaTitle()) {
                $headBlock->setTitle($article->getMetaTitle());
            } else {
                $headBlock->setTitle($article->getTitle());
            }
            $headBlock->setKeywords($article->getMetaKeywords());
            $headBlock->setDescription($article->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * articles rss list action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function rssAction()
    {
        if (Mage::helper('medvslav_news2/article')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Submit new comment action
     * @access public
     * @author Medvslav
     */
    public function commentpostAction()
    {
        $data   = $this->getRequest()->getPost();
        $article = $this->_initArticle();
        $session    = Mage::getSingleton('core/session');
        if ($article) {
            if ($article->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() ||
                    Mage::getStoreConfigFlag('medvslav_news2/article/allow_guest_comment'))) {
                    $comment  = Mage::getModel('medvslav_news2/article_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setArticleId($article->getId())
                                ->setStatus(Medvslav_News2_Model_Article_Comment::STATUS_PENDING)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        } catch (Exception $e) {
                            $session->setArticleCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    } else {
                        $session->setArticleCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                } else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            } else {
                $session->addError($this->__('This article does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }
}
