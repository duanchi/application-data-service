<?php

//var_dump('HTTP/3.0' < 'HTTP/2.0');




//var_dump(dns_get_record('ssh.07studio.org'));
























$_handle = new HTTPTest();

$_handle->go();

class HTTPTest
{
    public function go() {

        $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

        $client->on("connect", [$this, 'on_connect']);

        $client->on("receive", [$this, 'on_receive']);

        $client->on("error", function($cli){
            exit("error\n");
        });

        $_response = $this->__response;
        $client->on("close", function($cli) use ($_response){
            exit("closed\n");
            //var_dump($_response);
        });


        $client->connect('127.0.0.1', 80, 0.5);

        var_dump($_response);
        /*$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_ASYNC);
        $client->on('connect', [$this, 'on_connect']);
        $client->on('receive', [$this, 'on_receive']);
        $client->on('error', [$this, 'on_error']);
        $client->on('close', [$this, 'on_close']);
        $client->connect('127.0.0.1', 80, 5);
        var_dump($client->errCode);
        echo '2';*/
    }

    public function on_connect($client) {
        $_package = 'GET /env.php HTTP/1.1
Host: api.ads.devel
Connection: keep-alive
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
Referer: http://baidu.com/
Accept-Encoding: gzip, deflate, sdch
Accept-Language: zh-CN,zh;q=0.8,en;q=0.6,ja;q=0.4
Cookie: BAIDUID=861720F2CFE8CCE349580E417B3BF241:FG=1

';
        echo 'connected';
        $client->send($_package);
    }

    public $_header_line    = 1;

    public $__count = 0;

    public $__response = [];

    public function on_receive($_instance, $data) {
        echo 'received';
        $_response                          = [];

        if ($this->_header_line == 1) {

            $_tmp_stream                    = explode("\r\n\r\n", $data, 2);

            if (count($_tmp_stream) == 2) {
                $_tmp_response_header       = $_tmp_stream[0];
                $_tmp_response_body         = $_tmp_stream[1];
            } else {
                \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);
            }

            $_tmp_response_header           = explode("\r\n", $_tmp_response_header);

            $_response['line']              = array_shift($_tmp_response_header);

            $_tmp_response_line             = explode(' ', $_response['line'], 2);

            if (count($_tmp_response_line) == 2) {
                $_response['version']       = $_tmp_response_line[0];
                $_response['status']        = $_tmp_response_line[1];
            } else {
                \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);
            }

            $_response['header']            = [];
            foreach ($_tmp_response_header as $_value) {
                $_tmp_header                = explode(': ', $_value, 2);

                count($_tmp_header) == 2 ?
                    $_response['header'][strtolower($_tmp_header[0])]   = $_tmp_header[1]
                    :
                    $_response['header'][$_tmp_header[0]]               = $_tmp_header[0];
            }

            $_tmp_response_body             = explode("\r\n", $_tmp_response_body, 2);
            $_response['body']              = rtrim($_tmp_response_body[1], "\r\n");

            $this->_header_line             = 0;

            $_instance->recv();
        } else {
            $this->__response[] = $data;
        }
        //var_dump($tmp);

        echo '1';

        //var_dump($_response);
        //if ($this->__count > 2) $_instance->close();
        $_instance->close();
    }

    public function on_error($client) {
        //var_dump($client->errCode);
        echo 'error';
    }

    public function on_close($client) {
        echo 'closed AAA';
        var_dump($this->__response);
    }
}