<?php
/**
* Elgg sent messages page
*
* @package ElggMessages
*/

elgg_gatekeeper();

$username = elgg_extract('username', $vars);
$page_owner = get_user_by_username($username);
if (!$page_owner) {
	$page_owner = elgg_get_logged_in_user_entity();
}
elgg_set_page_owner_guid($page_owner->guid);

$page_owner = elgg_get_page_owner_entity();

if (!$page_owner || !$page_owner->canEdit()) {
	$guid = 0;
	if ($page_owner) {
		$guid = $page_owner->getGUID();
	}
	register_error(elgg_echo("pageownerunavailable", [$guid]));
	forward();
}

elgg_push_breadcrumb(elgg_echo('messages:sent'));

elgg_register_title_button('messages', 'add', 'object', 'messages');

$title = elgg_echo('messages:sentmessages', [$page_owner->name]);

$list = elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => 'messages',
	'metadata_name' => 'fromId',
	'metadata_value' => elgg_get_page_owner_guid(),
	'owner_guid' => elgg_get_page_owner_guid(),
	'full_view' => false,
	'bulk_actions' => true
]);

$body_vars = [
	'folder' => 'sent',
	'list' => $list,
];
$content = elgg_view_form('messages/process', [], $body_vars);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'show_owner_block_menu' => false,
]);

echo elgg_view_page($title, $body);
