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
 * Newscategory front contrller
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_NewscategoryController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('medvslav_news2/newscategory')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'newscategories',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Nescategories'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('medvslav_news2/newscategory')->getNewscategoriesUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('medvslav_news2/newscategory/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('medvslav_news2/newscategory/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('medvslav_news2/newscategory/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * Init Newscategory
     *
     * @access protected
     * @return Medvslav_News2_Model_Newscategory
     * @author Medvslav
     */
    protected function _initNewscategory()
    {
        $newscategoryId   = $this->getRequest()->getParam('id', 0);
        $newscategory     = Mage::getModel('medvslav_news2/newscategory')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($newscategoryId);
        if (!$newscategory->getId()) {
            return false;
        } elseif (!$newscategory->getStatus()) {
            return false;
        }
        return $newscategory;
    }

    /**
     * View newscategory action
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function viewAction()
    {
        $newscategory = $this->_initNewscategory();
        if (!$newscategory) {
            $this->_forward('no-route');
            return;
        }
        if (!$newscategory->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_newscategory', $newscategory);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('news2-newscategory news2-newscategory' . $newscategory->getId());
        }
        if (Mage::helper('medvslav_news2/newscategory')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('medvslav_news2')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'newscategories',
                    array(
                        'label' => Mage::helper('medvslav_news2')->__('Nescategories'),
                        'link'  => Mage::helper('medvslav_news2/newscategory')->getNewscategoriesUrl(),
                    )
                );
                $parents = $newscategory->getParentNewscategories();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('medvslav_news2/newscategory')->getRootNewscategoryId() &&
                        $parent->getId() != $newscategory->getId()) {
                        $breadcrumbBlock->addCrumb(
                            'newscategory-'.$parent->getId(),
                            array(
                                'label'    => $parent->getName(),
                                'link'    => $link = $parent->getNewscategoryUrl(),
                            )
                        );
                    }
                }
                $breadcrumbBlock->addCrumb(
                    'newscategory',
                    array(
                        'label' => $newscategory->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $newscategory->getNewscategoryUrl());
        }
        if ($headBlock) {
            if ($newscategory->getMetaTitle()) {
                $headBlock->setTitle($newscategory->getMetaTitle());
            } else {
                $headBlock->setTitle($newscategory->getName());
            }
            $headBlock->setKeywords($newscategory->getMetaKeywords());
            $headBlock->setDescription($newscategory->getMetaDescription());
        }
        $this->renderLayout();
    }
}
