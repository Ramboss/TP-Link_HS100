<?php
    Header ("Content-type: text/plain");
    $host = '192.168.1.15';
    $port = 9999;
    if (isset($_GET['host'])) {$host = $_GET['host'];}
    if (isset($_GET['port'])) {$port = $_GET['port'];}
    switch(isset($_GET['com'])?$_GET['com']:'i') {
        case 'on': { $command = '{"system":{"set_relay_state":{"state": 1}}}'; break; }
        case 'off':{ $command = '{"system":{"set_relay_state":{"state": 0}}}'; break; }
        default:   { $command = '{"system":{"get_sysinfo":{}}}'; }
    }
    $fp = fsockopen($host, $port);
    if(!$fp) { exit; }
    $request = pack('N', strlen($command));
    $key = 0xAB;
    for ($i = 0; $i < strlen($command); $i++) {
        $key = ord(substr($command, $i, 1)) ^ $key;
        $request.= chr($key);
    }
    fwrite($fp, $request, strlen($request));
    $response = fread($fp, 4);
    $response = fread($fp, unpack('N', $response)[1]);
    fclose($fp);
    $key = 0xAB;
    $output = '';
    for ($i = 0; $i < strlen($response); $i++) {
        $nextKey = ord(substr($response, $i, 1));
        $output.= chr($nextKey ^ $key);
        $key = $nextKey;
    }
    print $output;
?>