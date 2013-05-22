<?php

header('Content-Type: application/json');
header_remove('X-Powered-By');

header('Access-Control-Allow-Origin: *');

$url = '(group_id = 1937 OR group_id = 2371 OR group_id = 2220 OR group_id = 2195)';
$url .= " AND '" . date('Y-m-d', strtotime('-1 week')) . "' <= end_date";
$url .= " AND ('Wölflinge' IN keywords OR 'Pfadfinder' IN keywords OR 'Ranger & Rover' IN keywords)";

$url = 'http://www.scoutnet.de/api/0.1/events/?json=[%22' . rawurlencode($url) . '%22]';

readfile($url);
