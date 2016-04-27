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
 * Author admin controller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Adminhtml_News2_AuthorController extends Medvslav_News2_Controller_Adminhtml_News2
{
    /**
     * Init the author
     *
     * @access protected
     * @return Medvslav_News2_Model_Author
     */
    protected function _initAuthor()
    {
        $authorId  = (int) $this->getRequest()->getParam('id');
        $author    = Mage::getModel('medvslav_news2/author');
        if ($authorId) {
            $author->load($authorId);
        }
        Mage::register('current_author', $author);
        return $author;
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
             ->_title(Mage::helper('medvslav_news2')->__('Authors'));
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
     * Edit author - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function editAction()
    {
        $authorId    = $this->getRequest()->getParam('id');
        $author      = $this->_initAuthor();
        if ($authorId && !$author->getId()) {
            $this->_getSession()->addError(
                Mage::helper('medvslav_news2')->__('This author no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getAuthorData(true);
        if (!empty($data)) {
            $author->setData($data);
        }
        Mage::register('author_data', $author);
        $this->loadLayout();
        $this->_title(Mage::helper('medvslav_news2')->__('News2'))
             ->_title(Mage::helper('medvslav_news2')->__('Authors'));
        if ($author->getId()) {
            $this->_title($author->getName());
        } else {
            $this->_title(Mage::helper('medvslav_news2')->__('Add author'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * New author action
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
     * Save author - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('author')) {
            try {
                $author = $this->_initAuthor();
                $author->addData($data);
                $author->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Author was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $author->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAuthorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was a problem saving the author.')
                );
                Mage::getSingleton('adminhtml/session')->setAuthorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Unable to find author to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Delete author - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $author = Mage::getModel('medvslav_news2/author');
                $author->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Author was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting author.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('medvslav_news2')->__('Could not find author to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Mass Delete author - action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function MassDeleteAction()
    {
        $authorIds = $this->getRequest()->getParam('author');
        if (!is_array($authorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select authors to delete.')
            );
        } else {
            try {
                foreach ($authorIds as $authorId) {
                    $author = Mage::getModel('medvslav_news2/author');
                    $author->setId($authorId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('Total of %d authors were successfully deleted.', count($authorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error deleting authors.')
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
        $authorIds = $this->getRequest()->getParam('author');
        if (!is_array($authorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('medvslav_news2')->__('Please select authors.')
            );
        } else {
            try {
                foreach ($authorIds as $authorId) {
                $author = Mage::getSingleton('medvslav_news2/author')->load($authorId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d authors were successfully updated.', count($authorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('There was an error updating authors.')
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
        $fileName   = 'author.csv';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_author_grid')
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
        $fileName   = 'author.xls';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_author_grid')
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
        $fileName   = 'author.xml';
        $content    = $this->getLayout()->createBlock('medvslav_news2/adminhtml_author_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('medvslav_news2/author');
    }
}
