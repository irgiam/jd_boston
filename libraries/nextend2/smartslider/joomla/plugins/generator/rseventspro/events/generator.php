<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorRSEventsProEvents extends N2GeneratorAbstract
{

    private function formatDate($datetime, $format = false, $dateOrTime = 0)
    {
        switch ($dateOrTime) {
            case 0:
                $dot = $format;
                break;
            case 1:
                $dot = $format;
                break;
        }
        if ($dateOrTime == 1 || $datetime != '0000-00-00 00:00:00') {
            return date($dot, strtotime($datetime));
        } else {
            return '0000-00-00';
        }
    }

    protected function _getData($count, $startIndex)
    {
        require_once(JPATH_SITE . '/components/com_rseventspro/helpers/rseventspro.php');
        require_once(JPATH_SITE . '/components/com_rseventspro/helpers/route.php');

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $groups     = array_map('intval', explode('||', $this->data->get('sourcegroups', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('sourcetags', '')));
        $locations  = array_map('intval', explode('||', $this->data->get('sourcelocations', '')));

        $model = new N2Model('rseventspro_events');

        $where = array('re.published <> 0');

        if (!in_array('0', $categories)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $categories) . ") AND type = 'category')";
        }

        if (!in_array('0', $groups)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $groups) . ") AND type = 'groups')";
        }

        if (!in_array('0', $tags)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $tags) . ") AND type = 'tag')";
        }

        if (!in_array('0', $locations)) {
            $where[] = "re.location IN (" . implode(', ', $locations) . ")";
        }

        $today = date('Y-m-d h:i:s', time());

        switch ($this->data->get('started', '0')) {
            case 1:
                $where[] = "re.start < '" . $today . "'";
                break;
            case -1:
                $where[] = "re.start >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $where[] = "re.end < '" . $today . "'";
                break;
            case -1:
                $where[] = "re.end >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('allday', '0')) {
            case 1:
                $where[] = "re.allday = 1";
                break;
            case -1:
                $where[] = "re.allday = 0";
                break;
        }

        switch ($this->data->get('recurring', '0')) {
            case 1:
                $where[] = "re.recurring = 1";
                break;
            case -1:
                $where[] = "re.recurring = 0";
                break;
        }

        $query = 'SELECT
        re.start, re.end, re.id, re.name, re.description, re.created, re.URL, re.email, re.phone, re.metaname, re.metakeywords, re.metadescription, re.hits, re.icon, 
        rl.name as loc_name, rl.url as loc_url, rl.address, rl.description AS loc_description, rl.coordinates
        FROM #__rseventspro_events AS re
        LEFT JOIN #__rseventspro_locations AS rl ON re.location = rl.id
        WHERE ' . implode(' AND ', $where) . ' ';

        $order = N2Parse::parse($this->data->get('rseventsproorder', 'start|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY re.' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data       = array();
        $root       = N2Uri::getBaseUri();
        $dateFormat = $this->data->get('rseventsprodate', 'Y-m-d');
        $timeFormat = $this->data->get('rseventsprotime', 'H:i:s');
        foreach ($result AS $res) {
            $r = array(
                'title' => $res['name'],
                'description' => $res['description']
            );

            if (isset($res['icon'])) {
                $res['icon'] = 'components/com_rseventspro/assets/images/events/' . $res['icon'];
            } else {
                $res['icon'] = '';
            }

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($root . "/", array(
                @$res['icon']
            ), array(
                @$res['description']
            ));

            $r += array(
                'start_date' => $this->formatDate($res['start'], $dateFormat),
                'start_time' => $this->formatDate($res['start'], $timeFormat, 1),
                'end_date' => $this->formatDate($res['end'], $dateFormat),
                'end_time' => $this->formatDate($res['end'], $timeFormat, 1),
                'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($res['id'], $res['name']), true, RseventsproHelperRoute::getEventsItemid()),
                'created' => $res['created'],
                'website' => $res['URL'],
                'email' => $res['email'],
                'phone' => $res['phone'],
                'metaname' => $res['metaname'],
                'metakeywords' => $res['metakeywords'],
                'metadescription' => $res['metadescription'],
                'hits' => $res['hits'],
                'id' => $res['id'],
                'location_name' => $res['loc_name'],
                'location_url' => $res['loc_url'],
                'location_address' => $res['address'],
                'location_description' => $res['loc_description']
            );

            $coordinates = explode(',', $res['coordinates']);
            if (count($coordinates) == 2) {
                $r += array(
                    'location_coordinates_lat' => $coordinates[0],
                    'location_coordinates_long' => $coordinates[1]
                );
            }

            $data[] = $r;
        }
        return $data;
    }
}
