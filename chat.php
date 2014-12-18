<?php

require_once 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$users = new SplObjectStorage();
$i = 0;

#Connections
$socket->on('connection', function ($conn) use ($users, &$i) {
	$conn->id = ++$i;
	$conn->write('Please enter Your Username: ');

	$conn->on('data', function ($message) use ($users, $conn) {

		if (empty($conn->username)) {
			$conn->username = str_replace(array("\r", "\r\n", "\n"), '', $message);

		} else {
			foreach ($users as $user) {

				if ($user->id == $conn->id) {
					continue;
				}

				$user->write(sprintf('[%s] %s', $conn->username, $message));

			}
		}
	});

	$users->attach($conn);
});
if ($socket) {
	echo "Socket server listening on port 1211.\n";
	echo "You can connect to it by running in other terminal: netcat localhost 1211\n";
}

$socket->listen(1211);
$loop->run();