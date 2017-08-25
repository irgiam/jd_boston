<?php
N2Loader::import('libraries.slider.generator.N2SmartSliderGeneratorAbstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorEasySocialGroups extends N2GeneratorAbstract
{

    private function checkImage($url, $image) {
        if (isset($image)) {
            return N2ImageHelper::dynamic($url . $image);
        } else {
            return '';
        }
    }

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Groups');

        $where = array(
            "a.parent_id = 0",
            "a.cluster_type = 'group'",
            "a.state = '1'"
        );

        $category = array_map('intval', explode('||', $this->data->get('easysocialcategories', '')));

        if (!in_array('0', $category)) {
            $where[] = 'a.category_id IN (' . implode(',', $category) . ')';
        }

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'a.featured = 1';
                break;
            case -1:
                $where[] = 'a.featured = 0';
                break;
        }

        $type = $this->data->get('grouptype', 0);
        if ($type != 0) {
            $where[] = 'a.type = ' . $type;
        }

        $location = $this->data->get('location', '*');
        if ($location != '*' && !empty($location)) {
            $where[] = "a.address = '" . $location . "'";
        }

        $query = "SELECT
                  a.title, a.description, a.address, a.longitude, a.latitude, a.created, a.alias, a.category_id, a.id, a.alias,
                  c.small, c.medium, c.square, c.large, c.uid AS avatar_folder,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'original') AS original_cover,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'thumbnail') AS thumbnail_cover,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'square') AS square_cover,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'featured') AS featured_cover,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'large') AS large_cover,
                  (SELECT value FROM #__social_photos_meta WHERE photo_id = d.id AND property = 'stock') AS stock_cover
                  FROM #__social_clusters AS a
                  LEFT JOIN #__social_avatars AS c ON c.uid = a.id
                  LEFT JOIN #__social_photos AS d ON d.uid = a.id AND (d.album_id IN (SELECT id FROM #__social_albums WHERE title = 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER') OR d.album_id IS NULL)
                  WHERE " . implode(' AND ', $where) . "  ";

        $order = N2Parse::parse($this->data->get('easysocialorder', 'a.created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result     = $model->db->queryAll($query);
        $root       = N2Uri::getBaseUri();
        $avatarRoot = $root . "/media/com_easysocial/avatars/group/";

        if (!class_exists('FRoute')) {
            if (file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'easysocial.php')) {
                require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'easysocial.php');
            }
            require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'router.php');
        }

        $urlOptions = array(
            'layout'   => 'item',
            'external' => false,
            'sef'      => true
        );

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $urlOptions['id'] = $result[$i]['id'];

            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $result[$i]['description']
            );

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($root, array(
                @$result[$i]['original_cover'],
                isset($result[$i]['large']) ? "/media/com_easysocial/avatars/group/" . $result[$i]['avatar_folder'] . "/" . $result[$i]['large'] : ''
            ), array());

            $r += array(
                'thumbnail'           => $this->checkImage($root, $result[$i]['thumbnail_cover']),
                'square_image'        => $this->checkImage($root, $result[$i]['square_cover']),
                'featured_image'      => $this->checkImage($root, $result[$i]['featured_cover']),
                'large_image'         => $this->checkImage($root, $result[$i]['large_cover']),
                'stock_image'         => $this->checkImage($root, $result[$i]['stock_cover']),
                'avatar_small_image'  => $this->checkImage($avatarRoot . $result[$i]['avatar_folder'] . "/", $result[$i]['small']),
                'avatar_medium_image' => $this->checkImage($avatarRoot . $result[$i]['avatar_folder'] . "/", $result[$i]['medium']),
                'avatar_square_image' => $this->checkImage($avatarRoot . $result[$i]['avatar_folder'] . "/", $result[$i]['square']),
                'avatar_large_image'  => $this->checkImage($avatarRoot . $result[$i]['avatar_folder'] . "/", $result[$i]['large']),
                'url'                 => FRoute::groups($urlOptions, true),
                'creation_time'       => $result[$i]['created'],
                'alias'               => $result[$i]['alias'],
                'category_id'         => $result[$i]['category_id'],
                'id'                  => $result[$i]['id']
            );

            $data[] = $r;
        }

        return $data;
    }
}
