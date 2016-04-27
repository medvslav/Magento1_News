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
 * Article admin grid block
 *
 * @category    Medvslav
 * @package     Medvslav_News2
 * @author      Medvslav
 */
class Medvslav_News2_Block_Adminhtml_Article_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('articleGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
     * @author Medvslav
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('medvslav_news2/article')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collection
     *
     * @access protected
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
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
            'author_id',
            array(
                'header'    => Mage::helper('medvslav_news2')->__('Author'),
                'index'     => 'author_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('medvslav_news2/author_collection')
                    ->toOptionHash(),
                'renderer'  => 'medvslav_news2/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getAuthorId'
                ),
                'base_link' => 'adminhtml/news2_author/edit'
            )
        );
        $this->addColumn(
            'newscategory_id',
            array(
                'header'    => Mage::helper('medvslav_news2')->__('Newscategory'),
                'index'     => 'newscategory_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('medvslav_news2/newscategory_collection')
                    ->toOptionHash(),
                'renderer'  => 'medvslav_news2/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getNewscategoryId'
                ),
                'static' => array(
                    'clear' => 1
                ),
                'base_link' => 'adminhtml/news2_newscategory/edit'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('medvslav_news2')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
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
            'publication_date',
            array(
                'header' => Mage::helper('medvslav_news2')->__('Publication_date'),
                'index'  => 'publication_date',
                'type'=> 'date',

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
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
     * @author Medvslav
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('article');
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
        $values = Mage::getResourceModel('medvslav_news2/author_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'author_id',
            array(
                'label'      => Mage::helper('medvslav_news2')->__('Change Author'),
                'url'        => $this->getUrl('*/*/massAuthorId', array('_current'=>true)),
                'additional' => array(
                    'flag_author_id' => array(
                        'name'   => 'flag_author_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('medvslav_news2')->__('Author'),
                        'values' => $values
                    )
                )
            )
        );
        $values = Mage::getResourceModel('medvslav_news2/newscategory_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'newscategory_id',
            array(
                'label'      => Mage::helper('medvslav_news2')->__('Change Newscategory'),
                'url'        => $this->getUrl('*/*/massNewscategoryId', array('_current'=>true)),
                'additional' => array(
                    'flag_newscategory_id' => array(
                        'name'   => 'flag_newscategory_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('medvslav_news2')->__('Newscategory'),
                        'values' => $values
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
     * @param Medvslav_News2_Model_Article
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
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
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
     * @param Medvslav_News2_Model_Resource_Article_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Medvslav_News2_Block_Adminhtml_Article_Grid
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
