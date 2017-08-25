<?php
N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemList extends N2SSPluginItemAbstract
{

    var $_identifier = 'list';

    protected $priority = 6;

    protected $layerProperties = array(
        "left"   => 0,
        "top"    => 0,
        "width"  => 400,
        "align"  => "left",
        "valign" => "top"
    );

    private static $font = 1304;

    public function __construct() {
        $this->_title = n2_x('List', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }
            $inited = true;
        }
    }

    private static $listStyle = 1801;
    private static $itemStyle = '';

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-liststyle');
            if (is_array($res)) {
                self::$listStyle = $res['value'];
            }
            if (is_numeric(self::$listStyle)) {
                N2StyleRenderer::preLoad(self::$listStyle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-list-itemstyle');
            if (is_array($res)) {
                self::$itemStyle = $res['value'];
            }
            if (is_numeric(self::$itemStyle)) {
                N2StyleRenderer::preLoad(self::$itemStyle);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultFont();
        $settings['font'][] = '<param name="item-list-font" type="font" previewmode="paragraph" label="' . n2_('Item') . ' - ' . n2_('List') . '" default="' . self::$font . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-list-liststyle" type="style" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('List') . '" default="' . self::$listStyle . '" />';
        $settings['style'][] = '<param name="item-list-itemstyle" type="style" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('List') . ' - ' . n2_('Item') . '" default="' . self::$itemStyle . '" />';
    }

    function getTemplate($slider) {
        return '<ol class="{liststyleclass} {fontclass}" style="list-style-type: {type}">{lis}</ol>';
    }

    function _render($data, $id, $slider, $slide) {
        return $this->getHTML($data, $slider, array(), $slide);
    }

    function _renderAdmin($data, $id, $slider, $slide) {
        return $this->getHTML($data, $slider, array(), $slide);
    }

    private function getHTML($data, $slider, $attributes, $slide) {

        $font      = N2FontRenderer::render($data->get('font'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $listStyle = N2StyleRenderer::render($data->get('liststyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');
        $itemStyle = N2StyleRenderer::render($data->get('itemstyle'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $html = '';
        $lis  = explode("\n", $slide->fill($data->get('content', '')));
        foreach ($lis AS $li) {
            $html .= '<li class="' . $itemStyle . '">' . $li . '</li>';
        }

        return N2Html::tag('ol', array(
            'class' => $font . '' . $listStyle,
            'style' => "list-style-type:" . $data->get('type')
        ), $html);
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();
        return array(
            'content'   => n2_("Item 1\nItem 2\nItem 3"),
            'font'      => self::$font,
            'liststyle' => self::$listStyle,
            'itemstyle' => self::$itemStyle,
            'type'      => 'disc'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('content', $slide->fill($data->get('content', '')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('liststyle'));
        $export->addVisual($data->get('itemstyle'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('liststyle', $import->fixSection($data->get('liststyle')));
        $data->set('itemstyle', $import->fixSection($data->get('itemstyle')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemList');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemList::onSmartsliderDefaultSettings');
