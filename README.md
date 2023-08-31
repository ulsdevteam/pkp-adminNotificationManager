# Administrator Notification Manager plugin for OJS

This plugin changes the notification settings for all admin users, so they no longer recieve any email notifications from a journal after its creation. This plugin disables every type of email notifications for a journal when used, and must be used for each journal on the site. 

## Requirements

* OJS 3.3.x
* PHP 5.3 or later

## Installation

Install this as a "generic" plugin in OJS.  The preferred installation method is through the Plugin Gallery. To install manually via the filesystem, extract the contents of this archive to a "adminNotificationManager" directory under "plugins/generic" in your OJS root.  To install via Git submodule, target that same directory path: `git submodule add https://github.com/ulsdevteam/pkp-adminNotificationManager plugins/generic/adminNotificationManager` and `git submodule update --init --recursive plugins/generic/adminNotificationManager`.  Run the upgrade script to register this plugin, e.g.: `php tools/upgrade.php upgrade`.

## Configuration

This plugin is automatically enabled upon proper installation. The plugin will also be automatically enabled for the creation of each new journal is created on the site. 

## Usage

 Usage of this plugin can be accomplished one of two ways:

  1) After creating a new journal, click the administration button on the left (if you don't see this button click the list of journals in the top left corner and select one). From there click hosted journals, select your new journal, and click settings wizard. Make any desired changes here and hit save on the bottom right.

  2) After creating a new journal, select it from the menu in the top left. Click website and navigate to the plugins tab. Scroll down in this page until you see this plugin listed under the generic plugins. Click the button to disable active notifications and hit okay on the pop-up.

From here navigate back to your profile in the top right, hit edit profile, and go to the notifications tab. If properly enabled all checkboxes for not recieving email notifications should be checked. You can also manually configure any notifications you may want to see for a particular journal.

## Author / License

Written by Alex Wreschnig and Tazio Polanco for the [University of Pittsburgh](http://www.pitt.edu).  Copyright (c) University of Pittsburgh.

Released under a license of GPL v2 or later.
