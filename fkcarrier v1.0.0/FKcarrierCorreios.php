<?php

    require_once(dirname(__FILE__).'/../../config/config.inc.php');

    class FKcarrierCorreios {

        private $_empresa;
        private $_senha;
        private $_codServico;
        private $_cepOrigem;
        private $_cepDestino;
        private $_peso;
        private $_formato;
        private $_comprimento;
        private $_altura;
        private $_largura;
        private $_diametro;
        private $_cubagem;
        private $_maoPropria;
        private $_valorDeclarado;
        private $_avisoRecebimento;

        private $_valor;
        private $_prazoEntrega;
        private $_codErro;
        private $_msgErro;

        private $_url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?WSDL';

        public function getEmpresa() {
            return $this->_empresa;
        }

        public function setEmpresa($empresa) {
            $this->_empresa = $empresa;
        }

        public function getSenha() {
            return $this->_senha;
        }

        public function setSenha($senha) {
            $this->_senha = $senha;
        }

        public function getCodServico() {
            return $this->_codServico;
        }

        public function setCodServico($codServico) {
            $this->_codServico = $codServico;
        }

        public function getCepOrigem() {
            return $this->_cepOrigem;
        }

        public function setCepOrigem($cepOrigem) {
            $this->_cepOrigem = preg_replace("/[^0-9]/", "", $cepOrigem);
        }

        public function getCepDestino() {
            return $this->_cepDestino;
        }

        public function setCepDestino($cepDestino) {
            $this->_cepDestino = preg_replace("/[^0-9]/", "", $cepDestino);
        }

        public function getPeso() {
            return $this->_peso;
        }

        public function setPeso($peso) {
            $this->_peso = str_replace(",",".",$peso);
        }

        public function getFormato() {
            return $this->_formato;
        }

        public function setFormato($formato) {
            $this->_formato = $formato;
        }

        public function getComprimento() {
            return $this->_comprimento;
        }

        public function setComprimento($comprimento) {
            $this->_comprimento = str_replace(",",".",$comprimento);
        }

        public function getAltura() {
            return $this->_altura;
        }

        public function setAltura($altura) {
            $this->_altura = str_replace(",",".",$altura);
        }

        public function getLargura() {
            return $this->_largura;
        }

        public function setLargura($largura) {
            $this->_largura = str_replace(",",".",$largura);
        }

        public function getDiametro() {
            return $this->_diametro;
        }

        public function setDiametro($diametro) {
            $this->_diametro = str_replace(",",".",$diametro);
        }

        public function getCubagem() {
            return $this->_cubagem;
        }

        public function setCubagem($cubagem) {
            $this->_cubagem = str_replace(",",".",$cubagem);
        }

        public function getMaoPropria() {
            return $this->_maoPropria;
        }

        public function setMaoPropria($maoPropria) {
            $this->_maoPropria = $maoPropria;
        }

        public function getValorDeclarado() {
            return $this->_valorDeclarado;
        }

        public function setValorDeclarado($valorDeclarado) {
            $this->_valorDeclarado = str_replace(",",".",$valorDeclarado);
        }

        public function getAvisoRecebimento() {
            return $this->_avisoRecebimento;
        }

        public function setAvisoRecebimento($avisoRecebimento) {
            $this->_avisoRecebimento = $avisoRecebimento;
        }

        public function getPrazoEntrega() {
            return $this->_prazoEntrega;
        }

        public function setPrazoEntrega($prazoEntrega) {
            $this->_prazoEntrega = $prazoEntrega;
        }

        public function getValor() {
            return str_replace(",",".",$this->_valor);
        }

        public function getCodErro() {
            return $this->_codErro;
        }

        public function getMsgErro() {
            return $this->_msgErro;
        }

        public function Calcular() {

            $parm = array(
                'nCdEmpresa'            => $this->_empresa,
                'sDsSenha'              => $this->_senha,
                'nCdServico'            => $this->_codServico,
                'sCepOrigem'            => $this->_cepOrigem,
                'sCepDestino'           => $this->_cepDestino,
                'nVlPeso'               => $this->_peso,
                'nCdFormato'            => $this->_formato,
                'nVlComprimento'        => $this->_comprimento,
                'nVlAltura'             => $this->_altura,
                'nVlLargura'            => $this->_largura,
                'nVlDiametro'           => $this->_diametro,
                'sCdMaoPropria'         => $this->_maoPropria,
                'nVlValorDeclarado'     => $this->_valorDeclarado,
                'sCdAvisoRecebimento'   => $this->_avisoRecebimento
            );

            // Aciona rotina para calculo de preco e prazo
            if ($this->calcPrecoPrazo($parm)) {
                return true;
            }

            return false;
        }

        private function calcPrecoPrazo($parm) {

            try {
                // Timeout do servidor
                ini_set('default_socket_timeout' , 60);

                // Chamada ao Soap especificando para disparar excessoes e aguardar ate 30 segundos para conseguir conexao
                $soap = new SoapClient($this->_url, array('exceptions' => 1, "connection_timeout" => 30));
                $resp = $soap->CalcPrecoPrazo($parm);
                $obj = $resp->CalcPrecoPrazoResult->Servicos->cServico;
                
                if ($obj->Valor > 0 And $obj->PrazoEntrega > 0) {
                    $this->_valor = $obj->Valor;
                    $this->_prazoEntrega = $obj->PrazoEntrega;
                }else {
                    if ($obj->Erro == '0') {
                        $this->_codErro = '99';
                        $this->_msgErro = 'O web services dos Correios retornou a transação OK mas com valor do frete ZERO.';
                    }else {
                        $this->_codErro = $obj->Erro;
                        $this->_msgErro = $obj->MsgErro;
                    }
                    
                    return false;
                }
                
                return true;

            } catch (Exception $e) {
                $this->_codErro = '99';
                $this->_msgErro = 'Erro indefinido.';
                $this->_valor = 0;
                $this->_prazoEntrega = 0;
                return false;
            } 
        }

        public function trataErro() {

            switch($this->_codErro) {

                case '-33':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'Sistema dos Correios fora do ar.');

                case '-888':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'Erro ao calcular a tarifa.');

                case '7':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'Serviço dos Correios indisponível.');
                    
                case '010':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'Área com entrega temporariamente sujeita a prazo diferenciado.');
                    
                case '012':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'O CEP de destino pertence a uma área com restrição temporária de entrega.');                         

                case '99':
                    return array('calculo_offline' => true, 'mensagem_erro' => 'Erro indefinido.');

            }

            return array('calculo_offline' => false, 'mensagem_erro' => $this->_msgErro);
        }

    }
