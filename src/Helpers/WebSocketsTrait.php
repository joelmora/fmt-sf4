<?php

namespace isoft\fmtsf4\Helpers;

use WebSocket\Client;

trait WebSocketsTrait
{
    protected $clientWs;

    public function sendWSMessage(
        $type,
        $id = null,
        $name = null,
        $param = null,
        $step = null,
        $data = null,
        $log = null
    ) {
        $message = array();
        $message[$type] = array();

        if ($id) {
            $message[$type]['id'] = $id;
        }
        if ($name) {
            $message[$type]['name'] = $name;
        }

        if ($param) {
            $message[$type]['param'] = $param;
        }

        if ($step) {
            $message[$type]['step'] = $step;
        }

        if ($data) {
            $message[$type]['data'] = $data;
        }

        if ($log) {
            $message[$type]['log'] = $log;
        }

        $this->logClientWS($message);
    }

    public static function startClientWS($uri = null)
    {
        if ($uri == null) {
            $uri = getenv("WS_LOCAL_MONITORING_URI");
        }
        // Inicia Cliente WebSocket para Enviar Mensajes
        try {
            // if (!isset($this->clientWs) and !is_object($this->clientWs)) {
            return new Client($uri);
            // }
        } catch (Exception $exc) {
        }
    }

    public static function stopClientWS($clientWs)
    {
        // Cierra Cliente WebSocket para Enviar Mensajes
        try {
            if (is_object($clientWs)) {
                $clientWs->close();
            }
        } catch (Exception $exc) {
        }
    }

    public static function logClientWS($clientWs, $message)
    {
        try {
            $clientWs->send(json_encode($message));
        } catch (Exception $exc) {
        }
    }
}
