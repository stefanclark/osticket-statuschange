<?php

require_once(INCLUDE_DIR . 'class.plugin.php');
require_once(INCLUDE_DIR . 'class.signal.php');
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once(INCLUDE_DIR . 'class.osticket.php');
require_once(INCLUDE_DIR . 'class.config.php');
require_once('config.php');

class StatusChangePlugin extends Plugin
{
    public $config_class = "StatusChangePluginConfig";

    private static $instanceConfig = null;

    public function bootstrap()
    {
        $pluginInstance = new StatusChangePlugin();
        $pluginInstance->instanceConfig = $this->getConfig();

        Signal::connect('object.created', [$pluginInstance, 'statusChange'], 'Ticket', function ($object, $type) {
                    return $type['type'] == 'message'/* && isset($type['uid'])*/;
        });
    }

    public function statusChange($ticket, $type)
    {
        $config = $this->instanceConfig;
        if (!($config instanceof PluginConfig)) {
            return;
        }

        $by = $config->get('statuschange-by');
        if ($type['type'] != 'message') {
            return;
        }
        if (isset($type['uid']) && !isset($by['user'])) {
            return;
        }
        if (!isset($type['uid']) && !isset($by['agent'])) {
            return;
        }

        $from = $config->get('statuschange-from');
        $to = $config->get('statuschange-to');
        if (!empty($from) && !empty($to)) {
            $status = $ticket->getStatusId();
            if ($status == $from) {
                $ticket->setStatus($to);
                $this->log(__FUNCTION__ . ": Updating status ($from => $to)");
            } else {
                $this->log(__FUNCTION__ . ": Current status does not match ($status != $from)");
            }
        } else {
            $this->log(__FUNCTION__ . ': Status from/to not configured', LOG_WARN);
        }
    }

    private function log($message, $level = LOG_DEBUG)
    {
        global $ost;

        if ($ost instanceof osTicket && $message) {
            $ost->log($level, "Plugin: StatusChange", $message);
        }
    }
}
