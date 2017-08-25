<?php
N2Loader::import(array(
    'libraries.layout.storage'
), 'smartslider');


class N2SmartSliderLayoutModel extends N2SystemVisualModel
{

    public $type = 'layout';

    public function __construct($tableName = null) {

        parent::__construct($tableName);
        $this->storage = N2Base::getApplication('smartslider')->storage;
    }

    protected function getPath() {
        return dirname(__FILE__);
    }

    public function addVisual($setId, $visual) {
        // Might not needed
        N2Loader::import("libraries.slider.slide.layer", "smartslider");
        $slide                  = json_decode(base64_decode($visual), true);
        $slide['data']['slide'] = N2SmartSliderLayer::translateIds($slide['data']['slide']);
        return parent::addVisual($setId, base64_encode(json_encode($slide)));
    }
}
