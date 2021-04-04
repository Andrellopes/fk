<?php

include_once(dirname(__FILE__).'/../../../config/config.inc.php');

class FKparcg2Class
{

    public function procParcelamentoProduto($valor)
    {
        // Formata valor
        $valor = trim(preg_replace("/[^0-9]/", "", $valor));
        $valor = $valor / 100;

        // Inicia estrutura json
        $json = '{';

        // Inicia array parcelamento1
        $json .= '"parcelamento1":';
        $json .= '[';

        if ($valor > 0) {

            // Cria array com fatores
            $fatores = explode('|', Configuration::get('FKPARCG2_FATORES_1'));

            // Recupera valor minimo da parcela
            $valorMinimo = Configuration::get('FKPARCG2_VALOR_MIN_1');

            $parcela = 0;

            foreach ($fatores as $fator) {

                $parcela++;

                $valorParcela = $valor * $fator;

                if ($valorParcela >= $valorMinimo) {

                    // Grava json
                    if ($parcela == '1') {
                        $json .= '{';
                    } else {
                        $json .= ',{';
                    }

                    $json .= '"parcela": "'.$parcela.'", ';
                    $json .= '"valor": "'.number_format($valorParcela, 2, ',', '.').'"';
                    $json .= '}';
                }
            }
        }

        // Finaliza array parcelamento1
        $json .= '],';

        // Inicia array parcelamento2
        $json .= '"parcelamento2":';
        $json .= '[';

        if ($valor > 0) {

            // Cria array com fatores
            $fatores = explode('|', Configuration::get('FKPARCG2_FATORES_2'));

            // Recupera valor minimo da parcela
            $valorMinimo = Configuration::get('FKPARCG2_VALOR_MIN_2');

            $parcela = 0;

            foreach ($fatores as $fator) {

                $parcela++;

                $valorParcela = $valor * $fator;

                if ($valorParcela >= $valorMinimo) {

                    // Grava json
                    if ($parcela == '1') {
                        $json .= '{';
                    } else {
                        $json .= ',{';
                    }

                    $json .= '"parcela": "'.$parcela.'", ';
                    $json .= '"valor": "'.number_format($valorParcela, 2, ',', '.').'"';
                    $json .= '}';
                }
            }
        }

        // Finaliza array parcelamento2
        $json .= ']';

        // Fecha estrutura json
        $json .= '}';

        return $json;
    }

    public function processaParcelamentoCarrinho($valor, $id) {

        $parcelas = array();

        if ($valor > 0) {
            // Cria array com fatores
            $fatores = explode('|', Configuration::get('FKPARCG2_FATORES_'.$id));

            // Recupera valor minimo da parcela
            $valorMinimo = Configuration::get('FKPARCG2_VALOR_MIN_'.$id);

            $parcela = 0;

            foreach ($fatores as $fator) {

                $parcela++;

                $valorParcela = $valor * $fator;

                if ($valorParcela >= $valorMinimo) {
                    $parcelas[] = array('parcela' => $parcela, 'valor' => number_format($valorParcela, 2, ',', '.'));
                }
            }
        }


        return $parcelas;
    }
}