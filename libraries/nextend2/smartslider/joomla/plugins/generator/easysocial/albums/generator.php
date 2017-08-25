<?php
N2Loader::import('libraries.slider.generator.N2SmartSliderGeneratorAbstract', 'smartslider');

class N2GeneratorEasySocialAlbums extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Albums');

        $groups = array_map('intval', explode('||', $this->data->get('easysocialgroups', '0')));
        $events = array_map('intval', explode('||', $this->data->get('easysocialevents', '0')));

        if (!in_array('0', $groups) && !in_array('0', $events)) {
            $clusters = array_merge($groups, $events);
        } else if (!in_array('0', $groups)) {
            $clusters = $groups;
        } else if (!in_array('0', $events)) {
            $clusters = $events;
        } else {
            $clusters = '';
        }

        if (in_array('0', $groups) && in_array('0', $events)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group' OR cluster_type = 'event')";
        } else if (in_array('0', $groups)) {
            $all = '';
        } else if (in_array('0', $events)) {
            $all = "OR uid IN (SELECT cluster_id FROM #__social_events_meta WHERE group_id IN (" . implode(',', $groups) . "))";
        } else {
            $all = "OR uid IN (SELECT cluster_id FROM #__social_events_meta WHERE group_id IN (" . implode(',', $groups) . ") AND cluster_id IN(" . implode(',', $events) . "))";
        }

        $albumWhere = array("(type='event' OR type='group')");

        if ($clusters != '') {
            $albumWhere[] = "(uid IN (" . implode(',', $clusters) . ") " . $all . ")";
        }

        if ($this->data->get('avatarandcover', '0') == '0') {
            $albumWhere[] = "title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR' AND title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER'";
        }

        $albumTitle = $this->data->get('albumtitle', '*');
        if ($albumTitle != '*' && !empty($albumTitle)) {
            $albumWhere[] = "title = '" . $albumTitle . "'";
        }

        $where = array(
            "a.album_id IN (SELECT id FROM #__social_albums WHERE  " . implode(' AND ', $albumWhere) . ")",
            "a.state = 1"
        );

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'a.featured = 1';
                break;
            case -1:
                $where[] = 'a.featured = 0';
                break;
        }

        $query = "SELECT
                  a.title,
                  b.value AS original,
                  c.value AS thumbnail,
                  d.value AS square,
                  e.value AS featured,
                  f.value AS large,
                  g.value AS stock
                  FROM #__social_photos AS a
                  LEFT JOIN #__social_photos_meta AS b ON a.id = b.photo_id
                  LEFT JOIN #__social_photos_meta AS c ON a.id = c.photo_id
                  LEFT JOIN #__social_photos_meta AS d ON a.id = d.photo_id
                  LEFT JOIN #__social_photos_meta AS e ON a.id = e.photo_id
                  LEFT JOIN #__social_photos_meta AS f ON a.id = f.photo_id
                  LEFT JOIN #__social_photos_meta AS g ON a.id = g.photo_id
                  WHERE " . implode(' AND ', $where) . "
                  AND b.property = 'original' AND b.group = 'path'
                  AND c.property = 'thumbnail' AND c.group = 'path'
                  AND d.property = 'square' AND d.group = 'path'
                  AND e.property = 'featured' AND e.group = 'path'
                  AND f.property = 'large' AND f.group = 'path'
                  AND g.property = 'stock' AND g.group = 'path'
                  LIMIT " . $startIndex . ", " . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'     => $result[$i]['title'],
                'image'     => N2ImageHelper::dynamic($root . $result[$i]['original']),
                'thumbnail' => N2ImageHelper::dynamic($root . $result[$i]['thumbnail']),
                'square'    => N2ImageHelper::dynamic($root . $result[$i]['square']),
                'featured'  => N2ImageHelper::dynamic($root . $result[$i]['featured']),
                'large'     => N2ImageHelper::dynamic($root . $result[$i]['large']),
                'stock'     => N2ImageHelper::dynamic($root . $result[$i]['stock']),
            );

            $data[] = $r;
        }

        return $data;
    }
}
