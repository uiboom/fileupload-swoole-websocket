<?php
$server = new swoole_websocket_server('0.0.0.0', 9701);
$server->set(array(
	'worker_num'=>2,
	'deamonize'=>true,
	'log_level'=>0,
	'log_file'=>'./swoole.log'
));

$server->on('open', function(swoole_websocket_server $server, $request) {
	echo "server: handshake success with fd {$request->fd} \n";
});

$server->on('message', function(swoole_websocket_server $server, $frame) {

	$out = fopen('/tmp/'.$frame->fd, 'a');
	while($in = fread($frame->data, 4096)) {
		fwrite($out, $in);
	}
	
	fclose($out);

	$server->push($frame->fd, "success");
});

$server->on('close', function($server, $fd) {
	echo "client fd {$fd} closed\n";
});

$server->start();
