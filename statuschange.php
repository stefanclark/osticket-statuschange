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

    private static $pluginInstance = null;

    private function getPluginInstance(?int $id)
    {
        if ($id && ($i = $this->getInstance($id))) {
            return $i;
        }

        return $this->getInstances()->first();
    }

    public function bootstrap()
    {
        self::$pluginInstance = self::getPluginInstance(null);

        Signal::connect('object.created', [$this, 'statusChange'], 'Ticket', function ($object, $type) {
                return $type['type'] == 'message' && isset($type['uid']);
                });
    }

    public function statusChange($ticket, $type)
    {
        $config = $this->getConfig(self::$pluginInstance);

        if ($type['type'] != 'message' || !isset($type['uid'])) return;

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
