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
 * Author admin grid block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Author_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('authorGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Author_Grid
     * @author Medvslav
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('medvslav_news2/author')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Author_Grid
     * @author Medvslav
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('medvslav_news2')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('medvslav_news2')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('medvslav_news2')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('medvslav_news2')->__('Enabled'),
                    '0' => Mage::helper('medvslav_news2')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'email',
            array(
                'header' => Mage::helper('medvslav_news2')->__('Email'),
                'index'  => 'email',
                'type'=> 'text',

            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'store_id',
                array(
                    'header'     => Mage::helper('medvslav_news2')->__('Store Views'),
                    'index'      => 'store_id',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback'=> array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('medvslav_news2')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('medvslav_news2')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('medvslav_news2')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
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
     * @return Medvslav_News2_Block_Adminhtml_Author_Grid
     * @author Medvslav
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('author');
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
                'label'      => Mage::helper('medvslav_news2')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('medvslav_news2')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('medvslav_news2')->__('Enabled'),
                            '0' => Mage::helper('medvslav_news2')->__('Disabled'),
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
     * @param Medvslav_News2_Model_Author
     * @return string
     * @author Medvslav
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
     * After collection load
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Author_Grid
     * @author Medvslav
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store column
     *
     * @access protected
     * @param Medvslav_News2_Model_Resource_Author_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Medvslav_News2_Block_Adminhtml_Author_Grid
     * @author Medvslav
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
