<?php

class JamefClass {

    private $cnpj;
    private $cepDestino;
    private $valorPedido;
    private $pesoPedido;
    private $cubagemPedido;
    private $codigoFilial;
    private $ufFilial;

    private $valorFrete;
    private $codRetorno;
    private $msgRetorno;

    public function setCnpj($cnpj) {
        $this->cnpj = preg_replace("/[^0-9]/", "", $cnpj);
    }

    public function setCepDestino($cepDestino) {
        $this->cepDestino = preg_replace("/[^0-9]/", "", $cepDestino);
    }

    public function setValorPedido($valorPedido) {
        $this->valorPedido = number_format($valorPedido, 2, ',', '');
    }

    public function setPesoPedido($pesoPedido) {
        $this->pesoPedido = number_format($pesoPedido, 2, ',', '');
    }

    public function setCubagemPedido($cubagemPedido) {
        $this->cubagemPedido = number_format($cubagemPedido, 6, ',', '');
    }

    public function setCodigoFilial($codigoFilial) {
        $this->codigoFilial = $codigoFilial;
    }

    public function setUfFilial($ufFilial) {
        $this->ufFilial = $ufFilial;
    }

    public function getValorFrete() {
        return str_replace(",",".",$this->valorFrete);
    }

    public function getCodRetorno() {
        return $this->codRetorno;
    }

    public function getMsgRetorno() {
        return $this->msgRetorno;
    }

    public function calculaPreco() {

        $parm = array(
            'P_CIC_NEGC'    => $this->cnpj,
            'P_CEP'         => $this->cepDestino,
            'P_VLR_CARG'    => $this->valorPedido,
            'P_PESO_KG'     => $this->pesoPedido,
            'P_CUBG'        => $this->cubagemPedido,
            'P_COD_REGN'    => $this->codigoFilial,
            'P_UF'          => $this->ufFilial,
        );

        $url = Configuration::get('FKCORREIOSG2CP4_URL_JAMEF_CALCULO').'?'.http_build_query($parm, '', '&');
        $url = urldecode($url);

        // Chama o web service da JAMEF
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION , true);

        $retorno = curl_exec($curl);

        curl_close($curl);

        $retorno = str_replace('&amp;lt;sup&amp;gt;&amp;amp;reg;&amp;lt;/sup&amp;gt;', '', $retorno);
        $retorno = str_replace('&amp;lt;sup&amp;gt;&amp;amp;trade;&amp;lt;/sup&amp;gt;', '', $retorno);
        $retorno = str_replace('**', '', $retorno);
        $retorno = str_replace("\r\n", '', $retorno);
        $retorno = str_replace('\"', '"', $retorno);

        // Retorna se nao houve retorno da JAMEF
        if (!$retorno) {
            $this->valorFrete = 0;
            return false;
        }

        // Trata o retorno da JAMEF
        $xml = mb_convert_encoding($retorno, "UTF-8", "UTF-8,ISO-8859-1");
        $parser = xml_parser_create();

        if (!xml_parse($parser, $xml)) {
            $this->valorFrete = 0;
            return false;
        }

        $dom = new DOMDocument();
        $dom->loadXml($xml);

        $tagFrete = $dom->getElementsByTagName('frete');

        if ($tagFrete) {
            foreach ($tagFrete as $frete) {
                $dados = array(
                    "status"    => $frete->getElementsByTagName('status')->item(0)->nodeValue,
                    "msg"       => $frete->getElementsByTagName('msg')->item(0)->nodeValue,
                    "valor"     => $frete->getElementsByTagName('valor')->item(0)->nodeValue
                );
            }
        }else {
            $this->valorFrete = 0;
            return false;
        }

        // Verifica o valor retornado
        if ($dados['status'] == '2') {
            $this->valorFrete = 0;
            return false;
        }

        $this->valorFrete = $dados['valor'];
        return true;

    }

}