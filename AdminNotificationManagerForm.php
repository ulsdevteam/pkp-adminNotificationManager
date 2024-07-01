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
namespace APP\plugins\generic\adminNotificationManager;
//import('lib.pkp.classes.form.Form');
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use APP\template\TemplateManager;

class AdminNotificationManagerForm extends Form {

	/** @var object */
	var $_plugin;
	
	/**
	 * Constructor
	 * @param $plugin AdminNotificationManagerPlugin
	 */
	function __construct($plugin) {
		$this->_plugin = $plugin;
		parent::__construct($plugin->getTemplateResource('adminNotificationForm.tpl'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */ 
	//fetch($request, $template = null, $display = false)
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * Executing the form--hitting "OK"--should call disableAllAdminNotifications().
	 */
	function execute(...$functionArgs) {
		$this->_plugin->disableAllAdminNotifications();
	}

}

?>
