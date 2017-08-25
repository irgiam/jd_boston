<?php
N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemIFrame extends N2SSPluginItemAbstract
{

    var $_identifier = 'iframe';

    protected $priority = 9;

    protected $layerProperties = array(
        "width"  => 300,
        "height" => 300
    );

    public function __construct() {
        $this->_title = n2_x('iframe', 'Slide item');
    }

    function getTemplate($slider) {
        return N2Html::tag("iframe", array(
            "encode"      => false,
            "frameborder" => 0,
            "class"       => "n2-ow",
            "width"       => "{width}",
            "height"      => "{height}",
            "src"         => "{url}",
            "style"       => "min-height: 50px;"
        ), "");
    }

    function _render($data, $id, $slider, $slide) {

        $size = (array)N2Parse::parse($data->get('size', ''));
        if (!isset($size[0])) $size[0] = '100%';
        if (!isset($size[1])) $size[1] = '100%';

        return N2Html::tag("iframe", array(
            "encode"      => false,
            "frameborder" => 0,
            "class"       => "n2-ow",
            "width"       => $size[0],
            "height"      => $size[0],
            "src"         => $slide->fill($data->get("url")),
            "scrolling"   => $data->get("scroll")
        ), "");
    }

    function _renderAdmin($data, $id, $slider, $slide) {
        return $this->_render($data, $id, $slider, $slide);
    }

    function getValues() {
        return array(
            'url'    => 'http://www.nextendweb.com',
            'size'   => '100%|*|100%',
            'scroll' => 'yes'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('url', $slide->fill($data->get('url', '')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemIFrame');
