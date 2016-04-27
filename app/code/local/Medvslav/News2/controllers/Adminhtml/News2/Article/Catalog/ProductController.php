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
 * Article - product controller
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Medvslav_News2_Adminhtml_News2_Article_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Construct
     *
     * @access protected
     * @return void
     * @author Medvslav
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Medvslav_News2');
    }

    /**
     * articles in the catalog page
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function articlesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.article')
            ->setProductArticles($this->getRequest()->getPost('product_articles', null));
        $this->renderLayout();
    }

    /**
     * articles grid in the catalog page
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function articlesGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.article')
            ->setProductArticles($this->getRequest()->getPost('product_articles', null));
        $this->renderLayout();
    }
}
