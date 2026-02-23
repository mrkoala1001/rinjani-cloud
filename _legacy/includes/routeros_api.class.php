<?php
/*******************************************************************************
 *
 *  COMPACT ROUTEROS API CLASS v1.6
 *  Author: Denis Basta
 *  Contributors: Ben Menking, Jeremy
 *
 *  This file is part of routeros-api
 *
 ******************************************************************************/

namespace RouterOS;

class Client
{
    var $debug = false; // Show debug info?
    var $connected = false; // Connection state
    var $port = 8728; // Port
    var $ssl = false; // Use SSL?
    var $timeout = 3; // Connection timeout
    var $attempts = 5; // Connection attempts
    var $delay = 3; // Delay between connection attempts

    var $socket;
    var $error_no;
    var $error_str;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Connect to RouterOS
     *
     * @param string $ip Hostname or IP
     * @param string $login Username
     * @param string $password Password
     *
     * @return boolean
     */
    public function connect($ip, $login, $password)
    {
        for ($ATTEMPT = 1; $ATTEMPT <= $this->attempts; $ATTEMPT++) {
            $this->connected = false;
            $protocol = ($this->ssl ? "ssl://" : "");
            $context = stream_context_create(array('ssl' => array('ciphers' => 'ADH:ALL', 'verify_peer' => false, 'verify_peer_name' => false)));
            $this->debug('Connection attempt #' . $ATTEMPT . ' to ' . $protocol . $ip . ':' . $this->port . '...');
            $this->socket = @stream_socket_client($protocol . $ip . ':' . $this->port, $this->error_no, $this->error_str, $this->timeout, STREAM_CLIENT_CONNECT, $context);
            if ($this->socket) {
                socket_set_timeout($this->socket, $this->timeout);
                $this->write('/login', false);
                $this->write('=name=' . $login, false);
                $this->write('=password=' . $password);
                $RESPONSE = $this->read(false);
                if (isset($RESPONSE[0]) && $RESPONSE[0] == '!done') {
                    if (!isset($RESPONSE[1])) {
                        // Login successful
                        $this->connected = true;
                        break;
                    } else {
                        // Challenge response login
                        $MATCH = [];
                        if (preg_match_all('/[^=]+/i', $RESPONSE[1], $MATCH)) {
                            if ($MATCH[0][0] == 'ret' && strlen($MATCH[0][1]) == 32) {
                                $this->write('/login', false);
                                $this->write('=name=' . $login, false);
                                $this->write('=response=00' . md5(chr(0) . $password . pack('H*', $MATCH[0][1])));
                                $RESPONSE = $this->read(false);
                                if (isset($RESPONSE[0]) && $RESPONSE[0] == '!done') {
                                    $this->connected = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                fclose($this->socket);
            }
            sleep($this->delay);
        }

        if ($this->connected) {
            $this->debug('Connected...');
        } else {
            $this->debug('Error...');
        }
        return $this->connected;
    }

    /**
     * Disconnect from RouterOS
     */
    public function disconnect()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->connected = false;
        $this->debug('Disconnected...');
    }

    /**
     * Request from RouterOS
     *
     * @param string $command Command
     * @param boolean $d Parse request?
     *
     * @return mixed
     */
    public function comm($command, $d = array())
    {
        if (is_array($command)) {
            $command = implode("\n", $command);
        }
        $this->debug('<<< [CMD] ' . str_replace("\n", " ", $command));

        if (count($d)) {
            $this->write($command, false);
            foreach ($d as $k => $v) {
                switch ($k[0]) {
                    case "?":
                        $el = "$k=$v";
                        break;
                    case "~":
                        $el = "$k~$v";
                        break;
                    default:
                        $el = "=$k=$v";
                        break;
                }
                $this->write($el, false);
            }
            $this->write(null);
        } else {
            $this->write($command);
        }

        $tl = $this->read();

        return $tl;
    }


    /**
     * Write to RouterOS
     *
     * @param string $command Command
     * @param boolean $param2 Add null byte?
     *
     * @return integer
     */
    public function write($command, $param2 = true)
    {
        if ($command) {
            $data = explode("\n", $command);
            foreach ($data as $content) {
                // Determine length
                $this->debug('>>> [Length] ' . strlen($content));
                $length = strlen($content);
                if ($length < 128) {
                    $tmp = chr($length);
                    $this->debug('>>> [Bytes] 1');
                } elseif ($length < 16384) {
                    $tmp = chr(0x80 + ($length >> 8)) . chr($length & 0xff);
                    $this->debug('>>> [Bytes] 2');
                } elseif ($length < 2097152) {
                    $tmp = chr(0xC0 + ($length >> 14)) . chr(($length >> 8) & 0xff) . chr($length & 0xff);
                    $this->debug('>>> [Bytes] 3');
                } elseif ($length < 268435456) {
                    $tmp = chr(0xE0 + ($length >> 22)) . chr(($length >> 14) & 0xff) . chr(($length >> 8) & 0xff) . chr($length & 0xff);
                    $this->debug('>>> [Bytes] 4');
                } elseif ($length < 34359738368) {
                    $tmp = chr(0xF0 + ($length >> 30)) . chr(($length >> 22) & 0xff) . chr(($length >> 14) & 0xff) . chr(($length >> 8) & 0xff) . chr($length & 0xff);
                    $this->debug('>>> [Bytes] 5');
                }
                fwrite($this->socket, $tmp . $content);
                $this->debug('>>> [Data] ' . $content);
            }
        }

        if ($param2) {
            fwrite($this->socket, chr(0));
            $this->debug('>>> [Null Byte]');
        }
        return true;
    }

    /**
     * Read from RouterOS
     *
     * @param boolean $parse Parse?
     *
     * @return mixed
     */
    public function read($parse = true)
    {
        $response = [];
        $header = true;
        while (true) {
            $byte = ord(fread($this->socket, 1));
            $length = 0;
            if ($byte & 128) {
                if (($byte & 192) == 128) {
                    $length = (($byte & 63) << 8) + ord(fread($this->socket, 1));
                } else {
                    if (($byte & 224) == 192) {
                        $length = (($byte & 31) << 8) + ord(fread($this->socket, 1));
                        $length = ($length << 8) + ord(fread($this->socket, 1));
                    } else {
                        if (($byte & 240) == 224) {
                            $length = (($byte & 15) << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        } else {
                            $length = ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                            $length = ($length << 8) + ord(fread($this->socket, 1));
                        }
                    }
                }
            } else {
                $length = $byte;
            }

            $_ = "";
            if ($length > 0) {
                $_ = "";
                $retlen = 0;
                while ($retlen < $length) {
                    $toread = $length - $retlen;
                    $_ .= fread($this->socket, $toread);
                    $retlen = strlen($_);
                }
                $response[] = $_;
                $this->debug('<<< [Response] ' . $_);
            }

            if ($_ == "!done") {
                $header = false;
            }
            if ($_ == '!trap') {
                // Trap
            } elseif ($_ == '!re') {
                // Re
            }

            if (!$header && $length == 0) {
                break;
            }
        }

        if ($parse) {
            return $this->parseResponse($response);
        } else {
            return $response;
        }
    }

    /**
     * Parse response from RouterOS
     *
     * @param array $response Response
     *
     * @return array
     */
    private function parseResponse($response)
    {
        $parsed = [];
        $CURRENT = null;
        $single = null;

        foreach ($response as $x) {
            if (in_array($x, array('!fatal', '!re', '!trap'))) {
                if ($x == '!re') {
                    $CURRENT =& $parsed[];
                } else {
                    $CURRENT =& $parsed[$x][];
                }
            } elseif ($x != '!done') {
                $matches = [];
                if (preg_match_all('/^=([^=]+)=(.*)$/', $x, $matches)) {
                    $CURRENT[$matches[1][0]] = $matches[2][0];
                }
            }
        }

        if (empty($parsed) && !empty($response) && $response[0] == '!done' && isset($response['ret'])) {
            return $response['ret'];
        }

        return $parsed;
    }

    /**
     * Write debug info
     *
     * @param string $text Text
     */
    private function debug($text)
    {
        if ($this->debug) {
            error_log($text);
        }
    }
}
