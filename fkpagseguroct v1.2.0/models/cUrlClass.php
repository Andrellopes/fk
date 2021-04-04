<?php

class cUrlClass {

    private $status;
    private $resposta;
    private $msgErro;

    public function __construct() {
        
        if (!function_exists('curl_init')) {
            $this->setMsgErro('cURL não instalado');
            return false;
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getResposta() {
        return $this->resposta;
    }

    public function setResposta($resposta) {
        $this->resposta = $resposta;
    }
    
    public function getMsgErro() {
        return $this->msgErro;
    }
    
    public function setMsgErro($msg) {
        $this->msgErro = $msg;
    }

    public function post($url, array $data = null, $timeout = null, $charset = null, $httpVersion = null) {
        return $this->curlConnection('POST', $url, $data, $timeout, $charset, $httpVersion);
    }

    public function get($url, array $data = null, $timeout = null, $charset = null) {
        return $this->curlConnection('GET', $url, $data, $timeout, $charset);
    }

    private function curlConnection($method, $url, array $data = null, $timeout = 20, $charset = 'ISO-8859-1', $httpVersion = 'HTTP 1.0') {
        
        // Inicia cUrl
        $curl = curl_init();
        
        // Post ou Get
        if (strtoupper($method) === 'POST') {
            $postFields = ($data ? http_build_query($data, '', '&') : '');
            $contentLength = 'Content-length: '.strlen($postFields);
            
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        }else {
            $url = $url.'?'.http_build_query($data, '', '&');
            $contentLength = null;
            
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset='.$charset, $contentLength));

        if ($httpVersion == 'HTTP 1.0') {
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        }else {
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        }

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = curl_errno($curl);
        $errorMessage = curl_error($curl);
        
        curl_close($curl);
        
        $this->setStatus((int) $info['http_code']);
        $this->setResposta((String) $resp);

        if ($info['http_code'] == 200) {
            return true;
        }else {
            if ($error) {
                $this->setMsgErro('A conexão com o Pagseguro retornou com erro: '.$error.' - '.$errorMessage);
                return false;
            }else {
                return true;
            }
        }

    }
    
}