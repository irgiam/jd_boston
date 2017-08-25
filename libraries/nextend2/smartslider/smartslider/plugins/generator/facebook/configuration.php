<?php

class N2SliderGeneratorFacebookConfiguration
{

    private $configuration;

    /**
     * @param $info N2GeneratorInfo
     */
    public function __construct($info) {
        $this->configuration = new N2Data(array(
            'appId'       => '',
            'secret'      => '',
            'accessToken' => ''
        ));

        $this->configuration->loadJSON(N2Base::getApplication('smartslider')->storage->get('facebook'));

    }

    public function wellConfigured() {
        if (!$this->configuration->get('appId') || !$this->configuration->get('secret') || !$this->configuration->get('accessToken')) {
            return false;
        }
        $api = $this->getApi();
        try {
            $api->api('/me');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getApi() {
        require_once(dirname(__FILE__) . "/api/facebook.php");

        $facebook = new Facebook(array(
            'appId'  => $this->configuration->get('appId'),
            'secret' => $this->configuration->get('secret'),
        ));

        $accessToken = $this->configuration->get('accessToken');
        if ($accessToken) {
            $facebook->setAccessToken($accessToken);
        }

        return $facebook;
    }

    public function getData() {
        return $this->configuration->toArray();
    }

    public function addData($data, $store = true) {
        $this->configuration->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('facebook', null, json_encode($this->configuration->toArray()));
        }
    }

    public function render() {
        $form = new N2Form();
        $form->loadArray($this->getData());

        $form->loadXMLFile(dirname(__FILE__) . '/configuration.xml');

        echo $form->render('generator');

        try {
            $api    = $this->getApi();
            $result = $api->api('debug_token', array(
                'input_token' => $api->getAccessToken()
            ));
            if (isset($result['data']['expires_at']) && $result['data']['is_valid']) {
                N2Message::notice('The token will expire on ' . date('F j, Y', $result['data']['expires_at']));
            } else {
                N2Message::error(n2_('The token expired. Please request new token! '));
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        return $this->getApi()
                    ->getLoginUrl(array(
                        'redirect_uri' => N2Base::getApplication('smartslider')->router->createUrl(array(
                            "generator/finishAuth",
                            array(
                                'group' => N2Request::getVar('group'),
                                'type'  => N2Request::getVar('type')
                            )
                        )),
                        'scope'        => 'user_photos',
                        'display'      => 'popup'
                    ));
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $api  = $this->getApi();
            $user = $api->api('/me');
            if (!$user) {
                return false;
            } else {
                $api->setExtendedAccessToken();
                $data                = $this->getData();
                $data['accessToken'] = $api->getAccessToken();
                $api->destroySession();
                $this->addData($data);
                return true;
            }
            return false;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getAlbums() {
        $ID     = N2Request::getVar('facebookID');
        $result = $this->getApi()
                       ->api($ID . '/albums');
        $albums = array();
        if (count($result['data'])) {
            foreach ($result['data'] AS $album) {
                $albums[$album['id']] = $album['name'];
            }
        }
        return $albums;
    }

}