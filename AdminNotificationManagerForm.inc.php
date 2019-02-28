<?php

/**
 * @file plugins/generic/clamav/ClamavSettingsForm.inc.php
 *
 * Copyright (c) 2018 University of Pittsburgh
 * Distributed under the GNU GPL v2 or later. For full terms see the LICENSE file.
 *
 * @class ClamavSettingsForm
 * @ingroup plugins_generic_clamav
 *
 * @brief Form for the site admin to modify Clam AV plugin settings
 */
import('lib.pkp.classes.form.Form');

class AdminNotificationManagerSettingsForm extends Form {

	/** @var object */
	var $_plugin;
	
	/**
	 * Constructor
	 * @param $plugin ClamavSettingsForm
	 * @param $contextId int
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplatePath() . 'adminNotificationForm.tpl');

//		$this->addCheck(new FormValidator($this, 'clamavPath', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.generic.clamav.manager.settings.clamavPathRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData($request) {
		/*
		$plugin = $this->_plugin;
		$basePluginUrl = $request->getBaseUrl() . DIRECTORY_SEPARATOR . $plugin->getPluginPath() . DIRECTORY_SEPARATOR;
		$baseIndexUrl = $request->getIndexUrl();

		$this->setData('clamavPath', $plugin->getSetting(CONTEXT_SITE, 'clamavPath'));
		$this->setData('clamavUseSocket', $plugin->getSetting(CONTEXT_SITE, 'clamavUseSocket'));
		$this->setData('clamavSocketPath', $plugin->getSetting(CONTEXT_SITE, 'clamavSocketPath'));
		$this->setData('unscannedFileOption', $plugin->getSetting(CONTEXT_SITE, 'allowUnscannedFiles'));
		$this->setData('clamavSocketTimeout', $plugin->getSetting(CONTEXT_SITE, 'clamavSocketTimeout'));


		$this->setData('pluginJavascriptURL', $basePluginUrl . 'js' . DIRECTORY_SEPARATOR);
		$this->setData('pluginStylesheetURL', $basePluginUrl . 'css' . DIRECTORY_SEPARATOR);
		$this->setData('pluginLoadingImageURL', $basePluginUrl . 'images' . DIRECTORY_SEPARATOR . "spinner.gif");
		$this->setData('pluginLoadingImageURL', $basePluginUrl . 'images' . DIRECTORY_SEPARATOR . "spinner.gif");
		$this->setData('pluginAjaxUrl', $baseIndexUrl . DIRECTORY_SEPARATOR . 'clamav' . DIRECTORY_SEPARATOR . 'clamavVersion');

		$this->setData('baseUrl', $request->getBaseUrl());
		 * 
		 */
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		//$this->readUserVars(array('clamavPath', 'clamavUseSocket', 'clamavSocketPath', 'clamavSocketTimeout', 'allowUnscannedFiles',));
		return;
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
/*
		$unscannedFileOptions = array(
			'allow' => __('plugins.generic.clamav.manager.settings.allowUnscannedFiles.allow'),
			'block' => __('plugins.generic.clamav.manager.settings.allowUnscannedFiles.block')
		);
		$templateMgr->assign('unscannedFileOptions', $unscannedFileOptions);
*/
		return parent::fetch($request);
	}

	/**
	 * Save settings.
	 */
	function execute() {
		$this->_plugin->disableAllAdminNotifications();
	}

}

?>
