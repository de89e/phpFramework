<?php

    namespace framework\system\kernel;

    use framework\system\data\CModel;
    use framework;

    class Controller extends CModel {

        public $defaultAction = 'Index';
        public $actionId = 'Index';
        protected $view;

        public function __construct() {

            $this->view = new View();
        }

        public function beforeAction() {
            
        }

        public function afterAction() {
            
        }

        public function actionError() {
            
        }

        public function &View() {
            return $this->view;
        }

        final public function action($args = NULL) {
            $this->beforeAction($args);
            $action_id = 'action' . ucwords($this->actionId);
            if ($action_id == 'action') {
                $action_id = 'action' . $this->defaultAction;
            }
            $action_need_jump = framework::mm()->get('message.application.jumpAction');
            if (empty($action_need_jump)) {

                $this->$action_id($args);
                $this->afterAction($args);
            } else {

                $actions_jump = explode(',', $action_need_jump);
                if (in_array('_main_', $actions_jump)) {
                    if (in_array('~' . ucwords($this->actionId), $actions_jump)) {
                        $this->$action_id($args);
                    }
                } else {
                    if (!in_array(ucwords($this->actionId), $actions_jump)) {
                        $this->$action_id($args);
                    }
                }

                if (!in_array('_after_', $actions_jump)) {
                    $this->afterAction($args);
                }
            }
            if (empty($action_need_jump) || $action_need_jump !== '_after_') {
                $action_need_jump !== '_main_';
            }
        }

    }
    