<?php

/**
 * @file plugins/generic/adminNotificationManager/AdminNotificationManagerPlugin.inc.php
 *
 * Copyright (c) University of Pittsburgh
 * Distributed under the GNU GPL v2 or later. For full terms see the LICENSE file.
 *
 * @class AdminNotificationManagerPlugin
 * @ingroup plugins_generic_adminNotificationManager
 *
 * @brief Administrator Notification Manager plugin class
 */
import('lib.pkp.classes.plugins.GenericPlugin');

class AdminNotificationManagerPlugin extends GenericPlugin {

        /**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId) {
        $success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE'))
			return true;
		if ($success && $this->getEnabled()) {
			// Registers against a hook from controllers/grid/admin/journal/form/JournalSiteSettingsForm.inc.php .
			// This hook should be triggered upon submission of a form to create or edit a new journal.
			HookRegistry::register('JournalSiteSettingsForm::execute', array($this, 'disableNewAdminNotifications'));
		}
		return $success;
	}

        /**
	 * Get the display name of this plugin.
	 * @return String
	 */
	function getDisplayName() {
		return __('plugins.generic.adminNotificationManager.displayName');
	}

	/**
	 * Get a description of the plugin.
	 * @return String
	 */
	function getDescription() {
		return __('plugins.generic.adminNotificationManager.description');
	}

