# Administrator Notification Manager plugin for OJS

This plugin, when installed and enabled, automatically unsubscribes all admin users from notifications sent by a journal when that journal is first created. It also allows you to unsubscribe all admin users from all email notifications sent by all journals.

## Requirements

* OJS 3.1.x
* PHP 5.3 or later

## Installation

Install this as a "generic" plugin in OJS.  The preferred installation method is through the Plugin Gallery. To install manually via the filesystem, extract the contents of this archive to a "clamav" directory under "plugins/generic" in your OJS root.  To install via Git submodule, target that same directory path: `git submodule add https://github.com/ulsdevteam/pkp-clamav plugins/generic/clamav` and `git submodule update --init --recursive plugins/generic/clamav`.  Run the upgrade script to register this plugin, e.g.: `php tools/upgrade.php upgrade`.

## Configuration

You must be the site administrator in order to enable or configure this plugin.  Enabling this plugin enables the automatic unsubscription feature. To disable this feature, disable the plugin.

## Usage

This plugin does not require any further configuration in order to function. However, if you view the plugin in the plugin list, you can access the Disable Active Notifications feature. Click on the link, read the disclaimer, and click "OK" to disable all email notifications for all admin users from all journals.

## Author / License

Written by Alex Wreschnig for the [University of Pittsburgh](http://www.pitt.edu).  Copyright (c) University of Pittsburgh.

Released under a license of GPL v2 or later.
