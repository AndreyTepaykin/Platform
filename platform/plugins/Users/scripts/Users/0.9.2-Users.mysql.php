<?php

function Users_0_9_2_Users_mysql()
{
	$app = Q_Config::expect('Q', 'app');
	$communityId = Users::communityId();
	$limit = 20;
	$offset = 0;
	$sessions = Users_Session::select('*')
		->orderBy('id')
		->limit($limit, $offset)
		->caching(false)
		->fetchDbRows();
	echo "Adding userId to sessions";
	while ($sessions) {
		echo ".";
		foreach ($sessions as $s) {
			$parsed = Q::json_decode($s->content, true);
			if (empty($parsed['Users']['loggedInUser']['id'])) {
				continue;
			}
			$s->userId = $parsed['Users']['loggedInUser']['id'];
			$s->save();
		}
		$offset += $limit;
		$sessions = Users_Session::select('*')
			->orderBy('id')
			->limit($limit, $offset)
			->caching(false)
			->fetchDbRows();
	}
	echo "\n";
}

Users_0_9_2_Users_mysql();