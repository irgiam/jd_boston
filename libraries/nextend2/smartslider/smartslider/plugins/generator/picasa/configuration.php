<?php

class N2SliderGeneratorPicasaConfiguration
{

    private $configuration;

    /**
     * @param $info N2GeneratorInfo
     */
    public function __construct($info) {
        $this->configuration = new N2Data(array(
            'userID' => ''
        ));

        $this->configuration->loadJSON(N2Base::getApplication('smartslider')->storage->get('picasa'));
    }

    public function wellConfigured() {
        if (!$this->configuration->get('userID')) {
            return false;
        } else {
            return true;
        }
    }

    public function getData() {
        return $this->configuration->toArray();
    }

    public function addData($data, $user = true) {
        $this->configuration->loadArray($data);
        if ($user) {
            N2Base::getApplication('smartslider')->storage->set('picasa', null, json_encode($this->configuration->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $form->loadXMLFile(dirname(__FILE__) . '/configuration.xml');

        echo $form->render('generator');
    }

    public function getUserID() {
        return $this->configuration->get('userID');
    }
}
