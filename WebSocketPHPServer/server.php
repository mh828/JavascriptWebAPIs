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

        while (true) {
            $data = "";
            while ($input = socket_read($newc, 512)) {
                socket_set_nonblock($newc);
                $data .= $input;
            }
            socket_set_block($newc);
            var_dump(DecodePayloadLength($data));
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

function GenerateResponse($secWebsocketKey): string
{
    $acceptKey = GenerateAcceptHeader($secWebsocketKey);
    $response = "HTTP/1.1 101 Switching Protocols\r\n";
    $response .= "Upgrade: websocket\r\n";
    $response .= "Connection: Upgrade\r\n";
    $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n";
    $response .= "\r\n";

    return $response;
}

function DecodePayloadLength($input)
{
    $masking_key = null;
    $binary = decbin(ord($input[1]));
    $last_read_byte = 1;
    $mask = bindec($binary[0]);
    $binary = substr($binary, 1);
    $length = bindec($binary);


    if ($length < 126)
        return $length;
    if ($length === 126) {
        $binary = decbin(ord($input[2])) . decbin(ord($input[3]));
        $last_read_byte = 3;
        $length = bindec($binary);
    }
    if ($length === 127) {
        $binary = decbin(ord($input[2])) .
            decbin(ord($input[3])) .
            decbin(ord($input[4])) .
            decbin(ord($input[5])) .
            decbin(ord($input[6])) .
            decbin(ord($input[7])) .
            decbin(ord($input[8])) .
            decbin(ord($input[9]));
        $last_read_byte = 9;
        $length = bindec($binary);
    }


    if ($mask) {//read masking key
        $masking_key = $input[$last_read_byte + 1] .
            $input[$last_read_byte + 2] .
            $input[$last_read_byte + 3] .
            $input[$last_read_byte + 4];
        $last_read_byte += 4;

    }

    $payloadData = '';
    for ($i = $last_read_byte; $i < strlen($input); $i++) {
        $payloadData .= $input[$i] ^ $masking_key[$i % 4];
    }


    return [
        'mask' => boolval($mask),
        'length' => $length,
        'masking_key' => $masking_key,
        'payload' => $payloadData
    ];
}