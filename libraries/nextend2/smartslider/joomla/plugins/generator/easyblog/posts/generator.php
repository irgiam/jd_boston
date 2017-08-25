<?php
N2Loader::import('libraries.slider.generator.N2SmartSliderGeneratorAbstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../imagefallback.php');

class N2GeneratorEasyBlogPosts extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasyBlog_Post');

        $category = array_map('intval', explode('||', $this->data->get('easyblogcategories', '')));

        $query = 'SELECT con.*, con.intro as "main_content_of_post", con.content as "rest_of_the_post", usr.nickname as "blogger", usr.avatar as "blogger_avatar_picture", cat.title as cat_title ';

        /* id 	created_by 	title 	description 	alias 	avatar 	parent_id 	private 	created 	status 	published 	ordering 	level 	lft 	rgt 	default */

        $query .= 'FROM #__easyblog_post con ';

        $query .= 'LEFT JOIN #__easyblog_users usr ON usr.id = con.created_by ';

        $query .= 'LEFT JOIN #__easyblog_category cat ON cat.id = con.category_id ';

        $jnow  = JFactory::getDate();
        $now   = $jnow->toSql();
        $where = array("con.published = 1 AND (con.publish_up = '0000-00-00 00:00:00' OR con.publish_up < '" . $now . "') AND (con.publish_down = '0000-00-00 00:00:00' OR con.publish_down > '" . $now . "') ");
        
        $exclude = $this->data->get('easyblogexclude', '');
        if(!empty($exclude)){
          $where[] = ' con.id NOT IN (' . $exclude . ') ';
        }
        
        if (!in_array('0', $category)) {
            $where[] = 'con.category_id IN (' . implode(',', $category) . ') ';
        }

        $tags = array_map('intval', explode('||', $this->data->get('easyblogtags', '0')));

        if (!in_array(0, $tags)) {
            $where[] = 'con.id IN (SELECT post_id FROM #__easyblog_post_tag WHERE tag_id IN(' . implode(',', $tags) . '))';
        }

        switch ($this->data->get('easyblogfrontpage', 0)) {
            case 1:
                $where[] = "con.frontpage = 1 ";
                break;
            case -1:
                $where[] = "con.frontpage = 0 ";
                break;
        }

        switch ($this->data->get('easyblogfeatured', 0)) {
            case 1:
                $where[] = "con.id IN (SELECT content_id FROM #__easyblog_featured WHERE type = 'post')";
                break;
            case -1:
                $where[] = "con.id NOT IN (SELECT content_id FROM #__easyblog_featured WHERE type = 'post')";
                break;
        }

        $sourceUserId = intval($this->data->get('easybloguserid', ''));
        if (!empty($sourceUserId)) {
            $where[] = 'con.created_by = ' . $sourceUserId . ' ';
        }

        $where[] = " con.state = 0 ";

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = N2Parse::parse($this->data->get('easyblogorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);

        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {
            $description = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $result[$i]['main_content_of_post']);

            $url = 'index.php?option=com_easyblog&view=entry&id=' . $result[$i]['id'];
            if(class_exists('EBR', false)){
                $url = EBR::_($url, true, null, false, false, false);
            }

            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $description,
                'url'         => $url,
            );

            $img = explode('/', $result[$i]['image']);
            if (isset($img[1])) {
                $path     = explode(':', $img[0]);
                $fullRoot = $root . "/images/easyblog_articles/" . $path[1] . "/";
                $image    = $img[1];
            } else {
                $fullRoot = $root;
                $image    = '';
            }

            $r['image'] = $r['thumbnail'] = NextendImageFallBack::fallback($fullRoot, array($image), array($result[$i]['content']));
            $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $result[$i]['content']);

            $category_url= 'index.php?option=com_easyblog&view=categories&id=' . $result[$i]['category_id'];
            if(class_exists('EBR', false)){
                $category_url = EBR::_($category_url);
            }
            
            $r += array(
                'url_label'              => sprintf(n2_('View %s'), n2_('post')),
                'category_url'           => $category_url,
                'category_title'         => $result[$i]['cat_title'],
                'blogger'                => $result[$i]['blogger'],
                'blogger_avatar_picture' => ($result[$i]['blogger_avatar_picture'] == "default_blogger.png" ? "components/com_easyblog/assets/images/" . $result[$i]['blogger_avatar_picture'] : "images/easyblog/avatar/" . $result[$i]['blogger_avatar_picture']),
                'created_by_id'          => $result[$i]['created_by'],
                'creation_time'          => $result[$i]['created'],
                'modification_time'      => $result[$i]['modified'],
                'content'                => $content,
                'latitude'               => $result[$i]['latitude'],
                'longitude'              => $result[$i]['longitude'],
                'address'                => $result[$i]['address'],
                'hits'                   => $result[$i]['hits'],
                'category_id'            => $result[$i]['category_id'],
                'id'                     => $result[$i]['id'],
            );
            $data[] = $r;
        }
        return $data;
    }
}
