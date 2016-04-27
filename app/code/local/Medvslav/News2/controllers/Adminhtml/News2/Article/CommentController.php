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
class Medvslav_News2_Adminhtml_News2_Article_CommentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init the comment
     *
     * @access protected
     * @return Medvslav_News2_Model_Article_Comment
     * @author Medvslav
     */
    protected function _initComment()
    {
        $commentId  = (int) $this->getRequest()->getParam('id');
        $comment    = Mage::getModel('medvslav_news2/article_comment');
        if ($commentId) {
            $comment->load($commentId);
        }
        Mage::register('current_comment', $comment);
        return $comment;
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
             ->_title(Mage::helper('medvslav_news2')->__('Articles'))
             ->_title(Mage::helper('medvslav_news2')->__('Comments'));
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
     * Edit comment - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function editAction()
    {
        $commentId    = $this->getRequest()->getParam('id');
        $comment      = $this->_initComment();
        if (!$comment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('medvslav_news2')->__('This comment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $comment->setData($data);
        }
        Mage::register('comment_data', $comment);
        $article = Mage::getModel('medvslav_news2/article')->load($comment->getArticleId());
        Mage::register('current_article', $article);
        $this->loadLayout();
        $this->_title(Mage::helper('medvslav_news2')->__('News2'))
             ->_title(Mage::helper('medvslav_news2')->__('Articles'))
             ->_title(Mage::helper('medvslav_news2')->__('Comments'))
             ->_title($comment->getTitle());
        $this->renderLayout();
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
        if ($data = $this->getRequest()->getPost('comment')) {
            try {
                $comment = $this->_initComment();
                $comment->addData($data);
                if (!$comment->getCustomerId()) {
                    $comment->unsCustomerId();
                }
                $comment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Comment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $comment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was a problem saving the comment.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Unable to find comment to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Delete comment - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $comment = Mage::getModel('medvslav_news2/article_comment');
                $comment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Comment was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting the comment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Could not find comment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Mass delete comments - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function massDeleteAction()
    {
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select comments to delete.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getModel('medvslav_news2/article_comment');
                    $comment->setId($commentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__(
                        'Total of %d comments were successfully deleted.',
                        count($commentIds)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting comments.')
                );
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
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select comments.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getSingleton('medvslav_news2/article_comment')->load($commentId)
                         ->setStatus($this->getRequest()->getParam('status'))
                         ->setIsMassupdate(true)
                         ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($commentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error updating comments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
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
        $fileName   = 'article_comments.csv';
        $content    = $this->getLayout()->createBlock(
            'medvslav_news2/adminhtml_article_comment_grid'
        )
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
        $fileName   = 'article_comments.xls';
        $content    = $this->getLayout()->createBlock(
            'medvslav_news2/adminhtml_article_comment_grid'
        )
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
        $fileName   = 'article_comments.xml';
        $content    = $this->getLayout()->createBlock(
            'medvslav_news2/adminhtml_article_comment_grid'
        )
        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check access
     *
     * @access protected
     * @return bool
     * @author Medvslav
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('medvslav_news2/article_comments');
    }
}
