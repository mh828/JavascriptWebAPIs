<?php
const MAGIC_STRING = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

startServer();

function startServer()
{
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
            socket_write($newc, GenerateResponseHandShake($secWebSocketKey));
            socket_set_block($newc);

            while (true) {
                $data = "";
                while ($input = @socket_read($newc, 512)) {
                    socket_set_nonblock($newc);
                    $data .= $input;
                }
                if (empty($data))
                    break;//broken socket

                socket_set_block($newc);
                $receivedMessage = ClientMessage::DecodeClientMessage($data);

                //close handshake check
                if ($receivedMessage->getOpcode() === '1000') {
                    //close handshake
                    socket_write($newc, $data);
                }

                socket_write($newc, sendMessage($receivedMessage->getPayload(), false));
                /*if (!@socket_write($newc, sendMessage(file_get_contents('bridges.txt'), false)))
                    break;//broken socket*/


            }

        }

        echo "next while loop \n";
    }
}

function findSecWebSocketKey($request): string
{
    $matches = null;
    preg_match('/Sec-WebSocket-Key\:\s+([^\r\n]*)\r\n/i', $request, $matches);

    return isset($matches[1]) ? $matches[1] : '';
}

function GenerateAcceptHeaderKey($secWebsocketKey): string
{
    return base64_encode(sha1($secWebsocketKey . MAGIC_STRING, true));
}

function GenerateResponseHandShake($secWebsocketKey): string
{
    $acceptKey = GenerateAcceptHeaderKey($secWebsocketKey);
    $response = "HTTP/1.1 101 Switching Protocols\r\n";
    $response .= "Upgrade: websocket\r\n";
    $response .= "Connection: Upgrade\r\n";
    $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n";
    $response .= "\r\n";

    return $response;
}

function sendMessage($message, $mask = true): string
{
    $result = "";

    //FIN and RSV 1,2,3 and opcode 0X1 as text frame
    $result .= chr(bindec("10000001"));

    //mask and payload length
    $message_length = strlen($message);
    $payloadLen7Bit = '';
    $payLoadLenBytes = '';

    if ($message_length <= 125)
        $payloadLen7Bit = decbin($message_length);
    else if ($message_length > 125 && $message_length < 65536) {
        $payloadLen7Bit = decbin(126);
        $payLoadLenBytes = sprintf('%016b', $message_length);
    } else if ($message_length >= 65536) {
        $payloadLen7Bit = decbin(127);
        $payLoadLenBytes = sprintf('%064b', $message_length);
    }

    $result .= chr(bindec(($mask ? '1' : '0') . $payloadLen7Bit));
    //calculate payload
    if ($message_length > 125) {
        $rs = '';
        $temp = $payLoadLenBytes;
        while ($temp != '') {
            $rs .= chr(bindec(substr($temp, 0, 8)));
            $temp = substr($temp, 8);
        }
        $result .= $rs;
    }

    $result .= $message;

    return $result;
}


class ClientMessage
{
    private $fin;
    private $rsv1;
    private $rsv2;
    private $rsv3;
    private $opcode;
    private $mask;
    private $length;
    private $payload;
    private $masking_key;

    public function DecodeMessage($input)
    {
        $binary = decbin(ord($input[0]));
        $this->fin = $binary[0];
        $this->rsv1 = $binary[1];
        $this->rsv2 = $binary[2];
        $this->rsv3 = $binary[3];
        $this->opcode = substr($binary, 4);

        $masking_key = null;
        $binary = decbin(ord($input[1]));
        $last_read_byte = 1;

        $mask = bindec($binary[0]);
        $binary = substr($binary, 1);
        $length = bindec($binary);


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
        $EncodedData = substr($input, $last_read_byte + 1);
        for ($i = 0; $i < strlen($EncodedData); $i++) {
            $payloadData .= $EncodedData[$i] ^ $masking_key[$i % 4];
        }

        $this->mask = boolval($mask);
        $this->length = $length;
        $this->payload = $payloadData;
        $this->masking_key = $masking_key;

        return [
            'mask' => boolval($mask),
            'length' => $length,
            'masking_key' => $masking_key,
            'payload' => $payloadData
        ];
    }

    //<editor-fold desc="Getters">

    public function getMask()
    {
        return $this->mask;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getMaskingKey()
    {
        return $this->masking_key;
    }

    public function getFin()
    {
        return $this->fin;
    }

    public function getRsv1()
    {
        return $this->rsv1;
    }

    public function getRsv2()
    {
        return $this->rsv2;
    }

    public function getRsv3()
    {
        return $this->rsv3;
    }

    public function getOpcode()
    {
        return $this->opcode;
    }

    //</editor-fold>

    public static function DecodeClientMessage(string $message): ClientMessage
    {
        $cm = new ClientMessage();
        $cm->DecodeMessage($message);
        return $cm;
    }
}