<?php

/**
 * This extension uses the infinite scroll jQuery plugin, from
 * http://www.infinite-scroll.com/ to create an infinite scrolling pagination,
 * like in twitter.
 *
 * It uses javascript to load and parse the new pages, but gracefully degrade
 * in cases where javascript is disabled and the users will still be able to
 * access all the pages.
 *
 * @author davi_alexandre
 * 
 * forked and improved by apostolp
 */
class YiinfiniteScroller extends CBasePager
{

    public $contentSelector = '#content';

    private $_options = array(
        'loadingImg'    => null,
        'loadingText'   => null,
        'loadingImg'    => null,
        'donetext'      => null,
        'itemSelector'  => null,
        'errorCallback' => null,
    );

    private $jsCalback = '';

    private $_default_options = array(
        'navSelector' => 'div.infinite_navigation',
        'nextSelector' => 'div.infinite_navigation a:first',
        'bufferPx' => '50',
    );

    public function init()
    {
        $this->getPages()->validateCurrentPage = FALSE;
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        $this->createInfiniteScrollScript();
        $this->renderNavigation();

        if ($this->getPages()->getPageCount() > 0 && $this->theresNoMorePages()) {
            throw new CHttpException(404);
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_options)) {
            return $this->_options[$name];

        }  else if ($name == 'jsCalback') {
            return $this->jsCalback;
        }

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_options)) {
            return $this->_options[$name] = $value;
        }  else if ($name == 'jsCalback') {
            return $this->jsCalback = $value;
        }

        return parent::__set($name, $value);
    }

    public function registerClientScript()
    {
        $url = CHtml::asset(Yii::getPathOfAlias('ext.yiinfinite-scroll.assets') . '/jquery.infinitescroll.min.js');
        Yii::app()->clientScript->registerScriptFile($url);
    }

    private function createInfiniteScrollScript()
    {
        Yii::app()->clientScript->registerScript(
            uniqid(),
            "$('{$this->contentSelector}').infinitescroll(" . $this->buildInifiniteScrollOptions() . ');'
        );
    }

    private function buildInifiniteScrollOptions()
    {
        $options = array_merge($this->_options, $this->_default_options);
        $options = array_filter($options);
        $options = CJavaScript::encode($options);
        $options = $this->renderJavaScriptCallback($options);
        return $options;
    }

    /**
     * @param $options
     * @return string
     */
    private function renderJavaScriptCallback($options)
    {
        if(!empty($this->jsCalback)) {
            $options = $options . ',' . $this->jsCalback;
        }
        return $options;
    }

    private function renderNavigation()
    {
        $next_link = CHtml::link('', $this->createPageUrl($this->getCurrentPage(FALSE) + 1));
        echo '<div class="infinite_navigation">' . $next_link . '</div>';
    }

    private function theresNoMorePages()
    {
        return $this->getPages()->getCurrentPage() >= $this->getPages()->getPageCount();
    }

}