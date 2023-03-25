<?php

namespace framework\system\manager;

use framework\system\kernel\Manager;

/**
 * Description of CEventManager
 *
 * @author xiaolei
 */
class MessageManager extends Manager {

    public $message_map = ['message.framework.exit' => false];
    public $message = NULL;

    public function init() {
        ;
    }

    public function send($message_name = 'message.framework.exit', $message = true) {

        $this->sendMessage($message_name, $message);
    }

    public function sendMessage($message_name = 'message.framework.exit', $message = true) {


        $this->message_map[$message_name] = $message;
    }

    public function get($message) {

        return $this->getMessage($message);
    }

    public function getMessage($message_name = 'message.framework.exit') {

        if (array_key_exists($message_name, $this->message_map)) {


            $message = $this->message_map[$message_name];
        } else {

            $message = NULL;
        }
        return $message;
    }

    public function destoryMessage($message_name = '') {
        unset($this->message_map[$message_name]);
    }

}
