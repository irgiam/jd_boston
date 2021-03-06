<?php

N2Loader::import('libraries.form.element.list');

class N2ElementPhocaGalleryCategories extends N2ElementList
{

    function fetchElement() {

        $query = 'SELECT
            *, title, 
            parent_id AS parent, parent_id  
          FROM #__phocagallery_categories 
          WHERE published = 1 ORDER BY parent_id, ordering';

        $model     = new N2Model('phocagallery_categories');
        $menuItems = $model->db->queryAll($query, false, "object");

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }

}
