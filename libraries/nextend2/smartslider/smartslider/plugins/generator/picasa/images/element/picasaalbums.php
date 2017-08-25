<?php
N2Loader::import('libraries.form.element.list');

class N2ElementPicasaalbums extends N2ElementList
{

    function fetchElement() {
        $configuration = $this->_form->get('info')
                                     ->getConfiguration();
        $user          = $configuration->getUserID();

        $albumlist      = "http://picasaweb.google.com/data/feed/api/user/" . $user . "?kind=album";
        $sxml_albumlist = @simplexml_load_file($albumlist);
        $this->_xml->addChild('option', 'Please select')
                   ->addAttribute('value', 0);

        if ($sxml_albumlist === false) {
            N2Message::notice("There are no albums for this user.");
        } else {
            foreach ($sxml_albumlist->entry as $album_list) {
                if (count($album_list)) {
                    $value = str_replace("http://picasaweb.google.com/data/entry/api", "", $album_list->id);
                    $this->_xml->addChild('option', htmlentities(" - " . $album_list->title))
                               ->addAttribute('value', $value);
                }
            }
        }
        return parent::fetchElement();
    }

}