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
 * Author admin block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Author extends Mage_Adminhtml_Block_Widget_Grid_Container
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
        $this->_controller         = 'adminhtml_author';
        $this->_blockGroup         = 'medvslav_news2';
        parent::__construct();
        $this->_headerText         = Mage::helper('medvslav_news2')->__('Author');
        $this->_updateButton('add', 'label', Mage::helper('medvslav_news2')->__('Add Author'));

    }
}
