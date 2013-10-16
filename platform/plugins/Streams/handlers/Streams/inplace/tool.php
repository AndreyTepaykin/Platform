<?php

/**
 * This tool generates an inline editor to edit the content or attribute of a stream.
 * @param array $options
 *  An associative array of parameters, containing:
 *  "fieldType" => Required. The type of the fieldInput. Can be "textarea" or "text"
 *  "stream" => A Streams_Stream object
 *  "editing" => If true, then renders the inplace tool in editing mode.
 *  "editOnClick" => Defaults to true. If true, then edit mode starts only if "Edit" button is clicked.
 *  "selectOnEdit" => Defaults to true. If true, selects all the text when entering edit mode.
 *  "beforeSave" => Optional, reference to a callback to call after a successful save.
 *     This callback can cancel the save by returning false.
 *  "onSave" => Optional, reference to a callback or event to run after a successful save.
 *  "onCancel" => Optional, reference to a callback or event to run after cancel.
 */
function Streams_inplace_tool($options)
{
	extract($options);
	Q_Response::setToolOptions(array(
		'publisherId' => $stream->publisherId,
		'streamName' => $stream->name
	));
	$options['action'] = $stream->actionUrl();
	$options['method'] = 'put';
	if (!empty($attribute)) {
		$field = 'attributes['.urlencode($attribute).']';
	}
	$field = 'content';
	switch ($fieldType) {
		case 'text':
			$options['fieldInput'] = Q_Html::input($field, $stream->content);
			$options['staticHtml'] = Q_Html::text($stream->content);
			break;
		case 'textarea':
			$options['fieldInput'] = Q_Html::textarea($field, 5, 80, $stream->content);
			$options['staticHtml'] = Q_Html::text($stream->content, array("\n"));
			break;
		default:
			return "fieldType must be 'textarea' or 'text'";
			break;
	}
	if (!$stream->testWriteLevel('editPending')) {
		return $options['staticHtml'];
	}
	return Q::tool("Q/inplace", $options);
}
