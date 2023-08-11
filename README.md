osTicket Status Change Plugin
=====

Plugin will allow status change of a ticket on new message by user and/or agent response.

E.g. you may have two Open status "Open" and "Awaiting Client Response". This plugin can be configured to automatically change the status from "Awaiting Client Response" to "Open" when a new message is received.

Alternatively you may have statuses of "Open" and "Acknowledged" and want to automatically change the status when the Agent replies in case they forget to manually change the status when replying.

Note: This plugin processes the status change after any agent change.

## Installation
1. Create a 'statuschange' folder in include/plugins
2. Download all files into the folder
3. Install plugin via Admin Panel -> Manage -> Plugins
4. Add instance(s) for all status change rules you want to implement
