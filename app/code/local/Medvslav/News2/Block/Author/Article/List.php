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
 * Author Articles list block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Author_Article_List extends Medvslav_News2_Block_Article_List
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
        $author = $this->getAuthor();
        if ($author) {
            $this->getArticles()->addFieldToFilter('author_id', $author->getId());
        }
    }

    /**
     * Prepare the layout - actually do nothing
     *
     * @access protected
     * @return Medvslav_News2_Block_Author_Article_List
     * @author Medvslav
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Get the current author
     *
     * @access public
     * @return Medvslav_News2_Model_Author
     * @author Medvslav
     */
    public function getAuthor()
    {
        return Mage::registry('current_author');
    }
}
