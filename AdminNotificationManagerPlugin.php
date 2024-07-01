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
namespace APP\plugins\generic\adminNotificationManager;
//import('lib.pkp.classes.plugins.GenericPlugin');
use APP\services\ContextService;
use APP\plugins\generic\adminNotificationForm as adminNotificationForm;
use APP\facades\Repo;
//use APP\user\Repository as userRepository;
use APP\userGroup\Repository as userGroupRepository;
use PKP\security\Role;
use PKP\db\DAORegistry;
use PKP\config\Config;
use PKP\plugins\Hook;
use PKP\core\JSONMessage;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
//use PKP\services\PKPUserService;

class AdminNotificationManagerPlugin extends GenericPlugin {

	/**
	 * @copydoc Plugin::register()
	 */
	public function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE'))
			return true;
		if ($success && $this->getEnabled()) {
			// Registers against a hook from controllers/grid/admin/journal/form/JournalSiteSettingsForm.inc.php .
			// This hook should be triggered upon submission of a form to create or edit a new journal.
			Hook::add('JournalSiteSettingsForm::execute', array($this, 'disableNewAdminNotifications'));
		}
		return $success;
	}

	/**
	 * @copydoc Plugin::getDisplayName
	 */
	public function getDisplayName() {
		return __('plugins.generic.adminNotificationManager.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription
	 */
	public function getDescription() {
		return __('plugins.generic.adminNotificationManager.description');
	}

	/**
	 * @copydoc Plugin::isSitePlugin
	 */
	public function isSitePlugin() {
		return true;
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	public function getActions($request, $verb) {
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
	public function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'disableAllNotifications':
				//$this->import('AdminNotificationManagerForm');
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
	 * Private helper method to get an array that lists group
	 * context ids of all admin users.
	 * 
	 * @return array
	 */
	private function _getAdminList() {
		$arrayOfContexts = $this->_getContexts();
		$users = array();
		$userGroupId = array();
		$user = null;
		//$userFactory = new PKPUserService();
		$userRepo = Repo::user();

		foreach ($arrayOfContexts as $context) {
			//getByRoleIds(array $roleIds, int $contextId, ?bool $default = null)
			//$userGroupDAO = DAORegistry::getDAO('UserGroupDAO');
			//$userGroupDAOFactory = $userGroupDAO->getByRoleId($context, ROLE_ID_SITE_ADMIN);
			// $userGroupRepo = Repo::userGroup();
			// $userGroups = $userGroupRepo->getByRoleIds([Role::ROLE_ID_SITE_ADMIN], $context);
			// $userGroupsArray = $userGroups->toArray();

			// if (count($userGroupsArray) >= 1) {
			// 	foreach($userGroupsArray as $group) {
			// 		$groupId = $group->getId();
			// 		$userGroupId[] = $groupId;
			// 	}
			// }
			// $args = array('userGroupIds'=>$userGroupId);
			//$listOfUsers = $userFactory->getMany($args);
			$listOfUsers = $userRepo->getCollector()
			->filterByRoleIds([Role::ROLE_ID_SITE_ADMIN]) //need to check filterBySettings and find a replacement
			->getMany();
			foreach ($listOfUsers as $user) {
				$idValue = $user->getId();
				$users[$idValue] = $user;
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
	private function _getNotificationSettingsMap() {
		$notificationMap = array(
			/* from lib/pkp/classes/notification/form/PKPNotificationSettingsForm */
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_SUBMISSION_SUBMITTED => array('settingName' => 'notificationSubmissionSubmitted',
				'emailSettingName' => 'emailNotificationSubmissionSubmitted',
				'settingKey' => 'notification.type.submissionSubmitted'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_EDITOR_ASSIGNMENT_REQUIRED => array('settingName' => 'notificationEditorAssignmentRequired',
				'emailSettingName' => 'emailNotificationEditorAssignmentRequired',
				'settingKey' => 'notification.type.editorAssignmentTask'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_REVIEWER_COMMENT => array('settingName' => 'notificationReviewerComment',
				'emailSettingName' => 'emailNotificationReviewerComment',
				'settingKey' => 'notification.type.reviewerComment'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_NEW_QUERY => array('settingName' => 'notificationNewQuery',
				'emailSettingName' => 'emailNotificationNewQuery',
				'settingKey' => 'notification.type.queryAdded'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_QUERY_ACTIVITY => array('settingName' => 'notificationQueryActivity',
				'emailSettingName' => 'emailNotificationQueryActivity',
				'settingKey' => 'notification.type.queryActivity'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_NEW_ANNOUNCEMENT => array('settingName' => 'notificationNewAnnouncement',
				'emailSettingName' => 'emailNotificationNewAnnouncement',
				'settingKey' => 'notification.type.newAnnouncement'),
			/* from classes/notification/form/NotificationSettingsForm */
			\APP\notification\Notification::NOTIFICATION_TYPE_PUBLISHED_ISSUE => array('settingName' => 'notificationPublishedIssue',
				'emailSettingName' => 'emailNotificationPublishedIssue',
				'settingKey' => 'notification.type.issuePublished'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_EDITORIAL_REPORT => array('settingName' => 'notificationEditorialReport',
				'emailSettingName' => 'emailNotificationEditorialReport',
				'settingKey' => 'notification.type.editorialReport'),
			\APP\notification\Notification::NOTIFICATION_TYPE_OPEN_ACCESS => array('settingName' => 'notificationOpenAccess',
				'emailSettingName' => 'emailNotificationOpenAccess',
				'settingKey' => 'notification.type.openAccess'),
			\PKP\notification\PKPNotification::NOTIFICATION_TYPE_EDITORIAL_REMINDER => ['settingName' => 'notificationEditorialReminder',
				'emailSettingName' => 'emailNotificationEditorialReminder',
				'settingKey' => 'notification.type.editorialReminder'],
		);
		return $notificationMap;
	}

	/**
	 * Private helper method. Creates a new context service object and lists contexts
	 * by their ids, which are placed in a array to be returned. 
	 * 
	 * @return array
	 */
	private function _getContexts() {
		$contextIdsObject = new ContextService();
		$contextsById = $contextIdsObject->getIds();
		return $contextsById;
	}
	
	/**
	 * This public method requests a list of contexts and for each context, calls
	 * _disableAdminNotificationsByContext. Called by
	 * AdminNotificationManagerForm.inc.php.
	 * 
	 * @return none
	 */
	public function disableAllAdminNotifications() {
		$contexts = $this->_getContexts();
		foreach($contexts as $context) {
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
	private function _disableAdminNotificationsByContext($contextId) {
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
	private function _disableNotificationsByContextAndUser($contextId, $userId) {
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
	 * @return boolean
	 */
	public function disableNewAdminNotifications($hookName, $args) {
		$newContextId = $args[1]->getData("id");
		$this->_disableAdminNotificationsByContext($newContextId);
		// returning false allows processing to continue
		return false;
	}

}

?>
