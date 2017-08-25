<?php
N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemTransition extends N2SSPluginItemAbstract
{

    var $_identifier = 'transition';

    protected $priority = 7;

    protected $layerProperties = array("width" => 200);

    public function __construct() {
        $this->_title = n2_x('Transition', 'Slide item');
    }

    function getTemplate($slider) {

        $this->loadResources($slider);

        $html = N2Html::openTag("div", array(
            "id"    => '{uid}',
            "class" => "n2-ss-item-transition"
        ));

        $html .= N2Html::openTag("a", array(
            "href"    => '#',
            "class"   => 'n2-ow',
            "onclick" => "return false;"
        ));
        $html .= N2Html::openTag("div", array(
            "class" => "n2-ss-item-transition-inner"
        ));

        $html .= N2Html::image('{image}', 'Image', array(
            'class' => 'n2-ss-item-transition-image1 n2-ow'
        ));

        $html .= N2Html::image('{image2}', 'Image', array(
            'class' => 'n2-ss-item-transition-image2 n2-ow'
        ));

        $html .= N2Html::closeTag("div");

        $html .= N2Html::closeTag("a");

        $html .= N2Html::scriptTemplate($this->getJs($slider->elementId, "{uid}"));

        $html .= N2Html::closeTag("div");

        return $html;
    }

    function getJs($sliderId, $id) {
        return '
    if(typeof window.ssitemmarker == "undefined"){
        new NextendSmartSliderTransitionItem(window["' . $sliderId . '"], "' . $id . '", "{animation}");
    }';
    }

    function _render($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide, false);
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return $this->getHtml($data, $itemId, $slider, $slide, true);
    }

    private function getHtml($data, $id, $slider, $slide, $isAdmin = false) {

        $this->loadResources($slider);
        $slider->features->addInitCallback('new NextendSmartSliderTransitionItem(arguments[0], "' . $id . '", "' . $data->get('animation', 'Fade') . '");');

        $html = N2Html::openTag("div", array(
            "class" => "n2-ss-item-transition-inner"
        ));
        $html .= N2Html::tag('img', self::optimizeImage($slide->fill($data->get('image', '')), $data, $slider) + array(
                'alt'   => htmlspecialchars($slide->fill($data->get('alt', ''))),
                'class' => 'n2-ss-item-transition-image1 n2-ow'
            ));
        $html .= N2Html::tag('img', self::optimizeImage($slide->fill($data->get('image2', '')), $data, $slider) + array(
                'alt'   => htmlspecialchars($slide->fill($data->get('alt', ''))),
                'class' => 'n2-ss-item-transition-image2 n2-ow'
            ));
        $html .= N2Html::closeTag('div');

        $linkAttributes = array('class' => 'n2-ow');
        if ($isAdmin) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return N2Html::tag("div", array(
            "id"    => $id,
            "class" => "n2-ss-item-transition"
        ), $this->getLink($slide, $data, $html, $linkAttributes));
    }

    private function loadResources($slider) {

        N2LESS::addFile(N2Filesystem::translate($this->getPath() . "/transition.n2less"), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);
    }

    function getValues() {
        return array(
            'animation'      => 'Fade',
            'image'          => '$system$/images/placeholder/imagefront.png',
            'image2'         => '$system$/images/placeholder/imageback.png',
            'alt'            => n2_('Image not available'),
            'link'           => '#|*|_self',
            'image-optimize' => 1
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('image2', $slide->fill($data->get('image2', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addImage($data->get('image2'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {

        $data->set('image', $import->fixImage($data->get('image', '')));
        $data->set('image2', $import->fixImage($data->get('image2', '')));
        $data->set('link', $import->fixLightbox($data->get('link')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemTransition');
