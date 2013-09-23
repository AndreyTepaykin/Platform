<?php

function Streams_join_post()
{
	$user = Users::loggedInUser(true);
	$publisherId = Streams::requestedPublisherId();
	$streamName = Streams::requestedName(true);
	$streams = Streams::fetch($user->id, $publisherId, $streamName);
	if (empty($streams)) {
		throw new Q_Exception_MissingRow(array(
			'table' => 'stream',
			'criteria' => "publisherId = $publisherId and name = $streamName"
		));
	}
	$stream = reset($streams);
	$options = array();
	if (isset($_REQUEST['reason'])) {
		$options['reason'] = $_REQUEST['reason'];
	}
	if (isset($_REQUEST['enthusiasm'])) {
		$options['enthusiasm'] = $_REQUEST['enthusiasm'];
	}
	$stream->join($options, $participant);
	Q_Response::setSlot('participant', $participant->exportArray());
}