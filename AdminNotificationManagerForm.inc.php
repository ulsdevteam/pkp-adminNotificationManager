<?php

/**
 * @file plugins/generic/adminNotificationManager/AdminNotificationManagerForm.inc.php
 *
 * Copyright (c) University of Pittsburgh
 * Distributed under the GNU GPL v2 or later. For full terms see the LICENSE file.
 *
 * @class AdminNotificationManagerForm
 * @ingroup plugins_generic_adminNotificationManager
 *
 * @brief Form allows a user to unsubscribe all admin users from all email notifications
 * from all journals, and displays a disclaimer prior to the user hitting "OK."
 */
import('lib.pkp.classes.form.Form');

class AdminNotificationManagerForm extends Form {

	/** @var object */
	var $_plugin;
	
	/**
	 * Constructor
	 * @param $plugin AdminNotificationManagerPlugin
	 */
	function __construct($plugin) {
		$this->_plugin = $plugin;

		if (method_exists($plugin, 'getTemplateResource')) {
			// OJS 3.1.2 and later
			parent::__construct($plugin->getTemplateResource('adminNotificationForm.tpl'));
		} else {
			// OJS 3.1.1 and earlier
			parent::__construct($plugin->getTemplatePath() . 'adminNotificationForm.tpl');
		}

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * Executing the form--hitting "OK"--should call disableAllAdminNotifications().
	 */
	function execute() {
		$this->_plugin->disableAllAdminNotifications();
	}

}

?>
