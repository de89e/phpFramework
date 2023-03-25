<?php

class PageModel extends baseModel {

    public $nav = '';
    public $pageURL = '';
    public $urlReplace = '';
    public $page_array = array();
    public $start = 0;
    public $end = 0;
    public $row = 30; //old use $this->showRow
    public $showRow = 30; //the new
    public $total = 100;
    public $current = 1;
    public $totalPage = 1;
    public $show = 8;

    /* like this
     * @page_array(
     *          '10'=>"第10页"
     *          ）
     * pageURL URL
     * How to use
     * $page = new page($pageURL, '#@#');
     * IN CMVC USER init ($pageURL, '#@#')
     * $page->getNav($showRow = 30, $totalRow = 300, $currentPage = 1);
     * echo $page->nav
     *
     *
     * @replace like '#@#'
     * e.g. http://?p=#@#
     */

    /**
     *
     */
    function init($pageURL, $replace, $show = 8) {
        $this->pageURL = $pageURL;
        $this->urlReplace = $replace;
        $this->show = $show;
    }

    function setRow($int, $total = 100) {
        $this->row = $int;
        $this->showRow = $int;
        $this->total = $total;
        $this->totalPage = ceil($total / $int);
        if (($this->totalPage) <= 1) {
            $this->totalPage = 1;
        }
    }

    function setCurrent($int) {

        if ($int < 1)
            $int = 1;

        if ($int > $this->totalPage)
            $int = $this->totalPage;
        $this->current = $int;
        $this->start = ($int - 1) * $this->row;
        $this->end = $this->start + $this->row;
    }

    function getNav($showRow = 30, $totalRow = 300, $currentPage = 1) {
        $this->setRow($showRow, $totalRow);
        $this->setCurrent((int) $currentPage);
        $this->nav .= '<ul>';
        $this->pageFirst();
        $this->pageList();
        $this->pageLast();
        $this->nav .= '</ul>';
        if ($this->totalPage < 2) {
            $this->nav = "";
        }
    }

    function pageFirst() {
        if ($this->totalPage >= $this->show) {
            $this->nav .= "<li><a href=\"" . str_replace($this->urlReplace, 1, $this->pageURL) . "\">首页</a></li>";
        }
    }

    function pageList() {
        $this->setPageArray();
        ksort($this->page_array);
        $this->nav .= "<li><a href=\"" . str_replace($this->urlReplace, ($this->current - 1) > 1 ? $this->current - 1 : 1, $this->pageURL) . "\">上一页</a></li>"; //shang yi ye
        foreach ($this->page_array as $key => $value) {
            $str = str_replace($this->urlReplace, $key, $this->pageURL);
            $this->nav .= "<li><a href=\"{$str}\"";
            if ($key == $this->current) {
                $this->nav .= "class=\"current\"";
            }
            $this->nav .= ">{$value}</a></li>";
        }
        $this->nav .= "<li><a href=\"" . str_replace($this->urlReplace, ($this->current + 1) <= ($this->totalPage) ? ($this->current + 1) : ($this->totalPage), $this->pageURL) . "\">下一页</a></li>"; //xia yi ye
    }

    function pageLast() {
        if ($this->totalPage >= $this->show) {
            $this->nav .= "<li><a href=\"" . str_replace($this->urlReplace, $this->totalPage, $this->pageURL) . "\">尾页</a></li>";
        }
    }

    function setPageArray() {
        $start = $this->current - ceil($this->show / 2);
        if ($start <= 1)
            $start = 1;
        $end = $start + $this->show;
        for (; $start <= $end; $start++) {
            if ($start > $this->totalPage)
                break;
            $this->page_array[$start] = $start;
        }
    }

}
