<?php
const MAGIC_STRING = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', 1223);
socket_listen($socket);
socket_set_block($socket);

while (true) {
    if (($newc = socket_accept($socket)) !== false) {
        $data = '';
        while (($input = socket_read($newc, 512)) && $input !== '') {

            echo "echo Read";
            var_dump($input);
            $data .= $input;
        }
        var_dump($data);
    }

    echo "next while loop \n";
}


function GenerateAcceptHeader($secWebsocketKey): string
{
    return base64_encode(sha1("dGhlIHNhbXBsZSBub25jZQ==" . MAGIC_STRING, true));
}