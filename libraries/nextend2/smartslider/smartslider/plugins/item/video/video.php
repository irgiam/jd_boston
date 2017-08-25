<?php
N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemVideo extends N2SSPluginItemAbstract {

    public $_identifier = 'video';

    protected $layerProperties = array(
        "width"  => 300,
        "height" => 180
    );

    protected $priority = 20;

    protected $group = 'Media';

    public function __construct() {
        $this->_title = n2_x('Video', 'Slide item');
    }

    function getTemplate($slider) {
        return N2Html::tag('div', array(
            "style" => 'width: 100%; height: 100%; min-height: 50px; background: url(' . N2ImageHelper::fixed('$system$/images/placeholder/video.png') . ') no-repeat 50% 50%; background-size: cover;'
        ));
    }

    function _render($data, $itemId, $slider, $slide) {
        N2JS::addInline('window["' . $slider->elementId . '"].ready(function(){
        var video = new NextendSmartSliderVideoItem(this, "' . $itemId . '", ' . $data->toJSON() . ');
    });');


        return N2Html::tag("video", $this->_setVideoOptions($data, $itemId), $this->_setVideoContent($slide, $data));
    }

    function _renderAdmin($data, $itemId, $slider, $slide) {
        return N2Html::tag('div', array(
            "style" => 'width: 100%; height: 100%; background: url(' . N2ImageHelper::fixed('$system$/images/placeholder/video.png') . ') no-repeat 50% 50%; background-size: cover;'
        ));
    }

    private function _setVideoOptions($data, $id) {
        $videoOptions = array(
            'style'  => 'width: 100%; height: 100%;',
            'class'  => 'n2-ow',
            'encode' => false
        );

        $videoOptions["data-volume"] = $data->get("volume", 1);


        $videoOptions["id"] = $id;

        if ($data->get("showcontrols")) {
            $videoOptions["controls"] = "yes";
        }

        $videoOptions["preload"] = $data->get("preload", "auto");

        return $videoOptions;
    }

    private function _setVideoContent($slide, $data) {
        $videoContent = "";

        if ($data->get("video_mp4", false)) {
            $videoContent .= N2Html::tag("source", array(
                "src"  => $slide->fill($data->get("video_mp4")),
                "type" => "video/mp4"
            ), '', false);
        }

        if ($data->get("video_webm", false)) {
            $videoContent .= N2Html::tag("source", array(
                "src"  => $slide->fill($data->get("video_webm")),
                "type" => "video/webm"
            ), '', false);
        }

        if ($data->get("video_ogg", false)) {
            $videoContent .= N2Html::tag("source", array(
                "src"  => $slide->fill($data->get("video_ogg")),
                "type" => "video/ogg"
            ), '', false);
        }

        return $videoContent;
    }

    /**
     * @return array
     */
    function getValues() {
        return array(
            'autoplay'     => 0,
            'video_mp4'    => '',
            'video_webm'   => '',
            'video_ogg'    => '',
            'showcontrols' => 1,
            'volume'       => 1,
            'autoplay'     => 0,
            'center'       => 0,
            'loop'         => 0,
            'reset'        => 0,
            'videoplay'    => '',
            'videopause'   => '',
            'videoend'     => ''
        );
    }

    /**
     * @return string
     */
    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('video_mp4', $slide->fill($data->get('video_mp4', '')));
        $data->set('video_webm', $slide->fill($data->get('video_webm', '')));
        $data->set('video_ogg', $slide->fill($data->get('video_ogg', '')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemVideo');
