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
 * Newscategory admin controller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Adminhtml_News2_NewscategoryController extends Medvslav_News2_Controller_Adminhtml_News2
{
    /**
     * Init newscategory
     *
     * @access protected
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    protected function _initNewscategory()
    {
        $newscategoryId = (int) $this->getRequest()->getParam('id', false);
        $newscategory = Mage::getModel('medvslav_news2/newscategory');
        if ($newscategoryId) {
            $newscategory->load($newscategoryId);
        } else {
            $newscategory->setData($newscategory->getDefaultValues());
        }
        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setNewscategoryActiveTabId($activeTabId);
        }
        Mage::register('newscategory', $newscategory);
        Mage::register('current_newscategory', $newscategory);
        return $newscategory;
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
        $this->_forward('edit');
    }

    /**
     * Add new newscategory form
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsNewscategoryActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit newscategory page
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;
        $parentId = (int) $this->getRequest()->getParam('parent');
        $newscategoryId = (int) $this->getRequest()->getParam('id');
        $_prevNewscategoryId = Mage::getSingleton('admin/session')->getLastEditedNewscategory(true);
        if ($_prevNewscategoryId &&
            !$this->getRequest()->getQuery('isAjax') &&
            !$this->getRequest()->getParam('clear')) {
            $this->getRequest()->setParam('id', $_prevNewscategoryId);
        }
        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }
        if (!($newscategory = $this->_initNewscategory())) {
            return;
        }
        $this->_title($newscategoryId ? $newscategory->getName() : $this->__('New Newscategory'));
        $data = Mage::getSingleton('adminhtml/session')->getNewscategoryData(true);
        if (isset($data['newscategory'])) {
            $newscategory->addData($data['newscategory']);
        }
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $newscategory->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getNewscategoryDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }
            Mage::getSingleton('admin/session')->setLastEditedNewscategory($newscategory->getId());
            $this->loadLayout();
            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('newscategory.edit')->getFormHtml().
                        $this->getLayout()->getBlock('newscategory.tree')->getBreadcrumbsJavascript(
                            $breadcrumbsPath,
                            'editingNewscategoryBreadcrumbs'
                        ),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($eventResponse->getData()));
            return;
        }
        $this->loadLayout();
        $this->_title(Mage::helper('medvslav_news2')->__('News2'))
             ->_title(Mage::helper('medvslav_news2')->__('Nescategories'));
        $this->_setActiveMenu('medvslav_news2/newscategory');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('newscategory');

        $this->_addBreadcrumb(
            Mage::helper('medvslav_news2')->__('Manage Nescategories'),
            Mage::helper('medvslav_news2')->__('Manage Nescategories')
        );
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * Get tree node (Ajax version)
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function newscategoriesJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setNewscategoryIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setNewscategoryIsTreeWasExpanded(false);
        }
        if ($newscategoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $newscategoryId);
            if (!$newscategory = $this->_initNewscategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('medvslav_news2/adminhtml_newscategory_tree')
                    ->getTreeJson($newscategory)
            );
        }
    }

    /**
     * Move newscategory action
     * @access public
     * @author Medvslav
     */
    public function moveAction()
    {
        $newscategory = $this->_initNewscategory();
        if (!$newscategory) {
            $this->getResponse()->setBody(
                Mage::helper('medvslav_news2')->__('Newscategory move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId = $this->getRequest()->getPost('aid', false);
        try {
            $newscategory->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('medvslav_news2')->__('Newscategory move error')
            );
            Mage::logException($e);
        }
    }

    /**
     * Tree Action
     * Retrieve newscategory tree
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function treeAction()
    {
        $newscategoryId = (int) $this->getRequest()->getParam('id');
        $newscategory = $this->_initNewscategory();
        $block = $this->getLayout()->createBlock('medvslav_news2/adminhtml_newscategory_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text'          => $block->buildNodeName($root),
                        'draggable'     => false,
                        'allowDrop'     => ($root->getIsVisible()) ? true : false,
                        'id'            => (int) $root->getId(),
                        'expanded'      => (int) $block->getIsWasExpanded(),
                        'newscategory_id' => (int) $newscategory->getId(),
                        'root_visible'  => (int) $root->getIsVisible()
                    )
                )
            )
        );
    }

    /**
     * Build response for refresh input element 'path' in form
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $newscategory = Mage::getModel('medvslav_news2/newscategory')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $newscategory->getPath(),
                    )
                )
            );
        }
    }

    /**
     * Delete newscategory action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $newscategory = Mage::getModel('medvslav_news2/newscategory')->load($id);
                Mage::getSingleton('admin/session')->setNewscategoryDeletedPath($newscategory->getPath());

                $newscategory->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('The newscategory has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('medvslav_news2')->__('An error occurred while trying to delete the newscategory.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                Mage::logException($e);
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
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
        return Mage::getSingleton('admin/session')->isAllowed('medvslav_news2/newscategory');
    }

    /**
     * Wyisiwyg action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeMediaUrl = Mage::app()->getStore(0)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'adminhtml/catalog_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => 0,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Newscategory save action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function saveAction()
    {
         if (!$newscategory = $this->_initNewscategory()) {
            return;
        }
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost('newscategory')) {
            $newscategory->addData($data);
            if (!$newscategory->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId();
                }
                $parentNewscategory = Mage::getModel('medvslav_news2/newscategory')->load($parentId);
                $newscategory->setPath($parentNewscategory->getPath());
            }
            try {
                $newscategory->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('medvslav_news2')->__('The newscategory has been saved.')
                );
                $refreshTree = 'true';
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())->setNewscategoryData($data);
                Mage::logException($e);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $newscategory->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }
}
