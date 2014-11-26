<?php
	
/**
 * This tool renders the participants in a stream
 * @class Q columns
 * @constructor
 * @param {array} [options] Provide options for this tool
 *   @param {string} [options.publisherId] The id of the publisher
 *   @required
 *   @param {string} [options.streamName] The name of the stream
 *   @required
 *   @param {integer} [options.max]
 *    The number, if any, to show in the denominator of the summary
 *   @optional
 *   @param {integer} [options.maxShow]
 *    The maximum number of participants to fetch for display
 *   @optional
 *   @default 10
 *   @param {Q.Event} [options.onRefresh] An event that occurs when the tool is refreshed
 *   @optional
 *   @param {boolean} [options.renderOnClient]
 *    If true, only the html container is rendered, so the client will do the rest.
 *   @optional
 */
function Streams_participants_tool($options)
{
	if (!empty($options['renderOnClient'])) {
		Q_Response::setToolOptions($options);
		return '';
	}
 	$publisherId = Q::ifset($options, 'publisherId', null);
 	$streamName = Q::ifset($options, 'streamName', null);
	if (!isset($publisherId)) {
		$publisherId = Streams::requestedPublisherId(true);
	}
	if (!isset($streamName)) {
		$streamName = Streams::requestedName(true);
	}
	$max = Q_Config::get(
		'Streams', 'participants', 'max', 
		Q::ifset($options, 'max', 10)
	);
	
	$stream = Streams::fetchOne(null, $publisherId, $streamName);
	if (empty($stream)) {
		throw new Q_Exception_MissingRow(array(
			'table' => 'Stream', 
			'criteria' => "{publisherId: '$publisherId', name: '$streamName'}"
		));
	}
	if (!$stream->testReadLevel('participants')) {
		throw new Users_Exception_NotAuthorized();
	}
	$participants = $stream->getParticipants(compact('limit', 'offset', 'state'));
	
	Q_Response::addScript('plugins/Streams/js/Streams.js');
	Q_Response::addStylesheet('plugins/Streams/css/Streams.css');
	$options['rendered'] = true;
	Q_Response::setToolOptions($options);
	$stream->addPreloaded();
	
	$avatars = '';
	$c = 0;
	if ($participants) {
		$i = 0;
		foreach ($participants as $p) {
			if ($p->state !== 'participating') {
				continue;
			}
			++$c;
			if (empty($options['maxShow'])
			or ++$i <= $options['maxShow']) {
				$avatars .= Q::tool("Users/avatar", array(
					'userId' => $p->userId,
					'icon' => true,
					'short' => true
				), $p->userId);
			}
		}
	}
	$spans = "<span class='Streams_participants_avatars'>$avatars</span>"
		. "<span class='Streams_participants_blanks'></span>";
	$container = "<div class='Streams_participants_container'>$spans</div>";
	$count = "<span class='Streams_participants_count'>$c</span>";
	$m = isset($options['max']) ? '/'.$options['max'] : '';
	$max = "<span class='Streams_participants_max'>$m</span>";
	$summary = "<div class='Streams_participants_summary'><span>$count$max</span></div>";
	return $summary.$container;
}