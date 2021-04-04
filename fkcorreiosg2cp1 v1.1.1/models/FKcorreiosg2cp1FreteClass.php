<?php

if (file_exists(_PS_MODULE_DIR_.'fkcorreiosg2/models/FKcorreiosg2Class.php')) {
    include_once(_PS_MODULE_DIR_.'fkcorreiosg2/models/FKcorreiosg2Class.php');
}

include_once(_PS_MODULE_DIR_.'fkcorreiosg2cp1/defines/defines.php');

class FKcorreiosg2cp1FreteClass {

    // Variaveis das funcoes de retorno
    private $transportadoras = array();
    private $freteCarrier = array();

    // Retorno utilizado no simulador de frete
    public function getTransportadoras() {
        return $this->transportadoras;
    }

    // Retorno utilizado no getOrderShippingCost
    public function getFreteCarrier() {
        return $this->freteCarrier;
    }

    public function __construct() {
        $this->context = Context::getContext();
    }

    public function calculaFreteSimulador($origem, $dadosBasicos, $params) {

        // Recupera dados gerais
        $cepDestino = $dadosBasicos['cepDestino'];
        $ufDestino = $dadosBasicos['ufDestino'];
        $valorPedido = $dadosBasicos['valorPedido'];
        $freteGratisValor = $dadosBasicos['freteGratisValor'];
        $transpFreteGratisValor = $dadosBasicos['transpFreteGratisValor'];

        // Instancia FKcorreiosg2Class
        $fkclass = new FKcorreiosg2Class();

        // Recupera dados das transportadoras
        $sql = "SELECT
                    "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.*,
                    "._DB_PREFIX_."carrier.id_reference
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                    INNER JOIN "._DB_PREFIX_."carrier
                        ON "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.id_carrier = "._DB_PREFIX_."carrier.id_carrier
                WHERE "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.ativo = 1 AND
                      "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.id_shop = ".(int)$this->context->shop->id;

        $transportadoras = Db::getInstance()->executeS($sql);

        foreach ($transportadoras as $transp) {

            // Inicializa variaveis
            $produtos = array();
            $pesoPedido = 0;
            $freteGratisProdutos = false;
            $transpFreteGratisProdutos = 0;
            $fatorCubagem = 0;
            $prazoEntrega = '';
            $tipoTabela = '';
            $tabelaPreco = '';
            $valorAdicionalKilo = 0;
            $valorAdicionalFixo = 0;
            $freteMinimo = 0;
            $valorPedidoDescontoFrete = 0;
            $percentualDescontoFrete = 0;
            $regiaoSelecionada = false;

            // Filtro de grupo de clientes por transportadora
            if (!$fkclass->filtroClienteTransportadora($transp['id_carrier'])) {
                continue;
            }

            // Processa regioes
            $regioes = $this->recuperaRegioes($transp['id']);

            foreach ($regioes as $regiao) {

                // Verifica se a transportadora atende a regiao
                if (!$fkclass->filtroRegiao($regiao, $cepDestino, $ufDestino)) {
                    continue;
                }

                // Cria array de produtos
                if ($origem == 'produto') {
                    // Calcula cubagem
                    $cubagem = $params['product']->height * $params['product']->width * $params['product']->depth;

                    // Calcula valor do produto
                    $preco = $params['product']->price;
                    $impostos = $params['product']->tax_rate;
                    $valorProduto = $preco * (1 + ($impostos / 100));

                    // Recupera o peso do pedido
                    $pesoPedido = $params['product']->weight;

                    $produtos[] = array(
                        'id'                            => $params['product']->id,
                        'altura'                        => $params['product']->height,
                        'largura'                       => $params['product']->width,
                        'comprimento'                   => $params['product']->depth,
                        'peso'                          => ($params['product']->weight == 0 ? '0.01' : $params['product']->weight),
                        'cubagem'                       => $cubagem,
                        'valorProduto'                  => $valorProduto,
                        'adicionalEnvio'                => $params['product']->additional_shipping_cost,
                        'freteGratisProduto'            => false,
                    );
                }else {
                    foreach ($this->context->cart->getProducts() as $prod) {

                        // Ignora o produto se for virtual
                        if ($prod['is_virtual'] == 1) {
                            continue;
                        }

                        // Calcula cubagem
                        $cubagem = $prod['height'] * $prod['width'] * $prod['depth'];

                        for ($qty = 0; $qty < $prod['quantity']; $qty++) {

                            // Calcula o peso do pedido
                            $pesoPedido += $prod['weight'];

                            $produtos[] = array(
                                'id'                            => $prod['id_product'],
                                'altura'                        => $prod['height'],
                                'largura'                       => $prod['width'],
                                'comprimento'                   => $prod['depth'],
                                'peso'                          => ($prod['weight'] == 0 ? '0.01' : $prod['weight']),
                                'cubagem'                       => $cubagem,
                                'valorProduto'                  => $prod['price_wt'],
                                'adicionalEnvio'                => $prod['additional_shipping_cost'],
                                'freteGratisProduto'            => false,
                            );
                        }
                    }
                }

                // Processa os produtos
                foreach ($produtos as $key => $prod) {

                    // Filtro por peso do produto
                    if ($regiao['peso_maximo_produto'] > 0 and $prod['peso'] > $regiao['peso_maximo_produto']) {
                        continue 2;
                    }

                    // Filtro de produto por transportadora
                    if (!$fkclass->filtroProdutoTransportadora($prod['id'], $transp['id_reference'])) {
                        continue 2;
                    }

                    // Filtro por dimensoes e peso por transportadora
                    if (!$fkclass->filtroDimensoesPesoTransportadora($prod['id'], $transp['id_carrier'], $pesoPedido)) {
                        continue 2;
                    }

                    // Filtro de frete gratis por produto - altera o array de produtos
                    if ($fkclass->filtroFreteGratisProduto($prod['id'], $transp['id_carrier'], $cepDestino, $ufDestino)) {

                        $freteGratisProdutos = true;
                        $transpFreteGratisProdutos = $transp['id_carrier'];

                        // Altera array de produtos
                        $produtos[$key]['freteGratisProduto'] = true;
                        $produtos[$key]['adicionalEnvio'] = 0;
                    }
                }

                // Recupera dados das regioes
                $fatorCubagem = $regiao['fator_cubagem'];
                $prazoEntrega = $regiao['prazo_entrega'];
                $tipoTabela = $regiao['tipo_tabela'];
                $tabelaPreco = $regiao['tabela_preco'];
                $valorAdicionalKilo = $regiao['valor_adicional_kilo'];
                $valorAdicionalFixo = $regiao['valor_adicional_fixo'];
                $freteMinimo = $regiao['frete_minimo'];
                $valorPedidoDescontoFrete = $regiao['valor_pedido_desconto'];
                $percentualDescontoFrete = $regiao['percentual_desconto'];

                $regiaoSelecionada = true;
                break;

            }

            // Ignora a transportadora se nao selecionada regiao
            if (!$regiaoSelecionada) {
                continue;
            }

            // Ignora transportadora se Frete Gratis por Valor e configurado para mostrar somente a transportadora de Frete Gratis
            if (Configuration::get('FKCORREIOSG2_FRETE_GRATIS_DEMAIS_TRANSP') != 'on' and $transpFreteGratisValor != $transp['id_carrier'] and $freteGratisValor or
                Configuration::get('FKCORREIOSG2_FRETE_GRATIS_DEMAIS_TRANSP') != 'on' and $transpFreteGratisProdutos != $transp['id_carrier'] and $freteGratisProdutos) {
                continue;
            }

            // Monta array com os dados necessarios para o calculo
            $parm = array(
                'produtos'                      => $produtos,
                'valorPedido'                   => $valorPedido,
                'pesoPedido'                    => $pesoPedido,
                'tipoTabela'                    => $tipoTabela,
                'tabelaPreco'                   => $tabelaPreco,
                'fatorCubagem'                  => $fatorCubagem,
                'valorAdicionalKilo'            => $valorAdicionalKilo,
                'valorAdicionalFixo'            => $valorAdicionalFixo,
                'freteGratisValor'              => $freteGratisValor,
                'transpFreteGratisValor'        => $transpFreteGratisValor,
                'idTransp'                      => $transp['id'],
                'idCarrierAtual'                => $transp['id_carrier'],
                'prazoEntrega'                  => $prazoEntrega,
                'freteMinimo'                   => $freteMinimo,
                'tempoPreparacao'               => Configuration::get('FKCORREIOSG2_TEMPO_PREPARACAO'),
                'valorPedidoDescontoFrete'      => $valorPedidoDescontoFrete,
                'percentualDescontoFrete'       => $percentualDescontoFrete,
            );

            // Calcula valor do frete
            $retorno = $this->calculaValor($parm);

            // Ignora transportadora se nao calculado o valor do frete
            if (!$retorno['status']) {
                continue;
            }

            $valorFrete = $retorno['valorFrete'];

            // Formata prazo de entrega
            if (is_numeric($retorno['prazoEntrega'])) {
                if ($retorno['prazoEntrega'] == 0) {
                    $prazoEntrega = 'Entrega no mesmo dia';
                }else {
                    if ($retorno['prazoEntrega'] > 1) {
                        $prazoEntrega = 'Entrega em até '.$retorno['prazoEntrega'].' dias úteis';
                    }else {
                        $prazoEntrega = 'Entrega em '.$retorno['prazoEntrega'].' dia útil';
                    }
                }
            }else {
                $prazoEntrega = $retorno['prazoEntrega'];
            }

            // Grava array com as transportadoras
            $this->transportadoras[] = array(
                'urlLogo'               => Configuration::get('FKCORREIOSG2_URL_LOGO_PS').$transp['id_carrier'].'.jpg',
                'nomeTransportadora'    => $transp['nome_transp'],
                'prazoEntrega'          => $prazoEntrega,
                'mensagem'              => '',
                'valorFrete'            => $valorFrete,
            );
        }

        return true;
    }

