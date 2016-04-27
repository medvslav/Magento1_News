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
 * Article comments admin grid block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     *
     * @access public
     * @author Medvslav
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('articleCommentGrid');
        $this->setDefaultSort('ct_comment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Comment_Grid
     * @author Medvslav
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('medvslav_news2/article_comment_article_collection');
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Comment_Grid
     * @author Medvslav
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ct_comment_id',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Id'),
                'index'         => 'ct_comment_id',
                'type'          => 'number',
                'filter_index'  => 'ct.comment_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Title'),
                'index'         => 'title',
                'filter_index'  => 'main_table.title',
            )
        );
        $this->addColumn(
            'ct_title',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Comment Title'),
                'index'         => 'ct_title',
                'filter_index'  => 'ct.title',
            )
        );
        $this->addColumn(
            'ct_name',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Poster name'),
                'index'         => 'ct_name',
                'filter_index'  => 'ct.name',
            )
        );
        $this->addColumn(
            'ct_email',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Poster email'),
                'index'         => 'ct_email',
                'filter_index'  => 'ct.email',
            )
        );
        $this->addColumn(
            'ct_status',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Status'),
                'index'         => 'ct_status',
                'filter_index'  => 'ct.status',
                'type'          => 'options',
                'options'       => array(
                    Medvslav_News2_Model_Article_Comment::STATUS_PENDING  =>
                        Mage::helper('medvslav_news2')->__('Pending'),
                    Medvslav_News2_Model_Article_Comment::STATUS_APPROVED =>
                        Mage::helper('medvslav_news2')->__('Approved'),
                    Medvslav_News2_Model_Article_Comment::STATUS_REJECTED =>
                        Mage::helper('medvslav_news2')->__('Rejected'),
                )
            )
        );
        $this->addColumn(
            'ct_created_at',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Created at'),
                'index'         => 'ct_created_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.created_at',
            )
        );
        $this->addColumn(
            'ct_updated_at',
            array(
                'header'        => Mage::helper('medvslav_news2')->__('Updated at'),
                'index'         => 'ct_updated_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.updated_at',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'stores',
                array(
                    'header'     => Mage::helper('medvslav_news2')->__('Store Views'),
                    'index'      => 'stores',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('medvslav_news2')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getCtCommentId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('medvslav_news2')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('medvslav_news2')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('medvslav_news2')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('medvslav_news2')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
     * @author Medvslav
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ct_comment_id');
        $this->setMassactionIdFilter('ct.comment_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('comment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('medvslav_news2')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('medvslav_news2')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('medvslav_news2')->__('Change status'),
                'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('medvslav_news2')->__('Status'),
                            'values' => array(
                                Medvslav_News2_Model_Article_Comment::STATUS_PENDING  =>
                                    Mage::helper('medvslav_news2')->__('Pending'),
                                Medvslav_News2_Model_Article_Comment::STATUS_APPROVED =>
                                    Mage::helper('medvslav_news2')->__('Approved'),
                                Medvslav_News2_Model_Article_Comment::STATUS_REJECTED =>
                                    Mage::helper('medvslav_news2')->__('Rejected'),
                            )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * Get the row url
     *
     * @access public
     * @param Medvslav_News2_Model_Article_Comment
     * @return string
     * @author Medvslav
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getCtCommentId()));
    }

    /**
     * Get the grid url
     *
     * @access public
     * @return string
     * @author Medvslav
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Filter store column
     *
     * @access protected
     * @param Medvslav_News2_Model_Resource_Article_Comment_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Medvslav_News2_Block_Adminhtml_Article_Comment_Grid
     * @author Medvslav
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->setStoreFilter($value);
        return $this;
    }
}
