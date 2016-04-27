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
 * Author list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Author_List extends Mage_Core_Block_Template
{
    /**
     * Initialize
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $authors = Mage::getResourceModel('medvslav_news2/author_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        $authors->setOrder('name', 'asc');
        $this->setAuthors($authors);
    }

    /**
     * Prepare the layout
     *
     * @access protected
     * @return Medvslav_News2_Block_Author_List
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'medvslav_news2.author.html.pager'
        )
        ->setCollection($this->getAuthors());
        $this->setChild('pager', $pager);
        $this->getAuthors()->load();
        return $this;
    }

    /**
     * Get the pager html
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
