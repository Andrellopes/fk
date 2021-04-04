<?php

require_once('FKcarrierCorreios.php');

if (isset($_REQUEST['func'])) {

    switch ($_REQUEST['func']) {

        case '1':
            $funcao = new FKcarrierFuncoes();
            $retorno = $funcao->procTabOffline();
            echo $retorno;
            break;

        default:
            break;

    }
}

class FKcarrierFuncoes {

    public function procTabOffline() {

        // Recupera id_correios
        $correios_transp = Db::getInstance()->getRow('SELECT `id_correios` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id` ='.(int)$_REQUEST['idCorreiosTransp']);

        // Recupera dados do servico dos Correios
        $espec_correios = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_especificacoes_correios` WHERE `id` = '.(int)$correios_transp['id_correios']);

        // Recupera dados das tabelas offline
        $tabelas_offline = Db::getInstance()->getRow('SELECT `id_cadastro_cep`, `minha_cidade` FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` WHERE `id` = '.(int)$_REQUEST['idTabelasOff']);

        // Se não for minha cidade
        if ($tabelas_offline['minha_cidade'] == 0) {

            // Recupera dados do cadastro de cep
            $cadastro_cep = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep` WHERE `id` = '.(int)$tabelas_offline['id_cadastro_cep']);

            // Determina cep destino
            if ($_REQUEST['tipoTabela'] == 'capital') {
                $cep_destino = $cadastro_cep['cep_base_capital'];
            }else {
                $cep_destino = $cadastro_cep['cep_base_interior'];
            }

            // Verifica o intervalo de peso que dever ser calculado
            $estado_origem = $this->retornaUF(Configuration::get('FKCARRIER_MEU_CEP'));

            if ($estado_origem == 'erro') {
                return 'erro';
            }

            if ($estado_origem == $cadastro_cep['estado']) {
                $intervalo_peso = $espec_correios['intervalo_pesos_estadual'];
            }else {
                $intervalo_peso = $espec_correios['intervalo_pesos_nacional'];
            }
        }else {
            $cep_destino = Configuration::get('FKCARRIER_MEU_CEP');
            $intervalo_peso = $espec_correios['intervalo_pesos_estadual'];
        }

        // Cria array dos pesos
        $pesosArray = explode('/', $intervalo_peso);

        // Aciona webservice dos Correios
        $ws = new FKcarrierCorreios();

        $tabRetorno = '';

        foreach ($pesosArray as $peso) {

            if ($peso == '') {
                continue;
            }

            if ($peso > 0) {

                $ws->setEmpresa($espec_correios['cod_administrativo']);
                $ws->setSenha($espec_correios['senha']);
                $ws->setCodServico($espec_correios['cod_servico']);
                $ws->setCepOrigem(Configuration::get('FKCARRIER_MEU_CEP'));
                $ws->setCepDestino($cep_destino);
                $ws->setPeso($peso);
                $ws->setFormato('1');
                $ws->setComprimento($espec_correios['comprimento_min']);
                $ws->setAltura($espec_correios['altura_min']);
                $ws->setLargura($espec_correios['largura_min']);
                $ws->setDiametro('0');
                $ws->setMaoPropria('N');
                $ws->setValorDeclarado('0.00');
                $ws->setAvisoRecebimento('N');

                if ($ws->Calcular()) {
                    $tabRetorno .= $peso .':'.$ws->getValor().'/';
                }else {
                    $tabRetorno = 'erro: '.$ws->getMsgErro();
                    break;
                }
            }
        }

        return $tabRetorno;
    }

    public function retornaUF($cep) {

        $cep = preg_replace("/[^0-9]/", "", $cep);

        // Consulta cadastro de cep
        $cadastro_cep = Db::getInstance()->executeS('SELECT `estado`, `cep_estado` FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep`');

        $localizado = false;

        foreach ($cadastro_cep as $reg) {

            $cepArray = explode('/', $reg['cep_estado']);

            foreach ($cepArray as $intervalo_cep) {

                if ($intervalo_cep == '') {
                    continue;
                }

                if ($cep >= substr($intervalo_cep, 0, 8) And $cep <= substr($intervalo_cep, 9, 8)) {
                    $localizado = true;
                    break;
                }
            }

            if ($localizado == true){
                return $reg['estado'];
                break;
            }
        }

        return 'erro';
    }

    public function verificaUfCapital($cep_pesquisa) {

        $cadastro_cep = Db::getInstance()->executeS('SELECT `cep_capital` FROM '._DB_PREFIX_.'fkcarrier_cadastro_cep');

        foreach ($cadastro_cep as $reg) {

            $cep_capital = explode('/', $reg['cep_capital']);

            foreach ($cep_capital as $cep) {
                if ($cep == '') {
                    continue;
                }

                if ($cep_pesquisa >= substr($cep, 0, 8) And $cep_pesquisa <= substr($cep, 9, 8)) {
                    return true;
                }
            }
        }

        return false;
    }

}

