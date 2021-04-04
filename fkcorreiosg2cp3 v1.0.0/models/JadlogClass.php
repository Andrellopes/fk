<?php

class JadlogClass {

    private $cnpj;
    private $senha;
    private $valorColeta;
    private $tipoSeguro;
    private $pagamento;
    private $tipoEntrega;
    private $modalidadeFrete;
    private $cepOrigem;
    private $cepDestino;
    private $valorPedido;
    private $pesoPedido;

    private $valorFrete;
    private $prazoEntrega;
    private $codRetorno;
    private $msgRetorno;

    public function setCnpj($cnpj) {
        $this->cnpj = preg_replace("/[^0-9]/", "", $cnpj);
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function setValorColeta($valorColeta) {
        $this->valorColeta = str_replace(".", ",", $valorColeta);
    }

    public function setTipoSeguro($tipoSeguro) {
        $this->tipoSeguro = $tipoSeguro;
    }

    public function setPagamento($pagamento) {
        $this->pagamento = $pagamento;
    }

    public function setTipoEntrega($tipoEntrega) {
        $this->tipoEntrega = $tipoEntrega;
    }

    public function setModalidadeFrete($modalidadeFrete) {
        $this->modalidadeFrete = $modalidadeFrete;
    }

    public function setCepOrigem($cepOrigem) {
        $this->cepOrigem = preg_replace("/[^0-9]/", "", $cepOrigem);
    }

    public function setCepDestino($cepDestino) {
        $this->cepDestino = preg_replace("/[^0-9]/", "", $cepDestino);
    }

    public function setValorPedido($valorPedido) {
        $this->valorPedido = str_replace(".", ",", $valorPedido);
    }

    public function setPesoPedido($pesoPedido) {
        $this->pesoPedido = str_replace(".", ",", $pesoPedido);
    }

    public function getPrazoEntrega() {
        return $this->prazoEntrega;
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

    public function calculaPrecoPrazo() {

        $this->valorFrete = 50;
        return true;

        /*
        $parm = array(
            'vModalidade'   => $this->modalidadeFrete,
            'Password'      => $this->senha,
            'vSeguro'       => $this->tipoSeguro,
            'vVlDec'        => $this->valorPedido,
            'vVlColeta'     => $this->valorColeta,
            'vCepOrig'      => $this->cepOrigem,
            'vCepDest'      => $this->cepDestino,
            'vPeso'         => $this->pesoPedido,
            'vFrap'         => $this->pagamento,
            'vEntrega'      => $this->tipoEntrega,
            'vCnpj'         => $this->cnpj,
        );

        try {
            $ws = new SoapClient(Configuration::get('FKCORREIOSG2CP3_URL_WS_JADLOG_FRETE'));
            $arrayRetorno = $ws->valorar($parm);
            $retorno = $arrayRetorno->valorarResponse->valorarReturn->Jadlog_Valor_Frete;

            if ($retorno->Valor > 0 And $retorno->PrazoEntrega > 0) {
                $this->valorFrete = $retorno->Valor;
                $this->prazoEntrega = $retorno->PrazoEntrega;
                $this->msgRetorno = $retorno->MsgErro;
            }else {
                if ($retorno->Erro == '0') {
                    $this->codRetorno = 'fk01';
                    $this->msgRetorno = '';
                }else {
                    $this->codRetorno = $retorno->Erro;
                    $this->msgRetorno = $retorno->MsgErro;
                }

                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->codRetorno = 'fk02';
            $this->msgRetorno = '';
            $this->valorFrete = 0;
            $this->prazoEntrega = 0;
            return false;
        }

        */

    }

}