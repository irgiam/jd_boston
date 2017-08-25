<?php

class N2SmartSliderTypeBlock extends N2SmartSliderType {

    public function getDefaults() {
        return array(
            'background'       => '',
            'background-size'  => 'cover',
            'background-fixed' => 0,
            'slider-css'       => '',

            'kenburns-animation' => ''
        );
    }

    protected function renderType() {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-block-type-frontend.min.js', 'smartslider-block-type-frontend');
    

        $background = $params->get('background');
        $css        = $params->get('slider-css');
        if (!empty($background)) {
            $css = 'background-image: url(' . N2ImageHelper::fixed($background) . ');';
        }

        echo $this->openSliderElement();
        ?>

        <div class="n2-ss-slider-1" style="<?php echo $css; ?>">
            <?php
            echo $this->getBackgroundVideo($params);
            ?>
            <div class="n2-ss-slider-2">
                <?php
                echo $this->slider->staticHtml;

                $slide = $this->slider->slides[$this->slider->_activeSlide];
                echo N2Html::tag('div', $slide->attributes + array(
                        'class' => 'n2-ss-slide n2-ss-canvas ' . $slide->classes,
                        'style' => $slide->style
                    ), $slide->background . $slide->getHTML());
                ?>
            </div>
        </div>
        <?php
        $this->widgets->echoRemainder();
        echo N2Html::closeTag('div');

        N2Plugin::callPlugin('nextendslider', 'onNextendSliderProperties', array(&$this->javaScriptProperties));

        N2JS::addFirstCode("new NextendSmartSliderBlock('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");");

        echo N2Html::clear();
    }

    private function getBackgroundVideo($params) {
        $mp4  = $params->get('backgroundVideoMp4', '');
        $webm = $params->get('backgroundVideoWebm', '');
        $ogg  = $params->get('backgroundVideoOgg', '');

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

        $attributes = array(
            'autoplay' => 1
        );

        if ($params->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($params->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        return N2Html::tag('div', array('class' => 'n2-ss-slider-background-video-container'), N2Html::tag('video', $attributes + array(
                'class'     => 'n2-ss-slider-background-video',
                'data-mode' => $params->get('backgroundVideoMode', 'fill')
            ), $sources));

    }

    /**
     * @param $params N2Data
     */
    public function limitParams($params) {

        $params->loadArray(array(
            'controlsScroll'          => 0,
            'controlsDrag'            => 0,
            'controlsTouch'           => 0,
            'controlsKeyboard'        => 0,
            'controlsTilt'            => 0,
            'autoplay'                => 0,
            'autoplayStart'           => 0,
            'widgetarrow'             => 'disabled',
            'widgetbullet'            => 'disabled',
            'widgetautoplay'          => 'disabled',
            'widgetindicator'         => 'disabled',
            'widgetbar'               => 'disabled',
            'widgetthumbnail'         => 'disabled',
            'widgetshadow'            => 'disabled',
            'widgethtml'              => 'disabled',
            'randomize'               => 0,
            'randomizeFirst'          => 0,
            'randomize-cache'          => 0,
            'maximumslidecount'       => 1,
            'imageload'               => 0,
            'imageloadNeighborSlides' => 0,
            'maintain-session'        => 0
        ));
    }
}

