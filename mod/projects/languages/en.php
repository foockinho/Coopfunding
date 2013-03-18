<?php
/**
 * Elgg projects plugin language pack
 *
 * @package Coopfunding
 * @subpackage Projects
 */

$language = array(

	/**
	 * Menu items and titles
	 */
	'projects' => "Projects",
	'projects:owned' => "Projects I own",
	'projects:owned:user' => 'Projects %s owns',
	'projects:yours' => "My projects",
	'projects:user' => "%s's projects",
	'projects:all' => "All projects",
	'projects:add' => "Propose a new project",
	'projects:edit' => "Edit project",
	'projects:delete' => 'Delete project',
	'projects:membershiprequests' => 'Manage join requests',
	'projects:membershiprequests:pending' => 'Manage join requests (%s)',
	'projects:invitations' => 'Project invitations',
	'projects:invitations:pending' => 'Project invitations (%s)',

	'projects:icon' => 'Project icon (leave blank to leave unchanged)',
	'projects:name' => 'Project name',
	'projects:username' => 'Project short name (displayed in URLs, alphanumeric characters only)',
	'projects:description' => 'Description',
	'projects:briefdescription' => 'Brief description',
	'projects:interests' => 'Tags',
	'projects:website' => 'Website',
	'projects:members' => 'Project members',
	'projects:my_status' => 'My status',
	'projects:my_status:project_owner' => 'You own this project',
	'projects:my_status:project_member' => 'You are in this project',
	'projects:subscribed' => 'Project notifications on',
	'projects:unsubscribed' => 'Project notifications off',

	'projects:members:title' => 'Members of %s',
	'projects:members:more' => "View all members",
	'projects:membership' => "Project membership permissions",
	'projects:access' => "Access permissions",
	'projects:owner' => "Owner",
	'projects:owner:warning' => "Warning: if you change this value, you will no longer be the owner of this project.",
	'projects:widget:num_display' => 'Number of projects to display',
	'projects:widget:membership' => 'Project membership',
	'projects:widgets:description' => 'Display the projects you are a member of on your profile',
	'projects:noaccess' => 'No access to project',
	'projects:permissions:error' => 'You do not have the permissions for this',
	'projects:inproject' => 'in the project',
	'projects:cantedit' => 'You can not edit this project',
	'projects:saved' => 'Project saved',
	'projects:featured' => 'Featured projects',
	'projects:makeunfeatured' => 'Unfeature',
	'projects:makefeatured' => 'Make featured',
	'projects:featuredon' => '%s is now a featured project.',
	'projects:unfeatured' => '%s has been removed from the featured projects.',
	'projects:featured_error' => 'Invalid project.',
	'projects:joinrequest' => 'Request membership',
	'projects:join' => 'Join project',
	'projects:leave' => 'Leave project',
	'projects:invite' => 'Add members',
	'projects:invite:title' => 'Invite friends to this project',
	'projects:inviteto' => "Invite friends to '%s'",
	'projects:nofriends' => "You have no friends left who have not been invited to this project.",
	'projects:nofriendsatall' => 'You have no friends to invite!',
	'projects:viaprojects' => "via projects",
	'projects:project' => "Project",
	'projects:search:tags' => "tag",
	'projects:search:title' => "Search for projects tagged with '%s'",
	'projects:search:none' => "No matching projects were found",
	'projects:search_in_project' => "Search in this project",
	'projects:acl' => "Project: %s",

	'projects:activity' => "Project activity",
	'projects:enableactivity' => 'Enable project activity',
	'projects:activity:none' => "There is no project activity yet",

	'projects:notfound' => "Project not found",
	'projects:notfound:details' => "The requested project either does not exist or you do not have access to it",

	'projects:requests:none' => 'There are no current membership requests.',

	'projects:invitations:none' => 'There are no current invitations.',

	'projects:count' => "projects created",
	'projects:open' => "open project",
	'projects:closed' => "closed project",
	'projects:member' => "members",
	'projects:searchtag' => "Search for projects by tag",

	'projects:more' => 'More projects',
	'projects:none' => 'No projects',


	/*
	 * Access
	 */
	'projects:access:private' => 'Closed - Users must be invited',
	'projects:access:public' => 'Open - Any user may join',
	'projects:access:project' => 'Project members only',
	'projects:closedproject' => 'This project has a closed membership.',
	'projects:closedproject:request' => 'To ask to be added, click the "request membership" menu link.',
	'projects:visibility' => 'Who can see this project?',

	/*
	Project tools
	*/
	'projects:yes' => 'yes',
	'projects:no' => 'no',

);

add_translation(basename(__FILE__, '.php'), $language);
