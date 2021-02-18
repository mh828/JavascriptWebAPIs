<?php
const MAGIC_STRING = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', 1223);
socket_listen($socket);
socket_set_block($socket);

while (true) {
    if (($newc = socket_accept($socket)) !== false) {
        $data = '';
        while ($input = socket_read($newc, 512)) {
            socket_set_nonblock($newc);
            $data .= $input;
        }
        $secWebSocketKey = findSecWebSocketKey($data);
        socket_write($newc, GenerateResponse($secWebSocketKey));
        socket_set_block($newc);

        $data = "";
        while ($input = socket_read($newc, 512)) {
            var_dump(strlen($input));
        }

    }

    echo "next while loop \n";
}


function findSecWebSocketKey($request): string
{
    $matches = null;
    preg_match('/Sec-WebSocket-Key\:\s+([^\r\n]*)\r\n/i', $request, $matches);

    return isset($matches[1]) ? $matches[1] : '';
}

function GenerateAcceptHeader($secWebsocketKey): string
{
    return base64_encode(sha1($secWebsocketKey . MAGIC_STRING, true));
}

function GenerateResponse($secWebsocketKey)
{
    $acceptKey = GenerateAcceptHeader($secWebsocketKey);
    $response = "HTTP/1.1 101 Switching Protocols\r\n";
    $response .= "Upgrade: websocket\r\n";
    $response .= "Connection: Upgrade\r\n";
    $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n";
    $response .= "\r\n";

    return $response;
}