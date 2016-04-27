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
 * Article admin controller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Adminhtml_News2_ArticleController extends Medvslav_News2_Controller_Adminhtml_News2
{
    /**
     * Init the article
     *
     * @access protected
     * @return Medvslav_News2_Model_Article
     */
    protected function _initArticle()
    {
        $articleId  = (int) $this->getRequest()->getParam('id');
        $article    = Mage::getModel('medvslav_news2/article');
        if ($articleId) {
            $article->load($articleId);
        }
        Mage::register('current_article', $article);
        return $article;
    }

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
        $this->_title(Mage::helper('medvslav_news2')->__('News2'))
             ->_title(Mage::helper('medvslav_news2')->__('Articles'));
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * Edit article - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function editAction()
    {
        $articleId    = $this->getRequest()->getParam('id');
        $article      = $this->_initArticle();
        if ($articleId && !$article->getId()) {
            $this->_getSession()->addError(
                Mage::helper('medvslav_news2')->__('This article no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getArticleData(true);
        if (!empty($data)) {
            $article->setData($data);
        }
        Mage::register('article_data', $article);
        $this->loadLayout();
        $this->_title(Mage::helper('medvslav_news2')->__('News2'))
             ->_title(Mage::helper('medvslav_news2')->__('Articles'));
        if ($article->getId()) {
            $this->_title($article->getTitle());
        } else {
            $this->_title(Mage::helper('medvslav_news2')->__('Add article'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * New article action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save article - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('article')) {
            try {
                $data = $this->_filterDates($data, array('publication_date'));
                $article = $this->_initArticle();
                $article->addData($data);
                $imageName = $this->_uploadAndGetName(
                    'image',
                    Mage::helper('medvslav_news2/article_image')->getImageBaseDir(),
                    $data
                );
                $article->setData('image', $imageName);
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $article->setProductsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($products));
                }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $article->setCategoriesData($categories);
                }
                $article->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Article was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $article->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setArticleData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was a problem saving the article.')
                );
                Mage::getSingleton('adminhtml/session')->setArticleData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Unable to find article to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Delete article - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $article = Mage::getModel('medvslav_news2/article');
                $article->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Article was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting article.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Could not find article to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Mass Delete article - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function MassDeleteAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select articles to delete.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                    $article = Mage::getModel('medvslav_news2/article');
                    $article->setId($articleId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Total of %d articles were successfully deleted.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass status change - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function massStatusAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select articles.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('medvslav_news2/article')->load($articleId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d articles were successfully updated.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error updating articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass author change - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function massAuthorIdAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select articles.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('medvslav_news2/article')->load($articleId)
                    ->setAuthorId($this->getRequest()->getParam('flag_author_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d articles were successfully updated.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error updating articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass newscategory change - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function massNewscategoryIdAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select articles.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('medvslav_news2/article')->load($articleId)
                    ->setNewscategoryId($this->getRequest()->getParam('flag_newscategory_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d articles were successfully updated.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error updating articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Get grid of products action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function productsAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->renderLayout();
    }

    /**
     * Get grid of products action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function productsgridAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->renderLayout();
    }

    /**
     * Get categories action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function categoriesAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get child categories action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function categoriesJsonAction()
    {
        $this->_initArticle();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('medvslav_news2/adminhtml_article_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Export as csv - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function exportCsvAction()
    {
        $fileName   = 'article.csv';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_article_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function exportExcelAction()
    {
        $fileName   = 'article.xls';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_article_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export as xml - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function exportXmlAction()
    {
        $fileName   = 'article.xml';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_article_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Medvslav
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('medvslav_news2/article');
    }
}
