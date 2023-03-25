<?php

namespace framework\system\kernel;

use framework;

class Route extends Component
{

    private $config = [];
    private $url = null;
    private $queryString = null;
    public $controllerId = null;
    public $actionId = null;

    public function init()
    {

    }

    public function start()
    {

        $this->getTheRouteConfig();
        $this->getTheUrl();
        framework::em()->push('event.route.rules');
        framework::em()->push('event.route.analysisPath');
        framework::em()->push('event.route.dispatch');
    }

    public function end()
    {
        ;
    }

    public function getTheRouteConfig()
    {
        $cmp_config = framework::cm()->get('com.config');
        $this->config['rules'] = $cmp_config->get('application.route.rules');
        $this->config['ext'] = $cmp_config->get('application.route.ext');
        $this->config['delimiter'] = $cmp_config->get('application.route.delimiter');
        $this->config['entryFileName'] = $cmp_config->get('system.entryFileName');
        $this->config['controllerFileFirst'] = $cmp_config->get('system.controllerFileFirst');
    }

    public function getTheUrl()
    {
        $cmp_request = framework::cm()->get('com.request');
        $path_info = $cmp_request->getPathInfo();
        $request_url = $cmp_request->getRequestURL();
        $this->queryString = $cmp_request->getQueryString();
        $this->url = $path_info ? $path_info : $request_url;
        $this->url = str_replace('\\', '/', $this->url);
    }

    public function rules()
    {

        if (!empty($this->config['rules'])) {

            foreach ($this->config['rules'] as $rule) {
                if ($rule['search']) {
                    $this->url = preg_replace('#' . $rule['search'] . '#', $rule['replace'], $this->url);
                    $this->queryString = preg_replace('#' . $rule['search'] . '#', $rule['replace'], $this->queryString);
                }
            }
        }

    }

    public function getRequestPath()
    {
        //获得？前路径
        $path_info_request = stristr($this->url, '?');
        $path_info_request = str_replace($path_info_request, '', $this->url);
        /**
         *
         */
        $request_route = framework::cm()->get('com.request')->get('get.route');
        if (!empty($request_route)) {
            $path_info_request = $request_route;
        }
        return $path_info_request;
    }

    public function analysisPath()
    {

        /* abc/abc/acc_action?abc=
         * get before ?
         */
        $path_info_request = $this->getRequestPath();
        /*
         *
         */
        if ($this->config['ext']) {
            $path_info_request = preg_replace('/.' . $this->config['ext'] . '$/', '', $path_info_request, 1);
        }
        /*
         *
         */
        $control_action_path = $path_info_request;
        $params_path = null;

        if (substr($control_action_path, 0, 1) == '/') {
            $control_action_path = substr($control_action_path, 1);
        }
        if (substr($control_action_path, -1, 1) == '/') {
            $control_action_path = substr($control_action_path, 0, -1);
        }

        $control_action_path_array = explode('/', $control_action_path);

        if (count($control_action_path_array) == 0) {
            $this->controllerId = framework::cm()->get('com.config')->get('application.defaultControllerId');
            $this->actionId = framework::cm()->get('com.config')->get('application.defaultActionId');
        }

        if (count($control_action_path_array) == 1) {

            if ($this->config['controllerFileFirst']) {
                $this->actionId = framework::cm()->get('com.config')->get('application.defaultActionId');
                $this->controllerId = implode('/', $control_action_path_array);
            } else {
                $this->actionId = array_pop($control_action_path_array);
                $this->controllerId = framework::cm()->get('com.config')->get('application.defaultControllerId');
            }
        }

        if (count($control_action_path_array) >= 2) {

            $path = '';
            for ($i = 0, $ii = count($control_action_path_array); $i < $ii; $i++) {
                $path = strtolower($path) . '/' . ucfirst($control_action_path_array[$i]);

                if (framework::cm()->get('com.application')->checkControllerExist($path)) {
                    $this->controllerId = $path;
                    break;
                }
            }
            while ($i + 1) {
                array_shift($control_action_path_array);
                $i--;
            }

            $this->actionId = isset($control_action_path_array[0]) ? $control_action_path_array[0] : framework::cm()->get('com.config')->get('application.defaultActionId');
            $params_path = implode('/', $control_action_path_array);
        }
        if (!$this->controllerId) {
            $params_path = $path_info_request;
        }
        if ($params_path) {
            $params = explode('/', $params_path);
            $params = array_filter($params);
            /*
             * 数字关联
             */

            foreach ($params as $key => $value) {
                $_GET['key_' . $key] = $value;
            }
            /*
             * key=>value
             */
            for ($i = 0;isset($params[$i]); $i++) {
                $key = $params[$i];
                $value = isset($params[$i + 1]) ? $params[$i + 1] : null;
                if (!array_key_exists($key, $_GET) && !is_null($key)) {
                    $_GET[$key] = $value;
                }
            }
        }
    }

    public function dispatch()
    {
        $cmp_request = framework::cm()->get('com.request');
        $url_get_controller_id = $cmp_request->get('get.controller');
        $url_get_action_id = $cmp_request->get('get.action');
        if ($url_get_controller_id) {
            $this->controllerId = $url_get_controller_id;
            if ($url_get_action_id) {
                $this->actionId = $url_get_action_id;
            }
        }
        /*
         * 过滤
         *
         */
        $this->controllerId = str_replace('\\', '/', $this->controllerId);
        $this->controllerId = preg_replace('/[^A-Za-z0-9\_\-\/]/', '', $this->controllerId);
        framework::mm()->send('message.application.controllerId', $this->controllerId);
        framework::mm()->send('message.application.actionId', $this->actionId);

    }

}