    public function calculaFretePS($params, $idCarrier) {

        // Inicializa variaveis
        $cepDestino = '';
        $ufDestino = '';
        $valorPedido = 0;
        $freteGratisValor = false;
        $transpFreteGratisValor = 0;
        $produtos = array();
        $pesoPedido = 0;
        $freteGratisProdutos = false;
        $transpFreteGratisProdutos = 0;
        $fatorCubagem = 0;
        $prazoEntrega = '';
        $tipoTabela = '';
        $tabelaPreco = '';
        $valorAdicionalKilo = 0;
        $valorAdicionalFixo = 0;
        $freteMinimo = 0;
        $valorPedidoDescontoFrete = 0;
        $percentualDescontoFrete = 0;
        $regiaoSelecionada = false;

        // Se o cliente esta logado
        if ($this->context->customer->isLogged()) {
        
            $address = new Address($params->id_address_delivery);
        
            // Recupera CEP destino
            if ($address->postcode) {
                $cepDestino = $address->postcode;
            }
        }else {
            // Recupera CEP do cookie
            if ($this->context->cookie->fkcorreiosg2_cep_destino) {
                $cepDestino = $this->context->cookie->fkcorreiosg2_cep_destino;
            }
        }
        
        // Pedidos efetuados via Admin
        if (!$cepDestino) {
            $address = new Address($params->id_address_delivery);
        
            // Ignora Carrier se não existir CEP
            if (!$address->postcode) {
                return false;
            }
        
            $cepDestino = $address->postcode;
        }
        
        // Valida CEP destino
        $cepDestino = trim(preg_replace("/[^0-9]/", "", $cepDestino));
        
        // Ignora Carrier se o CEP for invalido
        if (strlen($cepDestino) <> 8) {
            return false;
        }
        
        // Instancia FKcorreiosg2Class
        $fkclass = new FKcorreiosg2Class();
        
        // Recupera UF destino
        $ufDestino = $fkclass->recuperaUF($cepDestino);
        
        // Ignora Carrier se UF Destino nao localizada
        if (!$ufDestino) {
            return false;
        }

        // Recupera valor do pedido
        if (isset($this->context->cart)) {
            $valorPedido = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        }else {
            // Para pedidos efetuados via Admin
            $cart = new cart($params->id);
            $valorPedido = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        }

        // Verifica frete gratis por valor
        $freteGratis = $fkclass->filtroFreteGratisValor($valorPedido, $cepDestino, $ufDestino);

        if ($freteGratis['status']) {
            $freteGratisValor = true;
            $transpFreteGratisValor = $freteGratis['idCarrier'];
        }
        
        // Recupera dados da transportadora
        $sql = "SELECT
                    "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.*,
                    "._DB_PREFIX_."carrier.id_reference
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                    INNER JOIN "._DB_PREFIX_."carrier
                        ON "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.id_carrier = "._DB_PREFIX_."carrier.id_carrier
                WHERE "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.ativo = 1 AND
                      "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.id_carrier = ".(int)$idCarrier." AND
                      "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras.id_shop = ".(int)$this->context->shop->id;
        
        $transp = Db::getInstance()->getRow($sql);
        
        // Ignora Carrier se nenhum dado foi selecionado
        if (!$transp) {
            return false;
        }
        
        // Filtro de grupo de clientes por transportadora
        if (!$fkclass->filtroClienteTransportadora($transp['id_carrier'])) {
            return false;
        }

        // Processa regioes
        $regioes = $this->recuperaRegioes($transp['id']);

        foreach ($regioes as $regiao) {

            // Verifica se a transportadora atende a regiao
            if (!$fkclass->filtroRegiao($regiao, $cepDestino, $ufDestino)) {
                continue;
            }

            // Cria array de produtos
            foreach ($params->getProducts() as $prod) {

                // Ignora o produto se for virtual
                if ($prod['is_virtual'] == 1) {
                    continue;
                }

                // Calcula cubagem
                $cubagem = $prod['height'] * $prod['width'] * $prod['depth'];

                for ($qty = 0; $qty < $prod['quantity']; $qty++) {

                    // Calcula o peso do pedido
                    $pesoPedido += $prod['weight'];

                    $produtos[] = array(
                        'id'                            => $prod['id_product'],
                        'altura'                        => $prod['height'],
                        'largura'                       => $prod['width'],
                        'comprimento'                   => $prod['depth'],
                        'peso'                          => ($prod['weight'] == 0 ? '0.01' : $prod['weight']),
                        'cubagem'                       => $cubagem,
                        'valorProduto'                  => $prod['price_wt'],
                        'adicionalEnvio'                => $prod['additional_shipping_cost'],
                        'freteGratisProduto'            => false,
                    );
                }
            }

            // Processa os produtos
            foreach ($produtos as $key => $prod) {

                // Filtro por peso do produto
                if ($regiao['peso_maximo_produto'] > 0 and $prod['peso'] > $regiao['peso_maximo_produto']) {
                    continue 2;
                }

                // Filtro de produto por transportadora
                if (!$fkclass->filtroProdutoTransportadora($prod['id'], $transp['id_reference'])) {
                    continue 2;
                }

                // Filtro por dimensoes e peso por transportadora
                if (!$fkclass->filtroDimensoesPesoTransportadora($prod['id'], $transp['id_carrier'], $pesoPedido)) {
                    continue 2;
                }

                // Filtro de frete gratis por produto - altera o array de produtos
                if ($fkclass->filtroFreteGratisProduto($prod['id'], $transp['id_carrier'], $cepDestino, $ufDestino)) {

                    $freteGratisProdutos = true;
                    $transpFreteGratisProdutos = $transp['id_carrier'];

                    // Altera array de produtos
                    $produtos[$key]['freteGratisProduto'] = true;
                    $produtos[$key]['adicionalEnvio'] = 0;
                }
            }

            // Recupera dados das regioes
            $fatorCubagem = $regiao['fator_cubagem'];
            $prazoEntrega = $regiao['prazo_entrega'];
            $tipoTabela = $regiao['tipo_tabela'];
            $tabelaPreco = $regiao['tabela_preco'];
            $valorAdicionalKilo = $regiao['valor_adicional_kilo'];
            $valorAdicionalFixo = $regiao['valor_adicional_fixo'];
            $freteMinimo = $regiao['frete_minimo'];
            $valorPedidoDescontoFrete = $regiao['valor_pedido_desconto'];
            $percentualDescontoFrete = $regiao['percentual_desconto'];

            $regiaoSelecionada = true;
            break;

        }

        // Ignora a transportadora se nao selecionada regiao
        if (!$regiaoSelecionada) {
            return false;
        }

        // Ignora transportadora se Frete Gratis por Valor e configurado para mostrar somente a transportadora de Frete Gratis
        if (Configuration::get('FKCORREIOSG2_FRETE_GRATIS_DEMAIS_TRANSP') != 'on' and $transpFreteGratisValor != $transp['id_carrier'] and $freteGratisValor or
            Configuration::get('FKCORREIOSG2_FRETE_GRATIS_DEMAIS_TRANSP') != 'on' and $transpFreteGratisProdutos != $transp['id_carrier'] and $freteGratisProdutos) {
            return false;
        }

        // Monta array com os dados necessarios para o calculo
        $parm = array(
            'produtos'                      => $produtos,
            'valorPedido'                   => $valorPedido,
            'pesoPedido'                    => $pesoPedido,
            'tipoTabela'                    => $tipoTabela,
            'tabelaPreco'                   => $tabelaPreco,
            'fatorCubagem'                  => $fatorCubagem,
            'valorAdicionalKilo'            => $valorAdicionalKilo,
            'valorAdicionalFixo'            => $valorAdicionalFixo,
            'freteGratisValor'              => $freteGratisValor,
            'transpFreteGratisValor'        => $transpFreteGratisValor,
            'idTransp'                      => $transp['id'],
            'idCarrierAtual'                => $transp['id_carrier'],
            'prazoEntrega'                  => $prazoEntrega,
            'freteMinimo'                   => $freteMinimo,
            'tempoPreparacao'               => Configuration::get('FKCORREIOSG2_TEMPO_PREPARACAO'),
            'valorPedidoDescontoFrete'      => $valorPedidoDescontoFrete,
            'percentualDescontoFrete'       => $percentualDescontoFrete,
        );

        // Calcula valor do frete
        $retorno = $this->calculaValor($parm);

        // Ignora transportadora se nao calculado o valor do frete
        if (!$retorno['status']) {
            return false;
        }

        $valorFrete = $retorno['valorFrete'];

        // Formata prazo de entrega
        if (is_numeric($retorno['prazoEntrega'])) {
            if ($retorno['prazoEntrega'] == 0) {
                $prazoEntrega = 'Entrega no mesmo dia';
            }else {
                if ($retorno['prazoEntrega'] > 1) {
                    $prazoEntrega = 'Entrega em até '.$retorno['prazoEntrega'].' dias úteis';
                }else {
                    $prazoEntrega = 'Entrega em '.$retorno['prazoEntrega'].' dia útil';
                }
            }
        }else {
            $prazoEntrega = $retorno['prazoEntrega'];
        }

        // Grava array com os dados de frete
        $this->freteCarrier = array(
            'prazoEntrega'          => $prazoEntrega,
            'valorFrete'            => $valorFrete,
        );

        return true;
    }

