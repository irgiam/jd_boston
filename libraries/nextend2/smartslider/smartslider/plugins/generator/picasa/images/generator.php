<?php

N2Loader::import('libraries.slider.generator.N2SmartSliderGeneratorAbstract', 'smartslider');

class N2GeneratorPicasaImages extends N2GeneratorAbstract
{

    function _getData($count, $startIndex) {
        $albums = $this->data->get('picasaalbums', '');
        if (strpos($albums, 'http://picasaweb.google.com/data/feed/api') !== false) {
            $album = $albums;
        } else {
            $album = 'http://picasaweb.google.com/data/feed/api' . $albums;
        }
        $random = $this->data->get('picasarandom', 0);
        if (!$random) {
            $urlVars = "?start-index=" . ($startIndex + 1) . "&max-results=" . $count;
            $album .= $urlVars;
        }

        if ($album != '') {
            $explode = explode("/", $album);
            $user_id = $explode[7];
            if (!$random) {
                $album_id = str_replace($urlVars, '', end($explode));
            } else {
                $album_id = end($explode);
            }
            $sxml_album = @simplexml_load_file($album);
            if ($sxml_album === false) {
                N2Message::error("No images were returned for this album.");
                return null;
            } else {
                $i    = 0;
                $j    = 0;
                $from = array(
                    "T",
                    "Z"
                );
                $to   = array(
                    " ",
                    ""
                );

                if ($random) {
                    $numbers = range(0, count($sxml_album->entry) - 1);
                    shuffle($numbers);
                    $randomArray = array_slice($numbers, 0, $count);
                }

                foreach ($sxml_album->entry as $album_photo) {
                    if (!$random || ($random && in_array($j, $randomArray))) {
                        $media                 = $album_photo->children('http://search.yahoo.com/mrss/');
                        $linkName              = (string)$media->group->content->attributes()->{'url'};
                        $data[$i]['image']     = $linkName . "?sz=0";
                        $data[$i]['thumbnail'] = $linkName;
                        $image_data            = get_object_vars($album_photo);

                        if (!is_object($image_data['summary'])) {
                            $data[$i]['title'] = $data[$i]['description'] = $image_data['summary'];
                        } else {
                            $data[$i]['title'] = $data[$i]['description'] = "";
                        }
                        $data[$i]['published'] = str_replace($from, $to, substr($image_data['published'], 0, -5));
                        $data[$i]['updated']   = str_replace($from, $to, substr($image_data['updated'], 0, -5));

                        $url                             = $image_data['link'][1]->attributes();
                        $data[$i]['url']                 = (string)$url['href'];
                        $imageUrl                        = $image_data['link'][2]->attributes();
                        $data[$i]['image_alternate_url'] = (string)$imageUrl['href'];
                        $albumUrl                        = explode('#', $url['href']);
                        $data[$i]['album_url']           = $albumUrl[0];

                        $albumID                           = explode("/", $album_photo->id);
                        $image_id                          = end($albumID);
                        $data[$i]['google_plus_url']       = "https://plus.google.com/photos/" . $user_id . "?pid=" . $image_id . "&oid=" . $user_id;
                        $data[$i]['google_plus_album_url'] = "https://plus.google.com/photos/" . $user_id . "/albums/" . $album_id;
                        $i++;
                    }
                    $j++;
                }
            }
            if ($random) {
                shuffle($data);
            }
            return $data;
        } else {
            N2Message::error("There isn't an album selected.");
            return null;
        }
    }
}
