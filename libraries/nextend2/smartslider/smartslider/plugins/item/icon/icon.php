<?php
class N2SSPluginItemIcon extends N2SSPluginItemAbstract {

    public $_identifier = 'icon';

    protected $priority = 6;

    protected $layerProperties = array("width" => 50);

    public function __construct() {
        $this->_title = n2_x('Icon', 'Slide item');
    }

    public function getTemplate($slider) {

        return '<div class="{styleclass}"><img class="n2-ow" src="data:image/svg+xml;base64,{image}" style="display: inline-block;width:{width};height:{height};" /></div>';
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {

        return $this->getHtml($slider, $data);
    }

    function _render($data, $itemId, $slider, $slide) {

        return $this->getLink($slide, $data, $this->getHtml($slider, $data), array(
            'style' => 'display:block;',
            'class' => 'n2-ow'
        ));
    }

    private function getHtml($slider, $data) {
        $svg = $data->get('icon', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="32" height="32"><rect width="100" height="100" data-style="{style}" /></svg>');


        list($width, $height) = (array)N2Parse::parse($data->get('size', '100%|*|auto'));
        list($color, $alpha) = N2Color::colorToSVG($data->get('color', '00000080'));
        $style = 'fill:#' . $color . ';fill-opacity:' . $alpha;

        list($colorHover, $alphaHover) = N2Color::colorToSVG($data->get('color-hover', '00000000'));
        $styleHover = 'fill:#' . $colorHover . ';fill-opacity:' . $alphaHover;

        $styleClass = N2StyleRenderer::render($data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');

        if ($alphaHover != 0) {
            $styleClass .= ' n2-ss-icon-has-hover';
        }
        return '<span class="' . $styleClass . '" style="display:block;">' . N2Html::image('data:image/svg+xml;base64,' . base64_encode(str_replace(array(
                'data-style',
                '{style}'
            ), array(
                'style',
                $style
            ), $svg)), 'Icon', array(
            'class' => 'n2-ow n2-ss-icon-normal',
            'style' => 'width:' . $width . ';height:' . $height . ';'
        )) . ($alphaHover == 0 ? '' : N2Html::image('data:image/svg+xml;base64,' . base64_encode(str_replace(array(
                'data-style',
                '{style}'
            ), array(
                'style',
                $styleHover
            ), $svg)), 'Icon', array(
            'class' => 'n2-ow n2-ss-icon-hover',
            'style' => 'width:' . $width . ';height:' . $height . ';'
        ))) . '</span>';
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="32" height="32"><rect width="100" height="100" data-style="{style}" /></svg>',
            'color'       => '00000080',
            'color-hover' => '00000000',
            'size'        => '100%|*|auto',
            'link'        => '#|*|_self',
            'style'       => ''
        );
    }

    public function getFilled($slide, $data) {
        $data->set('icon', $slide->fill($data->get('icon', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));
        return $data;
    }

}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemIcon');
