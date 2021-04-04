<?php


class FKcustomersClass {

    public function duplicidadeCPF_CNPJ($cpf_cnpj, $id_cliente) {

        // Se o cliente ja possui cadastro
        if ($id_cliente > 0) {
            // Verifica se a duplicidade é do proprio cliente
            $dados = Db::getInstance()->executeS('SELECT cpf_cnpj FROM '._DB_PREFIX_.'customer WHERE cpf_cnpj = "'.$cpf_cnpj.'" AND id_customer = '.(int)$id_cliente);

            if ($dados) {
                return false;
            }
        }

        // Se for cliente novo ou esta alterando CPF/CNPJ
        $dados = Db::getInstance()->executeS('SELECT cpf_cnpj FROM '._DB_PREFIX_.'customer WHERE cpf_cnpj = "'.$cpf_cnpj.'"');

        if (!$dados) {
            return false;
        }

        return true;

    }

    public function pesquisaCepRV($cep) {

        $url = "http://cep.republicavirtual.com.br/web_cep.php?cep=".$cep."&formato=json";
        $resp = file_get_contents($url);

        return $resp;

    }

    public function pesquisaCepBY($cep) {

        $url = "http://www.byjg.com.br/site/webservice.php/ws/cep?httpmethod=obterlogradouroauth";
        $url .=	"&cep=".$cep;
        $url .=	"&usuario=".Configuration::get('FKCUSTOMERS_USUARIOBY');
        $url .=	"&senha=".Configuration::get('FKCUSTOMERS_SENHABY');

        $resp = file_get_contents($url);

        return $resp;

    }

    public function pesquisaCepAC($cep) {

        $url = 'http://www.autocep.com.br/webcep/wsEndereco.asmx?WSDL';

        $parametros = array(
            'idCliente' => Configuration::get('FKCUSTOMERS_CODIGOAC'),
            'chave'     => Configuration::get('FKCUSTOMERS_CHAVEAC'),
            'CEP'       => $cep
        );

        try {
            $ws = new SoapClient($url);
            $result = $ws->AutenticaClienteXml($parametros);
            $autentica =  (array)$result->AutenticaClienteXmlResult;
            $xml =  new SimpleXMLElement($autentica['any']);

            if (strcmp((string)$xml->EnderecoCEP->IDRESULTADO,'002') == 0) {
                $retorno = '
                {
                    "uf" 			    : "'.(string)$xml->EnderecoCEP->UF.'",
                    "cidade" 		    : "'.ucwords(strtolower((string)$xml->EnderecoCEP->CIDADE)).'",
                    "bairro" 		    : "'.ucwords(strtolower((string)$xml->EnderecoCEP->BAIRRO1)).'",
                    "tipo_logradouro"   : "'.ucwords(strtolower((string)$xml->EnderecoCEP->TIPO)).'",
                    "logradouro" 	    : "'.ucwords(strtolower((string)$xml->EnderecoCEP->NOME)).'",
                    "resultado" 		: "1"
                }';
            }else {
                $retorno = '
                {
                    "uf" 			    : "",
                    "cidade" 		    : "",
                    "bairro" 		    : "",
                    "tipo_logradouro" 	: "",
                    "logradouro" 		: "",
                    "resultado" 		: "0"
                }';
            }

        }catch (Exception $e) {
            $retorno = '
                {
                    "uf" 			    : "",
                    "cidade" 		    : "",
                    "bairro" 		    : "",
                    "tipo_logradouro" 	: "",
                    "logradouro" 		: "",
                    "resultado" 		: "0"
                }';
        }

        return $retorno;

    }

    public function pesquisaCepCO($cep) {

        $url = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';

        $parametros = array(
            'cep'       => $cep
        );

        try {
            $ws = new SoapClient($url);
            $result = $ws->consultaCep($parametros);

            if (count((array)$result)) {
                $json  = json_encode($result->return);
                return $json;
            }

            return false;

        }catch (Exception $e) {
            return false;
        }

    }

    public function pesquisaUF($uf) {

        // Recupera id_country do Brasil
        $dados = Db::getInstance()->getRow('SELECT id_country FROM `'._DB_PREFIX_.'country` WHERE `iso_code` = "br" Or `iso_code` = "BR"');
        $id_country = $dados['id_country'];

        // Recupera id_state da UF do cliente
        $dados = Db::getInstance()->getRow('SELECT id_state FROM `'._DB_PREFIX_.'state` WHERE `id_country` = '.$id_country.' And `iso_code` = "'.$uf.'"');

        if ($dados) {
            return $dados['id_state'];
        }else {
            return '';
        }
    }

    public function validaEndereco($end_original) {

        $endereco = $end_original;
        $numero = '';
        $complemento = '';
        $bairro = '';

        $end_split = preg_split("/[-,\\n]/", $end_original);

        if (count($end_split) == 4) {
            list ($endereco, $numero, $complemento, $bairro) = $end_split;
        } elseif (count($end_split) == 3) {
            list ($endereco, $numero, $complemento) = $end_split;
        } elseif (count($end_split) == 2) {
            list ($endereco, $numero, $complemento) = $this->ordenaDados($end_original);
        } else {
            $endereco = $end_original;
        }

        if (trim($numero) == '') {
            return 0;
        }

        return 1;

    }

    private function ordenaDados($end_original) {

        $end_split = preg_split('/[-,\\n]/', $end_original);

        for ($i = 0; $i < strlen($end_split[0]); $i ++) {
            if (is_numeric(substr($end_split[0], $i, 1))) {
                return array(
                    substr($end_split[0], 0, $i),
                    substr($end_split[0], $i),
                    $end_split[1]
                );
            }
        }

        $end_original = preg_replace('/\s/', ' ', $end_original);
        $encontrar = substr($end_original, - strlen($end_original));
        for ($i = 0; $i < strlen($end_original); $i ++) {
            if (is_numeric(substr($encontrar, $i, 1))) {
                return array(
                    substr($end_original, 0, - strlen($end_original) + $i),
                    substr($end_original, - strlen($end_original) + $i),
                    ''
                );
            }
        }
    }

    public function validaDDD($telefone) {

        // Deixa somente numeros
        $telefone = preg_replace('/[^0-9]/','', $telefone);

        if (!$telefone) {
            return true;
        }

        $ddd = '|'.substr($telefone, 0, 2).'|';
        $ddd_validos = Configuration::get('FKCUSTOMERS_DDD');

        if (strpos($ddd_validos, $ddd) === false) {
            return false;
        }

        return true;

    }

}