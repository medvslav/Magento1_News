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
 * Article - category controller
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Medvslav_News2_Adminhtml_News2_Article_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
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
     * articles grid in the catalog page
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function articlesgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.article')
            ->setCategoryArticles($this->getRequest()->getPost('category_articles', null));
        $this->renderLayout();
    }
}
