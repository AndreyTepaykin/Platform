<?php

function Streams_participating_response_content()
{
	$user = Users::loggedInUser(true);
	$loggedUserId = $user->id;
	$dbStreams = Db::connect('Streams');
	$participants = $dbStreams->rawQuery("select srt.*, sp.state, sp.subscribed, sp.streamType, sp.publisherId from 
	streams_related_to srt, streams_participant sp 
	where srt.toPublisherId='".$loggedUserId."' and srt.toStreamname='Streams/participating'
	and sp.publisherId=srt.fromPublisherId and sp.streamName=srt.fromStreamName and sp.userId='".$loggedUserId."' 
	order by sp.subscribed")->fetchDbRows();

	$skipOwnStreams = Q_Config::get('Streams', 'participating', 'skipOwnStreams', true);

	// group by stream type
	$participantsGrouped = array();
	foreach($participants as $participant) {
		if ($skipOwnStreams && $participant->publisherId == $loggedUserId) {
			continue;
		}

		if (!is_array($participantsGrouped[$participant->streamType])) {
			$participantsGrouped[$participant->streamType] = array();
		}

		$stream = Streams::fetchOne($loggedUserId, $participant->fromPublisherId, $participant->fromStreamName);
		$checked = $participant->subscribed == 'yes' ? 'checked' : '';
		$iconUrl = $stream->iconUrl('40.png');

		$participantsGrouped[$participant->streamType][] = Q::view("Streams/content/participatingItem.php",
			compact('stream', 'iconUrl', 'checked')
		);
	}

	Q_Response::addStylesheet("{{Streams}}/css/pages/participants.css");
	Q_Response::addScript("{{Streams}}/js/pages/participants.js");

	$emailSubscribed = $dbStreams->rawQuery("select state from users_email where userId='".$loggedUserId."' and address='".$user->emailAddress."'")->fetchDbRow();
	$emailSubscribed = count($emailSubscribed) && $emailSubscribed->state == 'active';

	$mobileSubscribed = $dbStreams->rawQuery("select state from users_mobile where userId='".$loggedUserId."' and number='".$user->mobileNumber."'")->fetchDbRow();
	$mobileSubscribed = count($mobileSubscribed) && $mobileSubscribed->state == 'active';

	return Q::view("Streams/content/participating.php", compact('participantsGrouped', 'user', 'emailSubscribed', 'mobileSubscribed'));
}
