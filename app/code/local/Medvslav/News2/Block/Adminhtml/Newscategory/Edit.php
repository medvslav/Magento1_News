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
 * Newscategory admin edit form
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Newscategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     * @author Medvslav
     */
    public function __construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_blockGroup  = 'medvslav_news2';
        $this->_controller  = 'adminhtml_newscategory';
        $this->_mode        = 'edit';
        parent::__construct();
        $this->setTemplate('medvslav_news2/newscategory/edit.phtml');
    }
}

