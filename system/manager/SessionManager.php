<?php

namespace framework\system\manager;

use Exception;
use framework;
use framework\system\kernel\Manager;
use Redis;

class SessionManager extends Manager
{

    public $session_id = null;
    public $commit = 0;
    public $leval = 0;
    public $opened = 0;

    public function init()
    {
        if ($this->initialized) {
            return false;
        }
        $session_config_name = framework::cm()->get('com.config')->get('application.session.name');
        if (!empty($session_config_name)) {
            session_name($name);
        }
        $session_config_redis = framework::cm()->get('com.config')->get('application.session.redis');
        if ($session_config_redis) {
            $session_config_redis_server = framework::cm()->get('com.config')->get('application.session.redis.server');
            $session_config_redis_port = framework::cm()->get('com.config')->get('application.session.redis.port');
            if ($session_config_redis_server) {
                if (class_exists('Redis')) {
                    $redis = new Redis();
                    try {
                        $result = $redis->connect($session_config_redis_server, $session_config_redis_port);
                    } catch (Exception $ex) {
                        $result = null;
                    }

                    if ($result) {
                        ini_set("session.save_handler", "redis");
                        ini_set("session.save_path", "tcp://" . $session_config_redis_server . ":" . $session_config_redis_port);
                    } else {
                        _notice("Redis Connect Error!");
                    }
                } else {
                    _notice("Class redis not found", __FILE__, __LINE__);
                }
            }
        }
        parent::init();
    }

    public function start($name = "")
    {
        $this->startSession();
    }

    public function startSession()
    {
        if (!isset($_SESSION)) {
            session_start();
            $this->commit = 0;
        } else {
            $temp_session = $_SESSION;
            if ($this->commit) {
                session_start();
                $_SESSION = $temp_session;
                $this->commit = 0;
            }
        }

        $this->session_id = session_id();
        framework::mm()->send('message.session.id', $this->session_id);

        if (!empty($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                framework::mm()->send('message.session.' . $key, $value);
            }
        }
        $this->leval++;
    }
    public function setId($sessionId = "")
    {
        if (!empty($sessionId)) {
            session_id($session_id);
        }
    }
    public function getId()
    {
        return $this->session_id;
    }

    public function get($name)
    {
        if (array_key_exists($name, $_SESSION)) {
            $value = $_SESSION[$name];
        } else {
            $value = false;
        }
        return $value;
    }

    public function set($name, $value)
    {
        if (!empty($name)) {
            $_SESSION[$name] = $value;
        }
    }

    public function commit()
    {
        if (!$this->commit) {
            if ($this->leval <= 1) {
                session_commit();
                $this->commit = 1;
            } else {
                $this->leval--;
            }
        }
    }

    public function destroy()
    {
        $this->startSession();
        $_SESSION = [];
        if ($this->commit) {
            session_start();
            $this->commit = 0;
        }
        session_unset();
        session_destroy();
    }

    public function end()
    {
        $this->destroy();
    }

}
