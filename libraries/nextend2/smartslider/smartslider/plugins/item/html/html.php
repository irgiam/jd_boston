<?php
N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemHTML extends N2SSPluginItemAbstract {

    var $_identifier = 'html';

    protected $priority = 40;

    protected $layerProperties = array("width" => 200);

    public function __construct() {
        $this->_title = n2_x('HTML', 'Slide item');
    }

    function getTemplate($slider) {
        return '
    <div>
        {html}
        <style type="text/css">
          {css}
        </style>
    </div>
    ';
    }

    function _render($data, $id, $slider, $items) {
        return $this->getHtml($data, $id, $slider, $items);
    }

    function _renderAdmin($data, $id, $slider, $items) {
        return $this->getHtml($data, $id, $slider, $items);
    }

    private function getHtml($data, $id, $slider, $slide) {
        $css = '';
        if ($cssCode = $data->get('css', '')) {
            $css = N2Html::style($cssCode);
        }
        return N2Html::tag("div", array(), $this->closeTags($slide->fill($data->get("html"))) . $css);
    }

    function closeTags($html) {

        if (class_exists('tidy', false)) {
            $tidy_config = array(
                'input-xml'  => true,
                'output-xml' => true,
                'show-body-only'      => true,
                'wrap'                => 0,
                'new-blocklevel-tags' => 'menu,mytag,article,header,footer,section,nav,svg,path,g,a',
                'new-inline-tags'     => 'video,audio,canvas,ruby,rt,rp',
                'doctype'             => '<!DOCTYPE HTML>',
            );
            $tidy        = new tidy();
            return $tidy->repairString($html, $tidy_config, 'UTF8');
        }
        return $html;
    }

    function getValues() {
        return array(
            'html' => '<table  class="my-table">
<tbody><tr>
<th>First Name</th>
<th>Last Name</th>
<th>Points</th>
</tr>
<tr>
<td>Eve</td>
<td>Jackson</td>
<td>94</td>
</tr>
<tr>
<td>John</td>
<td>Doe</td>
<td>80</td>
</tr>
<tr>
<td>Adam</td>
<td>Johnson</td>
<td>67</td>
</tr>
<tr>
<td>Jill</td>
<td>Smith</td>
<td>50</td>
</tr>
</tbody></table>',
            'css'  => 'table.my-table{
width: 100%;
background: #0c92df;
color: white;
}

table.my-table th,
table.my-table td{
padding: 5px;
text-align: left;
}'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('html', $slide->fill($data->get('html', '')));
        return $data;
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemHTML');
