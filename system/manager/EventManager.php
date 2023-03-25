<?php

namespace framework\system\manager;

use framework;
use framework\system\kernel\Manager;
use framework\system\data\CChain;



class EventManager extends Manager {

    public $event_chain;
    public $current_event;
    public $current_component;
    public $current_method;

    /*
     * 
     * 
     * 
     * 
     */


    public function init() {

        $this->event_chain = new CChain;
        $this->addMappingFile([
            DIR_FRAMEWORK . DS . 'config' . DS . 'mapping' . DS . 'event' . EXT
        ]);
    }

    public function processMapping($mapping) {

        if (is_array($mapping)) {
            foreach ($mapping as $v) {
                if ($this->isEventName($v)) {
                    $this->add($v);
                }
            }
        }
    }

    /*
     *
     */

    public function push($event, $postion_event = null, $postion = 'before') {

        $this->add($event, $postion_event, $postion);
    }

    public function add($event, $postion_event = null, $postion = 'before') {

        if ($this->isEventName($event)) {
            $this->event_chain->push(['event' => $event]);
        }
    }

    public function prev() {

        $this->event_chain->prev();
    }

    /*
     * 
     */

    public function erun() {

        $event_a = $this->event_chain->current();

        if (!$event_a) {
            framework::mm()->send('message.framework.exit', true);
            return;
        }
        $event = $event_a['event'];
        if (isset($event_a['depends'])) {
            $depends = $event_a['depends'];
            //等待使用
        }

        $event = $this->eventComponent($event);

        $this->event($event);

        $this->event_chain->next();
    }

    /*
     * 
     * 
     */

    public function event($event, $parameter = NULL) {


        if (is_array($event) && isset($event['component'])) {

            $method = $event['method'];

            $component = framework::cm()->get($event['component']);

            if (substr($method, 0, 2) == '::') {
                $method = substr($method, 2);
                return $component::$method($parameter);
            } else {
                return $component->$method($parameter);
            }
        }
        if (is_string($event)) {
            $event = $this->eventComponent($event);
            $this->event($event, $parameter);
        }
    }

    /*
     * 
     * 
     */

    protected function eventComponent($event) {

        $event = explode('.', $event);
        array_shift($event);
        $component_method = array_pop($event);
        $component_name = 'com.' . implode('.', $event);
        $event = [];
        $event['component'] = $component_name;
        $event['method'] = $component_method;
        return $event;
    }

    /*
      public function onEvent() {
      if ((microtime(TRUE) - $GLOBALS['cong_start_time']) > framework::app()->getTimeOut()) {
      framework::console()->sendMessage('m_exit', TRUE);
      return FALSE;
      }
      if ((microtime(TRUE) - $GLOBALS['cong_start_time']) > 5) {
      ob_flush();
      flush();
      }
      if (empty($event)) {
      return FALSE;
      }
      }
     */
    /* public function getPos($name) {
      $pos = key($this->data);
      if (!empty($name)) {
      $data = $this->data;
      foreach ($data as $k => $v) {
      if ($name == $v['event']) {
      $pos = $k;
      }
      }
      }
      return $pos;
      }
     * 
     */
}
