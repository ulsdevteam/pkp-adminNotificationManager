# Administrator Notification Manager plugin for OJS

This plugin disables email notification for all admin users, so they no longer receive any email notifications from a journal after its creation.  This is useful, for example, if the administrator account(s) are not responsible for editorial actions.

## Requirements

* OJS 3.4.x
* PHP 8 or later

## Installation

Install this as a "generic" plugin in OJS.  The preferred installation method is through the Plugin Gallery. To install manually via the filesystem, extract the contents of this archive to a "adminNotificationManager" directory under "plugins/generic" in your OJS root.  To install via Git submodule, target that same directory path: `git submodule add https://github.com/ulsdevteam/pkp-adminNotificationManager plugins/generic/adminNotificationManager` and `git submodule update --init --recursive plugins/generic/adminNotificationManager`.  Run the pluign installation script to register this plugin, e.g.: `php lib/pkp/tools/installPluginVersion.php plugins/generic/adminNotificationManager/version.xml`.

## Configuration

This plugin can only be enabled or disabled by a Site Administrator, and it operates at the site level.  It cannot be enabled or disabled for only specific journals.

## Usage

 Usage of this plugin can be accomplished one of two ways:

  1) After creating a new journal, saving any changes in the Journal Settings Wizard will disable email notifications for all admin users for that new journal.  The settings wizard will appear automatically after creating a new journal.  You can also reach the settings wizard from the list of hosted journals within the Site Administration page. From there click hosted journals, select your new journal, and click settings wizard. Make any desired changes here and hit save on the bottom right.

  2) With any journal, click website and navigate to the plugins tab. Scroll down in this page until you see this plugin listed under the generic plugins. Click the button to disable active notifications and hit okay on the pop-up.  This will unsubscribe all admin users from notifications from all journals.

You can confirm the functionality of the plugin by viewing an Administrator's profile.  In the top right, hit edit profile, and go to the notifications tab. If properly enabled all checkboxes for not recieving email notifications should be checked. You can also manually configure any notifications you may want to see for a particular journal.

## Author / License

Written by Alex Wreschnig and Tazio Polanco for the [University of Pittsburgh](http://www.pitt.edu).  Copyright (c) University of Pittsburgh.

Released under a license of GPL v2 or later.
