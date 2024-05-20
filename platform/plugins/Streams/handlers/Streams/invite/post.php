<?php

/**
 * Invites a user (or a future user) to a stream .
 * @param {array} $_POST
 * @param {string} $_POST.publisherId The id of the stream publisher
 * @param {string} $_POST.streamName The name of the stream the user will be invited to
 * @param {string} [$_POST.userId] user id or an array of user ids
 * @param {string} [$_POST.platform] platform for which xids are passed
 * @param {string|array} [$_POST.xid]  platform xid or array of xids
 * @param {string} [$_POST.label]  label or an array of labels, or tab-delimited string
 * @param {string} [$_POST.identifier] identifier or an array of identifiers
 * @param {boolean|array} [$_POST.token=false] pass true here to save a Streams_Invite row
 *  with empty userId, which is used whenever someone shows up with the token
 *  and presents it via "Q.Streams.token" querystring parameter.
 *  This row is stored under "invite" key of the returned array.
 *  See the Streams/before/Q_objects.php hook for more information.
 *  You can also pass an array with two keys here, named "suggestion" and "Q.sig"
 *  which were generated by Streams_invite_response_suggestion handler.
 * @param {string} [$_POST.token.suggestion] Suggestion generated by Streams/invite "suggestion"
 * @param {string} [$_POST.token.Q.sig] Signature generated by Streams/invite "suggestion"
 * @param {string} [$_POST]
 * @param {string|array} [$_POST.addLabel] label or an array of labels for adding publisher's contacts
 * @param {string|array} [$_POST.addMyLabel] label or an array of labels for adding logged-in user's contacts
 * @param {string} [$_POST.readLevel] the read level to grant those who are invited
 * @param {string} [$_POST.writeLevel] the write level to grant those who are invited
 * @param {string} [$_POST.adminLevel] the admin level to grant those who are invited
 * @param {array} [$options.permissions] array of additional permissions to grant
 * @param {timestamp} [$_POST.expires] you can pass a timestamp that takes place in the future
 * @param {string} [$_POST.appUrl] Can be used to override the URL to which the invited user will be redirected and receive "Q.Streams.token" in the querystring.
 */
function Streams_invite_post()
{
	$publisherId = Streams::requestedPublisherId(true);
	$streamName = Streams::requestedName(true);
	
	$r = Q::take($_POST, array(
		'readLevel', 'writeLevel', 'adminLevel', 'permissions', 'expires',
		'addLabel', 'addMyLabel', 'appUrl', 'alwaysSend', 'assign',
		'userId', 'xid', 'platform', 'label', 'identifier', 'token'
	));

	$stream = Streams_Stream::fetch(null, $publisherId, $streamName, true);
	Streams::$cache['invite'] = $data = Streams::invite($publisherId, $streamName, $r, $r);
	if (!empty($data['invite'])) {
		$data['url'] = $data['invite']->url();
		$data['invite'] = $data['invite']->exportArray();
	}
	
	// do not give the clients an easy to way to find userIds by identifiers and xids
	unset($data['userIds']);
	unset($data['alreadyParticipating']);
	
	Q_Response::setSlot('stream', $stream->exportArray());
	Q_Response::setSlot('data', $data);
}
