<?php

function Q_before_Q_view ($params, &$result) {
	extract($params);
	if (strtolower(substr($viewName, -9)) === '.mustache') {
		$result = Q_Mustache::render($viewName, $params);
		return false;
	}
	if (strtolower(substr($viewName, -11)) === '.handlebars') {
		$result = Q_Handlebars::render($viewName, $params);
		return false;
	}
}