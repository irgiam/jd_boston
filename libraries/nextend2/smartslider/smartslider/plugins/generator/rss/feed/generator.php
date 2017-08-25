<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorRSSFeed extends N2GeneratorAbstract
{

    private function get_http_response_code($url) {
        $headers = @get_headers($url);
        $return  = 'No content received from url:';
        if (is_array($headers)) {
            foreach ($headers AS $h) {
                if (substr($h, 0, 4) == 'HTTP') {
                    $return .= '<br>' . $h;
                }
            }
        } else {
            $return .= "<br>No response headers, as the url wouldn't even exist.";
        }
        return $return;
    }

    protected function _getData($count, $startIndex) {
        $url     = $this->data->get('rssurl', '');
        $content = @file_get_contents($url);
        if ($content === false) {
            N2Message::error(n2_($this->get_http_response_code($url)));
            return null;
        }
        try {
            $xml = new SimpleXmlElement($content);
        } catch (Exception $e) {
            N2Message::error(n2_('The data in the given url is not valid XML.'));
            return null;
        }
        $data = array();
        $i    = 0;
        foreach ($xml->channel->item as $entry) {
            foreach ($entry AS $key => $value) {
                $val = (string)$value;
                if (!empty($val)) {
                    $data[$i][$key] = $val;
                }
            }
            if (!empty($entry->enclosure->attributes()->url)) {
                $data[$i]['enclosure_url'] = (string)$entry->enclosure->attributes()->url;
            }
            $i++;
            if ($i == $count + $startIndex) break;
        }
        $data = array_slice($data, $startIndex, $count);
        return $data;
    }
}