	/**
	 * Site-wide plugins should override this function to return true.
	 *
	 * @return boolean
	 */
	function isSitePlugin() {
		return true;
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
				$this->getEnabled() ? array(
			new LinkAction(
					'plugins.generic.adminNotificationManager.disableAllNotificationsTooltip', new AjaxModal(
					$router->url($request, null, null, 'manage', null, array('verb' => 'disableAllNotifications', 'plugin' => $this->getName(), 'category' => 'generic')), $this->getDisplayName()
					), __('plugins.generic.adminNotificationManager.disableAllNotifications'), null
			),
				) : array(), parent::getActions($request, $verb)
		);
	}

	/**
	 * @copydoc Plugin::manage()
	 */
	function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'disableAllNotifications':
				$this->import('AdminNotificationManagerForm');
				$form = new AdminNotificationManagerForm($this);
				
				if ($request->getUserVar('disableNotifications')) {
					$form->execute();
					return new JSONMessage(true);
				}
 				return new JSONMessage(true, $form->fetch($request));

			default:
				assert(false);
				return false;
		}
		return parent::manage($args, $request);
	}

	/**
	 * Private helper method to get a list of all admin users.
	 * @return array
	 */
	function _getAdminList() {
		$user = null;
		$users = array();

		$roleDAO = DAORegistry::getDAO('RoleDAO');
		$userDAO = $roleDAO->getUsersByRoleId(ROLE_ID_SITE_ADMIN);

		if ($userDAO && $userDAO->getCount() > 0) {
			while ($user = $userDAO->next()) {
				$users[$user->getId()] = $user;
			}
		}
		return $users;
	}
	
	/**
	 * Private helper method. Returns a map of notifications used. While this is
	 * based on libPKP's PKPNotificationSettingsForm and OJS's NotificationSettingsForm,
	 * those aren't _exactly_ available for easy reuse.
	 * 
	 * @return array
	 */
	function _getNotificationSettingsMap() {
		$notificationMap = array(
			/* from lib/pkp/classes/notification/form/PKPNotificationSettingsForm */
			NOTIFICATION_TYPE_SUBMISSION_SUBMITTED => array('settingName' => 'notificationSubmissionSubmitted',
				'emailSettingName' => 'emailNotificationSubmissionSubmitted',
				'settingKey' => 'notification.type.submissionSubmitted'),
                        /* newly added from OJS 3.1.2 */
			NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_REQUIRED => array('settingName' => 'notificationEditorAssignmentRequired',
				'emailSettingName' => 'emailNotificationEditorAssignmentRequired',
				'settingKey' => 'notification.type.editorAssignmentTask'),
			NOTIFICATION_TYPE_METADATA_MODIFIED => array('settingName' => 'notificationMetadataModified',
				'emailSettingName' => 'emailNotificationMetadataModified',
				'settingKey' => 'notification.type.metadataModified'),
			NOTIFICATION_TYPE_REVIEWER_COMMENT => array('settingName' => 'notificationReviewerComment',
				'emailSettingName' => 'emailNotificationReviewerComment',
				'settingKey' => 'notification.type.reviewerComment'),
			NOTIFICATION_TYPE_NEW_QUERY => array('settingName' => 'notificationNewQuery',
				'emailSettingName' => 'emailNotificationNewQuery',
				'settingKey' => 'notification.type.queryAdded'),
			NOTIFICATION_TYPE_QUERY_ACTIVITY => array('settingName' => 'notificationQueryActivity',
				'emailSettingName' => 'emailNotificationQueryActivity',
				'settingKey' => 'notification.type.queryActivity'),
			NOTIFICATION_TYPE_NEW_ANNOUNCEMENT => array('settingName' => 'notificationNewAnnouncement',
				'emailSettingName' => 'emailNotificationNewAnnouncement',
				'settingKey' => 'notification.type.newAnnouncement'),
			/* from classes/notification/form/NotificationSettingsForm */
			NOTIFICATION_TYPE_PUBLISHED_ISSUE => array('settingName' => 'notificationPublishedIssue',
				'emailSettingName' => 'emailNotificationPublishedIssue',
				'settingKey' => 'notification.type.issuePublished'),
		);
		return $notificationMap;
	}

	/**
	 * Private helper method. Opens up a context DAO and iterates over the contexts,
	 * returning an array with index "context ID" and value "context name".
	 * 
	 * @return array
	 */
	function _getContexts() {
		$contextDao = Application::getContextDAO();
		$contextIterator = $contextDao->getAvailable();
		if ($contextIterator && $contextIterator->getCount() > 1) {
			$contextsById = array();
			while ($context = $contextIterator->next()) {
				$contextsById[$context->getId()] = $context->getLocalizedName();
			}
		}
		return $contextsById;
	}
	
	/**
	 * This public method requests a list of contexts and for each context, calls
	 * _disableAdminNotificationsByContext. Called by
	 * AdminNotificationManagerForm.inc.php.
	 * 
	 * @return none
	 */
	function disableAllAdminNotifications() {
		$contexts = $this->_getContexts();
		foreach($contexts as $context=>$contextName) {
			$this->_disableAdminNotificationsByContext($context);
		}
		return;
	}
	
	/**
	 * This private helper method takes a context, gets a list of admin users
	 * from _getAdminList(), and iterates through the admin users to 
	 * call _disableNotificationsByContextAndUser() on each.
	 * 
	 * @param $contextId the ID of a context.
	 * @return none
	 */
	function _disableAdminNotificationsByContext($contextId) {
		$admins = $this->_getAdminList();
		foreach($admins as $userId=>$admin) {
			$this->_disableNotificationsByContextAndUser($contextId, $userId);
		}
		return;
	}
	
	/**
	 * This private helper method takes a context and a user and disables notifications
	 * for that user from that context.
	 * 
	 * @param $contextId the ID of a context.
	 * @param $userId the ID of a user.
	 * @return none
	 */
	function _disableNotificationsByContextAndUser($contextId, $userId) {
		if(!(is_numeric($contextId) && is_numeric($userId))) return;
		
		$notificationSubscriptionSettingsDao = DAORegistry::getDAO('NotificationSubscriptionSettingsDAO');
		$notificationMap = $this->_getNotificationSettingsMap();
		$emailSettings = array();
		foreach($notificationMap as $setting=>$settingArray) {
			$emailSettings[] = $setting;
		}
		$notificationSubscriptionSettingsDao->updateNotificationSubscriptionSettings('blocked_emailed_notification', $emailSettings, $userId, $contextId);
		return;
	}
	
	/**
	 * Hook callback: get the ID of the new journal (from args) and call the method
	 * that disables notifications for admin users on the context ID of the new journal.
	 * 
	 * @param $hookName string
	 * @param $args array
	 */
	function disableNewAdminNotifications($hookName, $args) {
		$newContextId = $args[1]->getData("id");
		$this->_disableAdminNotificationsByContext($newContextId);
		// returning false allows processing to continue
		return false;
	}

}

?>