    public function recuperaTranspFreteGratis() {

        $sql = "SELECT id_carrier, nome_transp AS transportadora
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_transportadoras
                WHERE id_shop = ".(int)$this->context->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }
    
    private function recuperaRegioes($transp) {
        
        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_regioes
                WHERE ativo = 1 AND
                      id_shop = ".(int)$this->context->shop->id." AND
                      id_transp = ".(int)$transp;
        
        return Db::getInstance()->executeS($sql);
        
    }

    private function calculaValor($parm) {

        // Adiciona Tempo de Preparacao
        $prazoEntrega = $parm['prazoEntrega'];

        if (is_numeric($prazoEntrega)) {
            $prazoEntrega += (int)$parm['tempoPreparacao'];
        }

        // Retorna se Frete Gratis por Valor for verdadeiro e a transportadora for a definida para o frete gratis
        if ($parm['freteGratisValor'] and $parm['transpFreteGratisValor'] == $parm['idCarrierAtual']) {
            return array('status' => true, 'valorFrete' => 'Grátis', 'prazoEntrega' => $prazoEntrega);
        }

        // Processa os produtos
        $totalCubagem = 0;
        $valorPedidoBaseFrete = $parm['valorPedido'];
        $pesoPedidoBaseFrete = 0;
        $valorAdicionalEnvio = 0;

        foreach ($parm['produtos'] as $prod) {

            // Nao acumula se o produto e Frete Gratis
            if ($prod['freteGratisProduto']) {

                // Retira o valor do produto que sera considerado nas Regras por Valor do Pedido
                $valorPedidoBaseFrete -= $prod['valorProduto'];
                continue;
            }

            // Acumula cubagem
            $totalCubagem += $prod['cubagem'];

            // Acumula peso dos produtos
            $pesoPedidoBaseFrete += $prod['peso'];

            // Acumula adicional de envio (definido no cadastro de produtos)
            $valorAdicionalEnvio += $prod['adicionalEnvio'];
        }

        // Retorna se existem somente Produtos com Frete Gratis
        if ($pesoPedidoBaseFrete == 0 and $totalCubagem == 0) {
            return array('status' => true, 'valorFrete' => 'Grátis', 'prazoEntrega' => $prazoEntrega);
        }

        // Verifica se deve considerar o peso real ou peso cubico
        $pesoCubico = ($totalCubagem / 1000000) * $parm['fatorCubagem'];

        if ($pesoCubico > $pesoPedidoBaseFrete) {
            $pesoPedidoBaseFrete = $pesoCubico;
        }

        // Pesquisa a tabela de precos
        $valorTabela = 0;
        $pesoTabela = 0;
        $ultimoPesoTabela = 0;
        $ultimoValorTabela = 0;

        // Cria array da tabela de precos
        $tabelaPreco = explode('/', $parm['tabelaPreco']);

        foreach ($tabelaPreco as $tabela) {

            if ($tabela == '') {
                continue;
            }

            $pos = strpos($tabela, ':');

            // Ignora a transportadora pois a tabela está configurada errada
            if ($pos === false) {
                return false;
            }

            $pesoTabela = substr($tabela, 0, $pos);

            // Guarda os ultimos valores de peso e valor
            $ultimoPesoTabela = $pesoTabela;
            $ultimoValorTabela = substr($tabela, $pos + 1);

            // Verifica se é o valor a ser adotado
            if ($pesoPedidoBaseFrete <= $pesoTabela) {
                $valorTabela = substr($tabela, $pos + 1);
                break;
            }
        }

        // Se nao localizado valor na tabela, recupera o valor com base nos ultimo valores da tabela e verifica se existe existe adicional por peso excedido
        $valorAdicionalPesoExcedente = 0;

        if ($valorTabela == 0) {
            if ($pesoPedidoBaseFrete > $ultimoPesoTabela) {

                // Recupera o valores com base no ultimo valor/peso da tabela
                $valorTabela = $ultimoValorTabela;
                $pesoTabela = $ultimoPesoTabela;

                // Calcula o valor excedente
                $valorAdicionalPesoExcedente = ($pesoPedidoBaseFrete - $ultimoPesoTabela) * $parm['valorAdicionalKilo'];
            }
        }

        // Calcula o valor do frete
        $totalFrete = $valorTabela;

        // Calcula o valor do frete considerando peso x valor kilo por intervalo de peso
        if ($parm['tipoTabela'] == _TABELA_TIPO_VALOR_KILO_) {
            $totalFrete = $valorTabela * $pesoTabela;
        }

        // Inclui o adicional por excedente de peso
        $totalFrete += $valorAdicionalPesoExcedente;

        // Inclui valor adicional fixo
        $totalFrete += $parm['valorAdicionalFixo'];

        // Processa regras de precos
        $sql = "SELECT * 
                FROM "._DB_PREFIX_."fkcorreiosg2cp1_regras_precos
                WHERE ativo = 1 AND 
                      id_shop = ".(int)$this->context->shop->id." AND 
                      id_transp = ".(int)$parm['idTransp'];
        
        $regras = Db::getInstance()->executeS($sql);
        
        // Processa regras com base diferente de frete
        foreach ($regras as $regra) {
            
            if ($regra['tipo_regra'] == _TIPO_VALOR_FRETE_ or $regra['tipo_regra'] == _TIPO_PERCENTUAL_FRETE_) {
                continue;
            }
            
            $totalFrete += $this->calculaRegra($regra, $valorPedidoBaseFrete, $parm['pesoPedido'], 0);
            
        }
        
        // Processa regra com base no frete
        $valorBaseFrete = 0;
        
        foreach ($regras as $regra) {
        
            if ($regra['tipo_regra'] != _TIPO_VALOR_FRETE_ and $regra['tipo_regra'] != _TIPO_PERCENTUAL_FRETE_) {
                continue;
            }
        
            $valorBaseFrete += $this->calculaRegra($regra, 0, 0, $totalFrete);
        
        }
        
        // Soma as regras com base no valor de frete
        $totalFrete += $valorBaseFrete;


        // Inclui o adicional de envio (cadastro de produtos)
        $totalFrete += $valorAdicionalEnvio;

        // Verifica se o Custo de Envio deve ser adicionado ao valor do frete da transportadora
        if (Configuration::get('PS_SHIPPING_HANDLING') > 0) {
            $carrier = new Carrier($parm['idCarrierAtual']);

            if ($carrier->shipping_handling) {
                $totalFrete += (float)Configuration::get('PS_SHIPPING_HANDLING');
            }
        }

        // Valor mínimo do frete
        if ($totalFrete < $parm['freteMinimo']) {
            $totalFrete = $parm['freteMinimo'];
        }

        // Desconto no frete
        if ($parm['percentualDescontoFrete'] > 0 and $parm['valorPedido'] >= $parm['valorPedidoDescontoFrete']) {
            $totalFrete *= (1 - ($parm['percentualDescontoFrete'] / 100));
        }

        if ($totalFrete == 0) {
            return array('status' => true, 'valorFrete' => 'Grátis', 'prazoEntrega' => $prazoEntrega);
        }

        return array('status' => true, 'valorFrete' => $totalFrete, 'prazoEntrega' => $prazoEntrega);
    }
    
    private function calculaRegra($regra, $valorPedido, $pesoPedido, $valorFrete) {
        
        $valorRegra = 0;
        
        if ($regra['tipo_regra'] == _TIPO_VALOR_FIXO_) {
            $valorRegra = $regra['tipo_regra_valor'];
        }else {
            // Recupera o valor base para o calculo considerando o tipo
            if ($regra['tipo_regra'] == _TIPO_VALOR_PEDIDO_ or $regra['tipo_regra'] == _TIPO_PERCENTUAL_PEDIDO_) {
                $valorBase = $valorPedido;
            }else {
                if ($regra['tipo_regra'] == _TIPO_VALOR_FRETE_ or $regra['tipo_regra'] == _TIPO_PERCENTUAL_FRETE_) {
                    $valorBase = $valorFrete;
                }else {
                    $valorBase = $pesoPedido;
                }
            }
            
            // Calcula com base em valor acima de
            if ($regra['formula_regra'] == _FORMULA_POR_VALOR_ACIMA_) {
                
                if ($valorBase > $regra['formula_regra_valor']) {
                    // Calcula considerando valor
                    if ($regra['tipo_regra'] == _TIPO_VALOR_PEDIDO_ or $regra['tipo_regra'] == _TIPO_VALOR_FRETE_ or $regra['tipo_regra'] == _TIPO_VALOR_PESO_) {
                        $valorRegra = $regra['tipo_regra_valor'];
                    }else {
                        // Calcula considerando percentual
                        $valorRegra = $valorBase * $regra['tipo_regra_valor'] / 100;
                    }
                }
            }else {
                // Calcula com base em intervalo
                $intervalos = (int)($valorBase/$regra['formula_regra_valor']);
                $resto = $valorBase % $regra['formula_regra_valor'];
                
                if ($resto > 0) {
                    $intervalos++;
                }
                
                // Calcula considerando valor
                if ($regra['tipo_regra'] == _TIPO_VALOR_PEDIDO_ or $regra['tipo_regra'] == _TIPO_VALOR_FRETE_ or $regra['tipo_regra'] == _TIPO_VALOR_PESO_) {
                    $valorRegra = $regra['tipo_regra_valor'] * $intervalos;
                }else {
                    // Calcula considerando percentual
                    $valorRegra = ($valorBase * $regra['tipo_regra_valor'] / 100) * $intervalos;
                }
                
            }
            
            // Verifica o valor minimo da regra
            if ($valorRegra < $regra['formula_regra_valor_minimo']) {
                $valorRegra = $regra['formula_regra_valor_minimo'];
            }
            
        }
        
        return $valorRegra;
        
    }
    
}