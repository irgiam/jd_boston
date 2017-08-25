<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorInFolderimages extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        $root   = N2Filesystem::getImagesFolder();
        $folder = N2Filesystem::realpath($root . '/' . ltrim(rtrim($this->data->get('sourcefolder', ''), '/'), '/'));
        $files  = N2Filesystem::files($folder);

        for ($i = count($files) - 1; $i >= 0; $i--) {
            $ext = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
            if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {
                array_splice($files, $i, 1);
            }
        }

        $IPTC = $this->data->get('iptc', 0) && function_exists('exif_read_data');

        $files = array_slice($files, $startIndex);

        $data = array();
        for ($i = 0; $i < $count && isset($files[$i]); $i++) {
            $image    = N2ImageHelper::dynamic(N2Uri::pathToUri($folder . '/' . $files[$i]));
            $data[$i] = array(
                'image'     => $image,
                'thumbnail' => $image,
                'title'     => $files[$i],
                'name'      => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i])
            );
            if ($IPTC) {
                $properties = @exif_read_data($folder . '/' . $files[$i]);
                if ($properties) {
                    foreach ($properties AS $key => $property) {
                        if (!is_array($property) && $property != '' && preg_match('/^[a-zA-Z]+$/', $key)) {
                            $data[$i][$key] = $property;
                        }
                    }
                }
            }
        }
         
        $order = explode("|*|", $this->data->get('order', '0|*|asc'));
        if($order[0]!=0){ 
          usort($data, 'N2GeneratorInFolderimages::' . $order[1]);
        }
        
        return $data;
    }
    
    public static function asc($a, $b){             
        return ($b['title'] < $a['title'] ? 1 : -1);         
    }
    
    public static function desc($a, $b){             
        return ($a['title'] < $b['title'] ? 1 : -1);         
    }   
}