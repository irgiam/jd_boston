<?php
class N2SmartSliderFeatureBackgroundVideo
{

    private $slider;

    public function __construct($slider) {

        $this->slider = $slider;
    }

    /**
     * @param $slide N2SmartSliderSlide
     *
     * @return string
     */
    public function make($slide) {
        $mp4  = $slide->fill($slide->parameters->get('backgroundVideoMp4', ''));
        $webm = $slide->fill($slide->parameters->get('backgroundVideoWebm', ''));
        $ogg  = $slide->fill($slide->parameters->get('backgroundVideoOgg', ''));

        if (empty($mp4) && empty($webm) && empty($ogg)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= N2Html::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ), '', false);
        }

        if ($webm) {
            $sources .= N2Html::tag("source", array(
                "src"  => $webm,
                "type" => "video/webm"
            ), '', false);
        }

        if ($ogg) {
            $sources .= N2Html::tag("source", array(
                "src"  => $ogg,
                "type" => "video/ogg"
            ), '', false);
        }

        $attributes = array();

        if ($slide->parameters->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($slide->parameters->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }
        
        $attributes["preload"] = $slide->parameters->get("preload","auto");

        $backgroundColor = '';
        $color           = $slide->parameters->get('backgroundColor', '');
        if (strlen($color) == 8 && substr($color, 6, 2) != '00') {
            if (!class_exists('N2Color')) {
                N2Loader::import("libraries.image.color");
            }

            $rgba    = N2Color::hex2rgba($color);
            $rgba[3] = round($rgba[3] / 127, 2);
            $backgroundColor .= "background-color: RGBA({$rgba[0]}, {$rgba[1]}, {$rgba[2]}, {$rgba[3]});";
        }

        return N2Html::tag('video', $attributes + array(
                'class'           => 'n2-ss-slide-background-video',
                'data-mode'       => $slide->parameters->get('backgroundVideoMode', 'fill'),
                'data-background' => $backgroundColor
            ), $sources);
    }

}
