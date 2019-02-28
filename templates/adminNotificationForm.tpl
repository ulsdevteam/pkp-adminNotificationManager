{**
* plugins/generic/adminNotificationManager/adminNotificationForm.tpl
*
* Copyright (c) University of Pittsburgh
* Distributed under the GNU GPL v2 or later. For full terms see the LICENSE file.
*
* Administrator Notification Manager form to disable notifications to all administrators
* from all contexts.
*
*}
<script>
	$(function () {ldelim}
		// Attach the form handler.
		$('#adminNotificationManagerForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="adminNotificationManagerForm" method="post"
	  action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="disableAllNotifications" disableNotifications=true}"
	  >
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="adminNotificationManagerFormNotification"}

	<div id="description">{translate key="plugins.generic.adminNotificationManager.instructions"}</div>

	{fbvFormButtons}

</form>
