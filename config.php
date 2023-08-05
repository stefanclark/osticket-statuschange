<?php

require_once INCLUDE_DIR . 'class.plugin.php';

class StatusChangePluginConfig extends PluginConfig
{
    public function translate()
    {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function ($x) {
                    return $x;
                }
                ,
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                }
                ,
            );
        }
        return Plugin::translate('statuschange');
    }

    public function getOptions()
    {
        list ($__, $_N) = self::translate();

        $choices = array();
        foreach (TicketStatusList::getStatuses(array(
            'states' => array('open', 'closed')
        ))
        as $S) {
            // TODO: Move this to TicketStatus::getName
            $name = $S->getName();
            if (!($isenabled = $S->isEnabled()))
                $name.=' '.__('(disabled)');
            $choices[$S->getId()] = $name;
        }

        return array(
            'statuschange' => new SectionBreakField(array(
                'label' => 'Status Change Plugin',
            )),
            'statuschange-by' => new ChoiceField(array(
                'label' => $__('Change status by'),
                'hint' => $__('Who can trigger a status change'),
                'choices' => array('agent' => 'Agent (staff)', 'user' => 'User (client)'),
                'configuration' => array('multiselect' => true),
            )),
            'statuschange-from' => new ChoiceField(array(
                'label' => $__('Current status'),
                'hint' => $__('Status that you wish to change from on new ticket message'),
                'choices' => $choices
            )),
            'statuschange-to' => new ChoiceField(array(
                'label' => $__('New status'),
                'hint' => $__('Status that you wish to change to on new ticket message'),
                'choices' => $choices
            ))
        );
    }
}
