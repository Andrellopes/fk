<?php

require_once('FKcarrierCorreios.php');
require_once('FKcarrierFuncoes.php');

class fkcarrier extends CarrierModule {

    // Esta variavel contem o id do Carrier em execucao
	public $id_carrier;

 	private $_path_logo;
    private $_url_funcoes;
    private $_prazo_entrega = array();

	private $_html = '';
	private $_postErrors = array();
	
	public function __construct() {

		$this->name = 'fkcarrier';
		$this->tab = 'shipping_logistics';
		$this->version = '1.0.0';
		$this->author = 'módulosFK';
	
		parent::__construct();
	
		$this->displayName = $this->l('Módulo FKcarrier');
		$this->description = $this->l('Oferece aos seus clientes várias formas de entrega dos produtos.');
	
		// Path da pasta com logos dos carrier
		$this->_path_logo = _PS_MODULE_DIR_.$this->name.'/upload/';

        // Path de funcoes.php para ser passado ao js
        $this->_url_funcoes = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'. $this -> name.'/FKcarrierFuncoes.php';
	}
	
	public function install() {

		if (version_compare(_PS_VERSION_, '1.6.0.14', '>')) {
			$this->_errors[] = Tools::displayError('Este módulo não é compatível com esta versão do Prestashop');
			return false;
		}

		if (!parent::install()
			Or !$this->criaTabelas()
            Or !$this->instalaRegioes()
			Or !$this->registerHook('displayBackOfficeHeader')
			Or !$this->registerHook('actionCarrierUpdate')
            Or !$this->registerHook('displayBeforeCarrier')
            Or !$this->registerHook('displayFooterProduct')
            Or !$this->registerHook('displayShoppingCartFooter')
            Or !$this->registerHook('displayHeader')
            Or !$this->registerHook('displayRightColumnProduct')
			Or !$this->registerHook('displayLeftColumn')
			Or !$this->registerHook('displayRightColumn')
			Or !$this->registerHook('displayFooter')
			Or !$this->registerHook('displayCustomerAccount')
			Or !Configuration::updateValue('FKCARRIER_MEU_CEP', '')
            Or !Configuration::updateValue('FKCARRIER_CEP_CIDADE', '')
            Or !Configuration::updateValue('FKCARRIER_MAO_PROPRIA', '')
            Or !Configuration::updateValue('FKCARRIER_VALOR_DECLARADO', '')
            Or !Configuration::updateValue('FKCARRIER_AVISO_RECEBIMENTO', '')
            Or !Configuration::updateValue('FKCARRIER_CALCULO_SERV_ADIC', '1')
			Or !Configuration::updateValue('FKCARRIER_BLOCO_PRODUTO', 'on')
            Or !Configuration::updateValue('FKCARRIER_BLOCO_POSICAO', '0')
            Or !Configuration::updateValue('FKCARRIER_BLOCO_PRODUTO_LB', '')
			Or !Configuration::updateValue('FKCARRIER_BLOCO_CARRINHO', 'on')
			Or !Configuration::updateValue('FKCARRIER_TEMPO_PREPARACAO', '0')
			Or !Configuration::updateValue('FKCARRIER_EMBALAGEM', '2')
			Or !Configuration::updateValue('FKCARRIER_OFFLINE', '')
            Or !Configuration::updateValue('FKCARRIER_FRETE_GRATIS_TRANSP', '')
            Or !Configuration::updateValue('FKCARRIER_CALCULO_LOGADO', '')
			Or !Configuration::updateValue('FKCARRIER_RASTREIO_LEFT', '')
			Or !Configuration::updateValue('FKCARRIER_RASTREIO_RIGHT', '')
			Or !Configuration::updateValue('FKCARRIER_RASTREIO_FOOTER', '')
			Or !Configuration::updateValue('FKCARRIER_RASTREIO_ACCOUNT', '')) {
			
			return false;
		}
		
		return true;
		
	}
	
	public function uninstall() {
		
		if (!parent::uninstall()
			Or !$this->excluiCarrier()
			Or !$this->excluiTabelas()
			Or !$this->unregisterHook('displayBackOfficeHeader')
			Or !$this->unregisterHook('actionCarrierUpdate')
            Or !$this->unregisterHook('displayBeforeCarrier')
            Or !$this->unregisterHook('displayFooterProduct')
            Or !$this->unregisterHook('displayShoppingCartFooter')
            Or !$this->unregisterHook('displayHeader')
            Or !$this->unregisterHook('displayRightColumnProduct')
			Or !$this->unregisterHook('displayLeftColumn')
			Or !$this->unregisterHook('displayRightColumn')
			Or !$this->unregisterHook('displayFooter')
			Or !$this->unregisterHook('displayCustomerAccount')) {
			
			return false;
		}

        // Exclui dados de Configuração
        if (!Db::getInstance()->delete("configuration", "name LIKE 'FKCARRIER_%'")) {
            return false;
        }
		
		return true;
		
	}
	
	public function getContent() {

		$this->_html = '';
		$this->_html .= '<h2>'.$this->l('Módulo FKcarrier').'</h2>';
	
		if (!empty($_POST)) {
			
			$this->postValidation();
			
			if (!sizeof($this ->_postErrors)) {
				$this->_html .= $this->displayConfirmation($this->l('Configuração atualizada'));
			}else {
				foreach ($this->_postErrors AS $erro) {
					$this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$erro.'</div>';
				}
			
				$this->_html .= $this->displayError($this->l('Configuração falhou'));
			}
		}
	
		return $this->displayForm();
	}
	
	private function postValidation() {
		
		$sessao = Tools::getValue('section');
		
		switch($sessao) {
			
			case 'configGeral':

                // Exclusao do cache
                if (Tools::isSubmit('submitCache')) {
                    $this->postProcess($sessao);
                    break;
                }

				if (Tools::isSubmit('submitSave')) {
					$this->validaGeral($sessao);
				}
				
				break;
				
			case 'configCadastroCep':
			
				if (Tools::isSubmit('submitSave')) {
					$this->validaCadastroCep($sessao);
				}
				
				break;
				
			case 'configPrazoEntrega':
				
				if (Tools::isSubmit('submitSave')) {
					$this->validaPrazoEntrega($sessao);
				}
				
				break;
				
			case 'configEmbalagens':
				
				// Inclui/Exclui embalagem
				if (Tools::isSubmit('submitAdd') Or Tools::isSubmit('submitDel')) {
					$this->postProcess($sessao);
					break;
				}
				
				// Verifica configuracoes das embalagens
				if (Tools::isSubmit('submitSave')) {
					$this->validaEmbalagens($sessao);
				}
					
				break;
				
			case 'configEspecifCorreios':				
				
				if (Tools::isSubmit('submitSave')) {
					$this->validaEspecifCorreios($sessao);
				}
				
				break;
				
				
			case 'configServicosCorreios':
				
				// Inclui/Exclui servico
				if (Tools::isSubmit('submitAdd') Or Tools::isSubmit('submitDel')) {
					$this->postProcess($sessao);
					break;
				}
				
				// Verifica configuracoes dos servicos dos Correios
				if (Tools::isSubmit('submitSave')) {
					$this->validaServicosCorreios($sessao);
				}
					
				break;
				
			case 'configTransp':
				
				if (Tools::isSubmit('submitAdd') Or Tools::isSubmit('submitAddRegiao') Or 
					Tools::isSubmit('submitDel') Or Tools::isSubmit('submitDelRegioes')) {
					$this->postProcess($sessao);
					break;
				}
				
				// Verifica configuracoes das transportadoras
				if (Tools::isSubmit('submitSave')) {
					$this->validaTransp($sessao);
				}
				
				break;

            case 'configFreteGratis':

                if (Tools::isSubmit('submitAdd') Or Tools::isSubmit('submitDel')) {
                    $this->postProcess($sessao);
                }

                // Verifica configuracoes do frete gratis
                if (Tools::isSubmit('submitSave')) {
                    $this->validaFreteGratis($sessao);
                }

                break;

            case 'configTabelasOff':

                if (Tools::isSubmit('submitDel')) {
                    $this->postProcess($sessao);
                }

                // Verifica configuracoes das tabelas offline
                if (Tools::isSubmit('submitSave')) {
                    $this->validaTabelasOff($sessao);
                }

                break;
		}
	}

	private function validaGeral($sessao) {
	
		if (Tools::getValue('fkcarrier_meu_cep') == NULL) {
			$this->_postErrors[] = $this->l('Meu CEP não preenchido');
		}

        if (Tools::getValue('fkcarrier_cep_cidade') == NULL) {
            $this->_postErrors[] = $this->l('CEP da minha cidade não preenchido');
        }
	
		if (Tools::getValue('fkcarrier_tempo_preparacao') == NULL) {
			$this->_postErrors[] = $this->l('Tempo de preparação em dias não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_tempo_preparacao'));
	
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Tempo de preparação em dias" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Tempo de preparação em dias" não pode ser menor que 0 (zero)');
                }
            }
		}

		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}
	
	private function validaCadastroCep($sessao) {
	
		$estados_capitais = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fkcarrier_cadastro_cep');
	
		foreach ($estados_capitais as $reg) {
				
			$intervalos = explode("/", Tools::getValue('fkcarrier_cep_estado_'.$reg['id']));
				
			foreach ($intervalos as $intervalo) {
                if ($intervalo == ''){
                    continue;
                }

				if (strlen($intervalo) < 17) {
					$this->_postErrors[] = $this->l('"Intervalo de CEP dos Estados" com erro. Estado').': '.$reg['estado'];
				}
			}
				
			$intervalos = explode("/", Tools::getValue('fkcarrier_cep_capital_'.$reg['id']));
	
			foreach ($intervalos as $intervalo) {
                if ($intervalo == ''){
                    continue;
                }

				if (strlen($intervalo) < 17) {
					$this->_postErrors[] = $this->l('"Intervalo de CEP das Capitais" com erro. Estado').': '.$reg['estado'];
				}
			}

            if (Tools::getValue('fkcarrier_cep_base_capital_'.$reg['id']) == NULL) {
                $this->_postErrors[] = $this->l('CEP base - Capital não preenchido. Estado').': '.$reg['estado'];
            }else {
                $valor = str_replace('-', '', Tools::getValue('fkcarrier_cep_base_capital_'.$reg['id']));

                if (!is_numeric($valor)) {
                    $this->_postErrors[] = $this->l('O campo "CEP base - Capital é inválido. Estado').': '.$reg['estado'];
                }
            }

            if (Tools::getValue('fkcarrier_cep_base_interior_'.$reg['id']) == NULL) {
                $this->_postErrors[] = $this->l('CEP base - Interior não preenchido. Estado').': '.$reg['estado'];
            }else {
                $valor = str_replace('-', '', Tools::getValue('fkcarrier_cep_base_interior_'.$reg['id']));

                if (!is_numeric($valor)) {
                    $this->_postErrors[] = $this->l('O campo "CEP base - Interior é inválido. Estado').': '.$reg['estado'];
                }
            }

        }
	
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}
	
	private function validaPrazoEntrega($sessao) {
	
		$prazos_entrega = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fkcarrier_prazos_entrega');
		foreach ($prazos_entrega as $reg) {
	
			$correios_capital = Trim(Tools::getValue('fkcarrier_correios_capital_'.$reg['id']));
			$correios_interior = Trim(Tools::getValue('fkcarrier_correios_interior_'.$reg['id']));
			$transp_capital = Trim(Tools::getValue('fkcarrier_transp_capital_'.$reg['id']));
			$transp_interior = Trim(Tools::getValue('fkcarrier_transp_interior_'.$reg['id']));

            if ($correios_capital == NULL) {
                $this->_postErrors[] = $this->l('Correios - Capital não preenchido / Estado: '.$reg['estado']);
            }else {
                if (!is_numeric($correios_capital)) {
                    $this->_postErrors[] = $this->l('O campo "Correios - Capital" não é numérico / Estado: '.$reg['estado']);
                }else {
                    if ($correios_capital < 0) {
                        $this->_postErrors[] = $this->l('O campo "Correios - Capital" não pode ser menor que 0 (zero) / Estado: '.$reg['estado']);
                    }
                }
            }

            if ($correios_interior == NULL) {
                $this->_postErrors[] = $this->l('Correios - Interior não preenchido / Estado: '.$reg['estado']);
            }else {
                if (!is_numeric($correios_interior)) {
                    $this->_postErrors[] = $this->l('O campo "Correios - Interior" não é numérico / Estado: '.$reg['estado']);
                }else {
                    if ($correios_interior < 0) {
                        $this->_postErrors[] = $this->l('O campo "Correios - Interior" não pode ser menor que 0 (zero) / Estado: '.$reg['estado']);
                    }
                }
            }

            if ($transp_capital == NULL) {
                $this->_postErrors[] = $this->l('Transp - Capital não preenchido / Estado: '.$reg['estado']);
            }else {
                if (!is_numeric($transp_capital)) {
                    $this->_postErrors[] = $this->l('O campo "Transp - Capital" não é numérico / Estado: '.$reg['estado']);
                }else {
                    if ($transp_capital < 0) {
                        $this->_postErrors[] = $this->l('O campo "Transp - Capital" não pode ser menor que 0 (zero) / Estado: '.$reg['estado']);
                    }
                }
            }

            if ($transp_interior == NULL) {
                $this->_postErrors[] = $this->l('Transp - Interior não preenchido / Estado: '.$reg['estado']);
            }else {
                if (!is_numeric($transp_interior)) {
                    $this->_postErrors[] = $this->l('O campo "Transp - Interior" não é numérico / Estado: '.$reg['estado']);
                }else {
                    if ($transp_interior < 0) {
                        $this->_postErrors[] = $this->l('O campo "Transp - Interior" não pode ser menor que 0 (zero) / Estado: '.$reg['estado']);
                    }
                }
            }

		}
	
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}
	
	private function validaEmbalagens($sessao) {
	
		$embalagens = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_embalagens` Where `id_shop` = '.$this->context->shop->id);
	
		foreach ($embalagens as $reg) {
				
			if (Tools::getValue('fkcarrier_descricao_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Descrição não preenchida');
			}
				
			if (Tools::getValue('fkcarrier_comprimento_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Comprimento não preenchido');
			}else {
				$valor = str_replace(',', '.', Tools::getValue('fkcarrier_comprimento_'.$reg['id']));
	
				if (!is_numeric($valor)) {
					$this->_postErrors[] = $this->l('O campo "Comprimento" não é numérico');
				}else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Comprimento" não pode ser menor que 0 (zero)');
                    }
                }
			}
				
			if (Tools::getValue('fkcarrier_altura_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Altura não preenchida');
			}else {
				$valor = str_replace(',', '.', Tools::getValue('fkcarrier_altura_'.$reg['id']));
	
				if (!is_numeric($valor)) {
					$this->_postErrors[] = $this->l('O campo "Altura" não é numérico');
				}else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Altura" não pode ser menor que 0 (zero)');
                    }
                }
			}
				
			if (Tools::getValue('fkcarrier_largura_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Largura não preenchida');
			}else {
				$valor = str_replace(',', '.', Tools::getValue('fkcarrier_largura_'.$reg['id']));
	
				if (!is_numeric($valor)) {
					$this->_postErrors[] = $this->l('O campo "Largura" não é numérico');
				}else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Largura" não pode ser menor que 0 (zero)');
                    }
                }
			}
				
			if (Tools::getValue('fkcarrier_peso_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Peso não preenchido');
			}else {
				$valor = str_replace(',', '.', Tools::getValue('fkcarrier_peso_'.$reg['id']));
	
				if (!is_numeric($valor)) {
					$this->_postErrors[] = $this->l('O campo "Peso" não é numérico');
				}else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Peso" não pode ser menor que 0 (zero)');
                    }
                }
			}
				
			if (Tools::getValue('fkcarrier_custo_'.$reg['id']) == NULL) {
				$this->_postErrors[] = $this->l('Custo não preenchido');
			}else {
				$valor = str_replace(',', '.', Tools::getValue('fkcarrier_custo_'.$reg['id']));
	
				if (!is_numeric($valor)) {
					$this->_postErrors[] = $this->l('O campo "Custo" não é numérico');
				}else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Custo" não pode ser menor que 0 (zero)');
                    }
                }
			}
		}
	
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}
	
	private function validaEspecifCorreios($sessao) {
		
		// Recupera id das especificacoes dos Correios
		$id_esp_correios = Tools::getValue('id_esp_correios');
	
	
		if (Tools::getValue('fkcarrier_cod_servico_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Código serviço não preenchido');
		}
			
		if (Tools::getValue('fkcarrier_comprimento_min_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Comprimento mínimo não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_comprimento_min_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Comprimento mínimo" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Comprimento mínimo" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_comprimento_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Comprimento máximo não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_comprimento_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Comprimento máximo" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Comprimento máximo" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_largura_min_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Largura mínima não preenchida');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_largura_min_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Largura mínima" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Largura mínima" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_largura_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Largura máxima não preenchida');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_largura_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Largura máxima" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Largura máxima" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_altura_min_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Altura mínima não preenchida');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_altura_min_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Altura mínima" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Altura mínima" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_altura_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Altura máxima não preenchida');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_altura_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Altura máxima" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Altura máxima" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_somatoria_dimensoes_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Somatória dimensões não preenchida');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_somatoria_dimensoes_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Somatória dimensões" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Somatória dimensões" não pode ser menor que 0 (zero)');
                }
            }
		}
        
        if (Tools::getValue('fkcarrier_volume_max_'.$id_esp_correios) == NULL) {
            $this->_postErrors[] = $this->l('Volume máximo não preenchido');
        }else {
            $valor = str_replace(',', '.', Tools::getValue('fkcarrier_volume_max_'.$id_esp_correios));
                
            if (!is_numeric($valor)) {
                $this->_postErrors[] = $this->l('O campo "Volume máximo" não é numérico');
            }else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Volume máximo" não pode ser menor que 0 (zero)');
                }
            }
        }
			
		if (Tools::getValue('fkcarrier_peso_estadual_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Peso máximo - Estadual não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_peso_estadual_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Peso máximo - Estadual" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Peso máximo - Estadual" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_peso_nacional_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Peso máximo - Nacional não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_peso_nacional_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Peso máximo - Nacional" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Peso máximo - Nacional" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_intervalo_pesos_estadual_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Intervalo de pesos - Estadual não preenchido');
		}
			
		if (Tools::getValue('fkcarrier_intervalo_pesos_nacional_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Intervalo de pesos - Nacional não preenchido');
		}
			
		if (Tools::getValue('fkcarrier_cubagem_max_isenta_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Cubagem max isenta não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_cubagem_max_isenta_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Cubagem max isenta" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Cubagem max isenta" não pode ser menor que 0 (zero)');
                }
            }
		}

        if (Tools::getValue('fkcarrier_cubagem_base_calculo_'.$id_esp_correios) == NULL) {
            $this->_postErrors[] = $this->l('Cubagem base cálculo não preenchido');
        }else {
            $valor = str_replace(',', '.', Tools::getValue('fkcarrier_cubagem_base_calculo_'.$id_esp_correios));

            if (!is_numeric($valor)) {
                $this->_postErrors[] = $this->l('O campo "Cubagem base cálculo" não é numérico');
            }else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Cubagem base cálculo" não pode ser menor que 0 (zero)');
                }
            }
        }
			
		if (Tools::getValue('fkcarrier_mao_propria_valor_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Valor Mão Própria não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_mao_propria_valor_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Valor mão própria" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Valor mão própria" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_aviso_recebimento_valor_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Valor Aviso Recebimento não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_aviso_recebimento_valor_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Valor Aviso Recebimento" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Valor Aviso Recebimento" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_valor_declarado_percentual_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Percentual Valor Declarado não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_valor_declarado_percentual_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Percentual Valor Declarado" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Percentual Valor Declarado" não pode ser menor que 0 (zero)');
                }
            }
		}
			
		if (Tools::getValue('fkcarrier_valor_declarado_max_'.$id_esp_correios) == NULL) {
			$this->_postErrors[] = $this->l('Máximo Valor Declarado não preenchido');
		}else {
			$valor = str_replace(',', '.', Tools::getValue('fkcarrier_valor_declarado_max_'.$id_esp_correios));
				
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Máximo Valor Declarado" não é numérico');
			}else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Máximo Valor Declarado" não pode ser menor que 0 (zero)');
                }
            }
		}

        if (Tools::getValue('fkcarrier_seguro_automatico_valor_'.$id_esp_correios) == NULL) {
            $this->_postErrors[] = $this->l('Seguro automático não preenchido');
        }else {
            $valor = str_replace(',', '.', Tools::getValue('fkcarrier_seguro_automatico_valor_'.$id_esp_correios));

            if (!is_numeric($valor)) {
                $this->_postErrors[] = $this->l('O campo "Seguro automático" não é numérico');
            }else {
                if ($valor < 0) {
                    $this->_postErrors[] = $this->l('O campo "Seguro automático" não pode ser menor que 0 (zero)');
                }
            }
        }
	
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}
	
	private function validaServicosCorreios($sessao) {
	
		// Recupera id do servico
		$id_correios_transp = Tools::getValue('id_correios_transp');
	
		// Verifica se o servico esta ativo
		if (Tools::getValue('fkcarrier_correios_ativo_'.$id_correios_transp)) {
		
			// Verifica o campo "Grade"
			$valor = Tools::getValue('fkcarrier_correios_grade_'.$id_correios_transp);
		
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Grade" não é numérico');
			}else {
				if ($valor < 0 or $valor > 9) {
					$this->_postErrors[] = $this->l('O valor do campo "Grade" deve estar entre 0 e 9');
				}
			}
		
			// Verifica os campo "Estados atendidos" e "Intervalo de CEPs atendidos"
			$regioes_precos = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$id_correios_transp);
			
			foreach ($regioes_precos as $reg_regioes_precos) {
		
				if (!Tools::getValue('fkcarrier_correios_uf_'.$reg_regioes_precos['id']) and !Tools::getValue('fkcarrier_correios_intervalos_cep_'.$reg_regioes_precos['id'])) {
					$this->_postErrors[] = $this->l('O campo "Estados atendidos" ou "Intervalo CEPs atendidos" devem ser preenchidos');
				}
			}
		
		}
		
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
		
	}
	
	private function validaTransp($sessao) {
	
		// Recupera id da transportadora
		$id_transp = Tools::getValue('id_correios_transp');
			
		// Verifica se a transportadora esta ativa
		if (Tools::getValue('fkcarrier_transp_ativo_'.$id_transp)) {
	
			// Verifica nome da transportadora
			if (!Tools::getValue('fkcarrier_transp_nome_'.$id_transp)) {
				$this->_postErrors[] = $this->l('Nome da transportadora não preenchido.');
			}
	
			// Verifica o campo "Grade"
			$valor = Tools::getValue('fkcarrier_transp_grade_'.$id_transp);
	
			if (!is_numeric($valor)) {
				$this->_postErrors[] = $this->l('O campo "Grade" não é numérico');
			}else {
				if ($valor < 0 or $valor > 9) {
					$this->_postErrors[] = $this->l('O valor do campo "Grade" deve estar entre 0 e 9');
				}
			}
	
			// Recupera dados das regioes da transportadora
			$regioes_precos = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$id_transp);
			foreach ($regioes_precos as $reg_regioes_precos) {
					
				// Verifica nome da regiao
				$nome_regiao = Tools::getValue('fkcarrier_transp_nome_regiao_'.$reg_regioes_precos['id']);
					
				if (!$nome_regiao) {
					$this->_postErrors[] = $this->l('O campo "Nome da região" não preenchido.');
				}
					
				// Verifica os campo "Estados atendidos" e "Intervalo de CEPs atendidos"
				if (!Tools::getValue('fkcarrier_transp_uf_'.$reg_regioes_precos['id']) and !Tools::getValue('fkcarrier_transp_intervalos_cep_'.$reg_regioes_precos['id'])) {
					$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Estados atendidos" ou "Intervalo CEPs atendidos" devem ser preenchidos');
				}
					
				// Verifica cobranca do frete
				$tipo_preco = Tools::getValue('fkcarrier_transp_tipo_preco_'.$reg_regioes_precos['id']);
	
				if ($tipo_preco == 1) {
					if (Tools::getValue('fkcarrier_transp_preco_1_'.$reg_regioes_precos['id']) == NULL) {
						$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor" do preço fixo não preenchido');
					}else {
						$valor = str_replace(',', '.', Tools::getValue('fkcarrier_transp_preco_1_'.$reg_regioes_precos['id']));
	
						if (!is_numeric($valor)) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor" do preço fixo não é numérico');
						}else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Valor" do preço fixo não pode ser menor que 0 (zero)');
                            }
                        }
					}
				}else {
					if ($tipo_preco == 2) {
						if (Tools::getValue('fkcarrier_transp_preco_2_'.$reg_regioes_precos['id']) == NULL) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Preço fixo por intervalo de peso" não preenchido');
						}
					}else {
						if (Tools::getValue('fkcarrier_transp_preco_3_'.$reg_regioes_precos['id']) == NULL) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Preço peso x valor kilo por intervalo de peso" não preenchido');
						}
					}
						
					if (Tools::getValue('fkcarrier_transp_valor_kilo_excedente_'.$reg_regioes_precos['id']) == NULL) {
						$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor kilo excedente" não preenchido');
					}else {
						$valor = str_replace(',', '.', Tools::getValue('fkcarrier_transp_valor_kilo_excedente_'.$reg_regioes_precos['id']));
							
						if (!is_numeric($valor)) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor kilo excedente" não é numérico');
						}else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Valor kilo excedente" não pode ser menor que 0 (zero)');
                            }
                        }
					}
						
					if (Tools::getValue('fkcarrier_transp_seguro_'.$reg_regioes_precos['id']) == NULL) {
						$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Percentual seguro" não preenchido');
					}else {
						$valor = str_replace(',', '.', Tools::getValue('fkcarrier_transp_seguro_'.$reg_regioes_precos['id']));
							
						if (!is_numeric($valor)) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Percentual seguro" não é numérico');
						}else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Percentual seguro" não pode ser menor que 0 (zero)');
                            }
                        }
					}
						
					if (Tools::getValue('fkcarrier_transp_pedagio_'.$reg_regioes_precos['id']) == NULL) {
						$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor pedágio" não preenchido');
					}else {
						$valor = str_replace(',', '.', Tools::getValue('fkcarrier_transp_pedagio_'.$reg_regioes_precos['id']));
							
						if (!is_numeric($valor)) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Valor pedágio" não é numérico');
						}else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Valor pedágio" não pode ser menor que 0 (zero)');
                            }
                        }
					}
						
					if (Tools::getValue('fkcarrier_transp_cubagem_'.$reg_regioes_precos['id']) == NULL) {
						$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Fator cubagem" não preenchido');
					}else {
						$valor = str_replace(',', '.', Tools::getValue('fkcarrier_transp_cubagem_'.$reg_regioes_precos['id']));
							
						if (!is_numeric($valor)) {
							$this->_postErrors[] = $nome_regiao.': '.$this->l('O campo "Fator cubagem" não é numérico');
						}else {
                            if ($valor < 0) {
                                $this->_postErrors[] = $this->l('O campo "Fator cubagem" não pode ser menor que 0 (zero)');
                            }
                        }
					}
				}
			}
		}
	
		if (!$this->_postErrors) {
			$this->postProcess($sessao);
		}
	}

    private function validaFreteGratis($sessao) {

        // Recupera id da regiao frete gratis
        $id_frete_gratis = Tools::getValue('id_frete_gratis');

        // Verifica se a regiao esta ativa
        if (Tools::getValue('fkcarrier_frete_gratis_ativo_'.$id_frete_gratis)) {

            // Verifica nome da regiao
            $nome_regiao = Tools::getValue('fkcarrier_frete_gratis_nome_regiao_'.$id_frete_gratis);

            if (!$nome_regiao) {
                $this->_postErrors[] = $this->l('O campo "Nome da região" não preenchido.');
            }

            // Verifica os campo "Estados atendidos" e "Intervalo de CEPs atendidos"
            if (!Tools::getValue('fkcarrier_frete_gratis_uf_'.$id_frete_gratis) and !Tools::getValue('fkcarrier_frete_gratis_intervalos_cep_'.$id_frete_gratis)) {
                $this->_postErrors[] = $this->l('O campo "Estados atendidos" ou "Intervalo CEPs atendidos" devem ser preenchidos');
            }

            // Verifica valor do pedido
            if (Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$id_frete_gratis) == NULL) {
                $this->_postErrors[] = $this->l('O campo "Valor pedido" não preenchido');
            }else {
                $valor = str_replace(',', '.', Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$id_frete_gratis));

                if (!is_numeric($valor)) {
                    $this->_postErrors[] = $this->l('O campo "Valor pedido" não é numérico');
                }else {
                    if ($valor < 0) {
                        $this->_postErrors[] = $this->l('O campo "Valor pedido" não pode ser menor que 0 (zero)');
                    }
                }
            }

            // Verifica valor do pedido e produtos com frete gratis
            if (Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$id_frete_gratis) == 0 and !Tools::getValue('fkcarrier_frete_gratis_relacao_produtos_'.$id_frete_gratis)) {
                $this->_postErrors[] = $this->l('O campo "Valor pedido" ou "Produtos" devem ser preenchidos');
            }

            // Verifica transportadora
            if (!Tools::getValue('fkcarrier_frete_gratis_transp_'.$id_frete_gratis)) {
                $this->_postErrors[] = $this->l('Transportadora não selecionada');
            }

        }

        if (!$this->_postErrors) {
            $this->postProcess($sessao);
        }
    }

    private function validaTabelasOff($sessao) {

        // Recupera id
        $id_correios_transp = Tools::getValue('id_correios_transp');

        // Verifica se existem tabelas com erro
        $tabelas_off = Db::getInstance()->executeS('SELECT `id`, `minha_cidade` FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` WHERE `id_correios_transp` = '.(int)$id_correios_transp);

        foreach ($tabelas_off as $reg_tabelas_off) {

            if ($reg_tabelas_off['minha_cidade'] == 1) {
                if (Tools::getValue('fkcarrier_tabelas_off_capital_'.$reg_tabelas_off['id']) == NULL) {
                    $this->_postErrors[] = $this->l('Existem tabelas offline inválidas ou não preenchidas. Favor verificar e regerar estas tabelas.');
                    break;
                }
            }else {
                if (Tools::getValue('fkcarrier_tabelas_off_capital_'.$reg_tabelas_off['id']) == NULL Or Tools::getValue('fkcarrier_tabelas_off_interior_'.$reg_tabelas_off['id']) == NULL) {
                    $this->_postErrors[] = $this->l('Existem tabelas offline inválidas ou não preenchidas. Favor verificar e regerar estas tabelas.');
                    break;
                }
            }

        }

        if (!$this->_postErrors) {
            $this->postProcess($sessao);
        }
    }
	
	private function postProcess($sessao) {
		
		switch($sessao) {
			
			case 'configGeral':

                if (Tools::isSubmit('submitCache')) {
                    $this->excluiCache();
                    break;
                }

                if (Tools::isSubmit('submitSave')) {
                    $this->salvaGeral();
                    break;
                }

                break;

			case 'configCadastroCep':
				
				$this->salvaCadastroCep();
				break;
				
			case 'configPrazoEntrega':

				$this->salvaPrazoEntrega();
				break;
				
			case 'configEmbalagens':
				
				if (Tools::isSubmit('submitAdd')) {
					$this->incluiEmbalagem();
					break;
				}
				
				if (Tools::isSubmit('submitDel')) {
					$this->excluiEmbalagem();
					break;
				}
				
				if (Tools::isSubmit('submitSave')) {
					$this->salvaEmbalagens ();
					break;
				}

                break;
				
			case 'configEspecifCorreios':
				
				$this->salvaEspecifCorreios ();
				break;
				
			case 'configServicosCorreios':
				
				if (Tools::isSubmit('submitAdd')) {
					$this->incluiServicoCorreios();
					break;
				}
				
				// Exclui servicos
				if (Tools::isSubmit('submitDel')) {
					$this->excluiServicosCorreios();
					break;
				}
				
				// Salva as configuracoes dos servicos dos Correios
				if (Tools::isSubmit('submitSave')) {
					$this->salvaServicosCorreios($sessao);
					break;
				}

                break;
				
			case 'configTransp':
				
				if (Tools::isSubmit('submitAdd')) {
					$this->incluiTransp();
					break;
				}
				
				if (Tools::isSubmit('submitAddRegiao')) {
					$this->incluiRegiaoTransp(Tools::getValue('id_correios_transp'));
					break;
				}
				
				if (Tools::isSubmit('submitDel')) {
					$this->excluiTransp ();
					break;
				}
				
				if (Tools::isSubmit('submitDelRegioes')) {
					$this->excluiRegioesTransp();
					break;
				}
				
				if (Tools::isSubmit('submitSave')) {
					$this->salvaTransp($sessao);
					break;
				}

                break;

            case 'configFreteGratis':

                if (Tools::isSubmit('submitAdd')) {
                    $this->incluiFreteGratis();
                    break;
                }

                if (Tools::isSubmit('submitDel')) {
                    $this->excluiFreteGratis();
                    break;
                }

                if (Tools::isSubmit('submitSave')) {
                    $this->salvaFreteGratis();
                    break;
                }

                break;

            case 'configTabelasOff':

                if (Tools::isSubmit('submitDel')) {
                    $this->excluiTabelasOff();
                    break;
                }

                if (Tools::isSubmit('submitSave')) {
                    $this->salvaTabelasOff($sessao);
                    break;
                }
		}
	}
	
    private function excluiCache() {
        Db::getInstance()->delete('fkcarrier_cache');
    }

	private function salvaGeral() {
		
		Configuration::updateValue('FKCARRIER_MEU_CEP', Tools::getValue('fkcarrier_meu_cep'));
        Configuration::updateValue('FKCARRIER_CEP_CIDADE', Tools::getValue('fkcarrier_cep_cidade'));
        Configuration::updateValue('FKCARRIER_MAO_PROPRIA', Tools::getValue('fkcarrier_mao_propria'));
        Configuration::updateValue('FKCARRIER_VALOR_DECLARADO', Tools::getValue('fkcarrier_valor_declarado'));
        Configuration::updateValue('FKCARRIER_AVISO_RECEBIMENTO', Tools::getValue('fkcarrier_aviso_recebimento'));
        Configuration::updateValue('FKCARRIER_CALCULO_SERV_ADIC', Tools::getValue('fkcarrier_calculo_serv_adic'));
		Configuration::updateValue('FKCARRIER_BLOCO_PRODUTO', Tools::getValue('fkcarrier_bloco_produto'));
		Configuration::updateValue('FKCARRIER_BLOCO_POSICAO', Tools::getValue('fkcarrier_bloco_posicao'));
        Configuration::updateValue('FKCARRIER_BLOCO_PRODUTO_LB', Tools::getValue('fkcarrier_bloco_produto_lb'));
        Configuration::updateValue('FKCARRIER_BLOCO_CARRINHO', Tools::getValue('fkcarrier_bloco_carrinho'));
		Configuration::updateValue('FKCARRIER_RASTREIO_LEFT', Tools::getValue('fkcarrier_bloco_rastreio_left'));
		Configuration::updateValue('FKCARRIER_RASTREIO_RIGHT', Tools::getValue('fkcarrier_bloco_rastreio_right'));
		Configuration::updateValue('FKCARRIER_RASTREIO_FOOTER', Tools::getValue('fkcarrier_bloco_rastreio_footer'));
		Configuration::updateValue('FKCARRIER_RASTREIO_ACCOUNT', Tools::getValue('fkcarrier_bloco_rastreio_account'));
		Configuration::updateValue('FKCARRIER_TEMPO_PREPARACAO', Tools::getValue('fkcarrier_tempo_preparacao'));
		Configuration::updateValue('FKCARRIER_EMBALAGEM', Tools::getValue('fkcarrier_embalagem'));
		Configuration::updateValue('FKCARRIER_OFFLINE', Tools::getValue('fkcarrier_offline'));
        Configuration::updateValue('FKCARRIER_FRETE_GRATIS_TRANSP', Tools::getValue('fkcarrier_frete_gratis_transp'));
        Configuration::updateValue('FKCARRIER_CALCULO_LOGADO', Tools::getValue('fkcarrier_calculo_logado'));
	}
	
	private function salvaCadastroCep() {
		
		$estados_capitais = db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep`');
	
		foreach ($estados_capitais as $reg) {

            $dados = array(
                'cep_estado'             => Tools::getValue('fkcarrier_cep_estado_'.$reg['id']),
                'cep_capital'           => Tools::getValue('fkcarrier_cep_capital_'.$reg['id']),
                'cep_base_capital'      => Tools::getValue('fkcarrier_cep_base_capital_'.$reg['id']),
                'cep_base_interior'     => Tools::getValue('fkcarrier_cep_base_interior_'.$reg['id'])
            );

			Db::getInstance()->update('fkcarrier_cadastro_cep', $dados,'`id` = '.(int)$reg['id']);
		}
	}
	
	private function salvaPrazoEntrega() {
		
		$prazos_entrega = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'fkcarrier_prazos_entrega WHERE `id_shop` = '.$this->context->shop->id);
		foreach ($prazos_entrega as $reg) {
	
			$correios_capital = Trim(Tools::getValue('fkcarrier_correios_capital_'.$reg['id']));
			$correios_interior = Trim(Tools::getValue('fkcarrier_correios_interior_'.$reg['id']));
			$transp_capital = Trim(Tools::getValue('fkcarrier_transp_capital_'.$reg['id']));
			$transp_interior = Trim(Tools::getValue('fkcarrier_transp_interior_'.$reg['id']));
	
			$dados = array(
					'correios_capital'      => $correios_capital,
					'correios_interior'     => $correios_interior,
					'transp_capital'        => $transp_capital,
					'transp_interior'       => $transp_interior
			);
	
			Db::getInstance()->update('fkcarrier_prazos_entrega', $dados, '`id` = '.(int)$reg['id']);
		}
	}
	
	private function incluiEmbalagem() {
		
		$dados = array(
				'id_shop'		=> $this->context->shop->id,
				'descricao' 	=> 'Nova Caixa',
				'comprimento' 	=> '0',
				'altura'    	=> '0',
				'largura'   	=> '0',
				'peso'      	=> '0',
				'cubagem'   	=> '0',
				'custo'     	=> '0',
				'ativo' 		=> '1'
		);
	
		Db::getInstance()->insert('fkcarrier_embalagens', $dados);
	}
	
	private function excluiEmbalagem() {
		
		// Array com as embalagens a ser excluidas
		$regioes_excluidas = Tools::getValue('fkcarrier_excluir');
	
		if ($regioes_excluidas) {
			foreach ($regioes_excluidas as $servicos) {
				Db::getInstance()->delete('fkcarrier_embalagens', '`id` = '.(int)$servicos);
			}
		}
	}
	
	private function salvaEmbalagens() {
		
		// Array com as embalagens ativas
		$embalagens_ativas = Tools::getValue('fkcarrier_ativo');
			
		// Atualiza os dados das embalagens
        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkcarrier_embalagens
                WHERE id_shop =".(int)$this->context->shop->id;

        $embalagens = Db::getInstance()->ExecuteS($sql);

		foreach ($embalagens as $reg) {
	
			$comprimento = str_replace(',', '.', Tools::getValue('fkcarrier_comprimento_'.$reg['id']));
			$altura = str_replace(',', '.', Tools::getValue('fkcarrier_altura_'.$reg['id']));
			$largura = str_replace(',', '.', Tools::getValue('fkcarrier_largura_'.$reg['id']));
			$peso = str_replace(',', '.', Tools::getValue('fkcarrier_peso_'.$reg['id']));
			$custo = str_replace(',', '.', Tools::getValue('fkcarrier_custo_'.$reg['id']));
				
			// Calcula cubagem da caixa
			$cubagem = ($comprimento * $altura * $largura);
				
			// Verifica se a embalagem esta ativa
			$ativo = 0;

            if ($embalagens_ativas) {
                if (in_array($reg['id'], $embalagens_ativas)) {
                    $ativo = 1;
                }
            }

			$dados = array(
					'descricao' 	=> Tools::getValue('fkcarrier_descricao_'.$reg['id']),
					'comprimento' 	=> $comprimento,
					'altura'   		=> $altura,
					'largura'   	=> $largura,
					'peso'      	=> $peso,
					'cubagem'   	=> $cubagem,
					'custo'     	=> $custo,
					'ativo'			=> $ativo
			);
	
			Db::getInstance()->update('fkcarrier_embalagens', $dados, '`id` = '.(int)$reg['id']);
		}
	}
	
	private function salvaEspecifCorreios() {
		
		// Recupera id das especificacoes dos Correios
		$id_esp_correios = Tools::getValue('id_esp_correios');
		
		$dados = array(
				'cod_servico' 					=> Tools::getValue('fkcarrier_cod_servico_'.$id_esp_correios),
				'cod_administrativo'			=> Tools::getValue('fkcarrier_cod_administrativo_'.$id_esp_correios),
				'senha'  						=> Tools::getValue('fkcarrier_senha_'.$id_esp_correios),
				'comprimento_min'				=> Tools::getValue('fkcarrier_comprimento_min_'.$id_esp_correios),
				'comprimento_max' 				=> Tools::getValue('fkcarrier_comprimento_max_'.$id_esp_correios),
				'largura_min' 					=> Tools::getValue('fkcarrier_largura_min_'.$id_esp_correios),
				'largura_max' 					=> Tools::getValue('fkcarrier_largura_max_'.$id_esp_correios),
				'altura_min' 					=> Tools::getValue('fkcarrier_altura_min_'.$id_esp_correios),
				'somatoria_dimensoes_max' 		=> Tools::getValue('fkcarrier_somatoria_dimensoes_max_'.$id_esp_correios),
                'volume_max'                    => Tools::getValue('fkcarrier_volume_max_'.$id_esp_correios),
				'peso_estadual_max' 			=> Tools::getValue('fkcarrier_peso_estadual_max_'.$id_esp_correios),
				'peso_nacional_max' 			=> Tools::getValue('fkcarrier_peso_nacional_max_'.$id_esp_correios),
				'intervalo_pesos_estadual' 		=> Tools::getValue('fkcarrier_intervalo_pesos_estadual_'.$id_esp_correios),
				'intervalo_pesos_nacional' 		=> Tools::getValue('fkcarrier_intervalo_pesos_nacional_'.$id_esp_correios),
				'cubagem_max_isenta' 			=> Tools::getValue('fkcarrier_cubagem_max_isenta_'.$id_esp_correios),
                'cubagem_base_calculo' 			=> Tools::getValue('fkcarrier_cubagem_base_calculo_'.$id_esp_correios),
				'mao_propria_valor' 			=> Tools::getValue('fkcarrier_mao_propria_valor_'.$id_esp_correios),
				'aviso_recebimento_valor' 		=> Tools::getValue('fkcarrier_aviso_recebimento_valor_'.$id_esp_correios),
				'valor_declarado_percentual' 	=> Tools::getValue('fkcarrier_valor_declarado_percentual_'.$id_esp_correios),
				'valor_declarado_max' 			=> Tools::getValue('fkcarrier_valor_declarado_max_'.$id_esp_correios),
                'seguro_automatico_valor'		=> Tools::getValue('fkcarrier_seguro_automatico_valor_'.$id_esp_correios)
		);
			
		Db::getInstance()->update('fkcarrier_especificacoes_correios', $dados,'`id` = '.(int)$id_esp_correios);

        // Exclui cache
        $this->excluiCache();

	}
	
	private function incluiServicoCorreios() {
		
		$servicos_correios = Tools::getValue('fkcarrier_servicos_correios');
	
		if ($servicos_correios) {
	
			foreach ($servicos_correios as $servico) {
	
				// Recupera o nome do servico selecionado
				$especif_correios = Db::getInstance()->getRow('SELECT `servico` FROM `'._DB_PREFIX_.'fkcarrier_especificacoes_correios` WHERE `id` = '.(int)$servico);
	
				// Insere registro em fkcarrier_correios_transp
				if ($especif_correios) {
					$dados = array(
							'id_shop' 		=> $this->context->shop->id,
							'id_carrier' 	=> 0,
							'id_correios' 	=> $servico,
							'nome_carrier' 	=> $especif_correios['servico'],
							'grade' 		=> 0,
							'ativo' 		=> 1
					);
	
					Db::getInstance()->insert('fkcarrier_correios_transp', $dados);
	
					// Recupera id do registro incluido
					$id_correios_transp = DB::getInstance()->Insert_ID();
	
					// Insere registro em fkcarrier_regioes_precos
					$dados = array(
							'id_correios_transp'	=> $id_correios_transp,
							'regiao_uf' 			=> '',
							'regiao_cep'			=> ''
					);
	
					Db::getInstance()->insert('fkcarrier_regioes_precos', $dados);
				}
			}
		}
	}
	
	private function excluiServicosCorreios() {
		
		// Recupera id do servico
		$id_correios_transp = Tools::getValue('id_correios_transp');
		
		$correios_transp = Db::getInstance()->getRow('SELECT `id_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id` = '.(int)$id_correios_transp);

        // Exclui registro das tabelas do FKcarrier
        Db::getInstance()->delete('fkcarrier_correios_transp', '`id` = '.(int)$id_correios_transp);
        Db::getInstance()->delete('fkcarrier_regioes_precos', '`id_correios_transp` = '.(int)$id_correios_transp);
        Db::getInstance()->delete('fkcarrier_tabelas_offline', '`id_correios_transp` = '.(int)$id_correios_transp);

        // Exclui logos
        if (file_exists(_PS_SHIP_IMG_DIR_.'/'.$correios_transp['id_carrier'].'.jpg')) {
            unlink(_PS_SHIP_IMG_DIR_.'/'.$correios_transp['id_carrier'].'.jpg');
        }

        if (file_exists($this->_path_logo.$correios_transp['id_carrier'].'.jpg')) {
            unlink($this->_path_logo.$correios_transp['id_carrier'].'.jpg');
        }

        // Marca como excluido o carrier do Prestashop
		$carrier = new Carrier($correios_transp['id_carrier']);

        if ($carrier->id) {
            $carrier->deleted = true;
            $carrier->update();
        }
	}
	
	private function salvaServicosCorreios($sessao) {
		
		// Recupera id do servico
		$id_correios_transp = Tools::getValue('id_correios_transp');
		
		// Altera fkcarrier_correios_transp
		$dados = array(
				'grade' => Tools::getValue('fkcarrier_correios_grade_'.$id_correios_transp),
				'ativo' => (!Tools::getValue('fkcarrier_correios_ativo_'.$id_correios_transp) ? '0' : '1')
		);
		
		Db::getInstance()->update('fkcarrier_correios_transp', $dados, 'id = '.(int)$id_correios_transp);
		
		// Altera fkcarrier_regioes_precos
		$regioes_precos = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$id_correios_transp);
		
		foreach ($regioes_precos as $reg_regioes_precos) {
		
			$correios_uf = Tools::getValue('fkcarrier_correios_uf_'.$reg_regioes_precos['id']);
		
			$regiao_uf = '';
		
			if ($correios_uf) {
				foreach ($correios_uf as $uf) {
					$regiao_uf .= $uf.'/';
				}
			}
			
			$dados = array(
					'regiao_uf' 	=> $regiao_uf,
					'regiao_cep' 	=> Tools::getValue('fkcarrier_correios_intervalos_cep_'.$reg_regioes_precos['id'])
			);
			
			Db::getInstance()->update('fkcarrier_regioes_precos', $dados, 'id = '.(int)$reg_regioes_precos['id']);
			
			
		}
		
		// Recupera dados do servico
		$correios_transp = Db::getInstance()->getRow('SELECT `id_carrier`, `nome_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id` = '.(int)$id_correios_transp);
		
		// Upload logo
		$this->uploadLogo($sessao, $id_correios_transp, $correios_transp['nome_carrier']);
		
		// Cria ou alterar carrier do Prestashop
		if ($correios_transp['id_carrier'] == 0) {
		
			$configCarrier = array(
					'name' 					=> $correios_transp['nome_carrier'],
					'id_tax_rules_group' 	=> 0,
					'active' 				=> (!Tools::getValue('fkcarrier_correios_ativo_'.$id_correios_transp) ? '0' : '1'),
					'deleted' 				=> false,
					'shipping_handling' 	=> false,
					'range_behavior' 		=> true,
					'is_module' 			=> true,
					'shipping_external' 	=> true,
					'shipping_method' 		=> 0,
					'external_module_name' 	=> $this->name,
					'need_range' 			=> true,
					'url' 					=> 'http://websro.correios.com.br/sro_bin/txect01%24.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=@',
					'is_free' 				=> false,
					'grade' 				=> Tools::getValue('fkcarrier_correios_grade_'.$id_correios_transp),
					'id_shop'				=> $this->context->shop->id,
					'id_correios_transp'	=> $id_correios_transp
			);
		
			// Atualiza o campo id_carrier
			$id_carrier = $this->instalaCarrier($configCarrier);
			Db::getInstance()->update('fkcarrier_correios_transp', array('id_carrier' => $id_carrier), '`id` = '.(int)$id_correios_transp);
		}else {
			$configCarrier = array(
					'id_carrier' 			=> $correios_transp['id_carrier'],
					'id_correios_transp'	=> $id_correios_transp,
					'name' 					=> $correios_transp['nome_carrier'],
					'active'				=> (!Tools::getValue('fkcarrier_correios_ativo_'.$id_correios_transp) ? '0' : '1'),
					'grade' 				=> Tools::getValue('fkcarrier_correios_grade_'.$id_correios_transp),
					'id_shop'				=> $this->context->shop->id
			);
		
			$this->alteraCarrier($configCarrier);
		}

	}
	
	private function incluiTransp() {
		
		$dados = array(
				'id_shop'		=> $this->context->shop->id,
				'id_carrier' 	=> '0',
				'id_correios' 	=> '0',
				'nome_carrier' 	=> 'Nova Transportadora',
				'grade'		   	=> '0',
				'ativo' 		=> '1'
		);
	
		Db::getInstance()->insert('fkcarrier_correios_transp', $dados);
			
		// Recupera id do registro incluido
		$id_transp = DB::getInstance()->Insert_ID();
	
		// Inclui regiao
		$this->incluiRegiaoTransp($id_transp);
	}
	
	private function incluiRegiaoTransp($id_transp) {
	
		// Insere registro em fkcarrier_regioes_precos
		$dados = array(
				'id_correios_transp'				=> $id_transp,
				'nome_regiao'						=> 'Nova Região',
				'prazo_entrega_especifico'			=> '',
				'regiao_uf' 						=> '',
				'regiao_cep'						=> '',
				'tipo_preco'						=> '1',
				'preco_1'							=> '0',
				'preco_2'							=> '',
				'preco_3'							=> '',
				'valor_adicional_excedente_kilo'	=> '0',
				'percentual_seguro'					=> '0',
				'valor_pedagio'						=> '0',
				'fator_cubagem'						=> '0'
		);
			
		Db::getInstance()->insert('fkcarrier_regioes_precos', $dados);
	}
	
	private function excluiTransp() {
		
		// Recupera id da transportadora a ser excluida
		$id_transp = Tools::getValue('id_correios_transp');
	
		$correios_transp = Db::getInstance()->getRow('SELECT `id_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id` = '.(int)$id_transp);
	
		// Excluir carrier do Prestashop
		$carrier = new Carrier($correios_transp['id_carrier']);
	
		if ($carrier->delete()) {
			// Exclui registro das tabelas do FKcarrier
			Db::getInstance()->delete('fkcarrier_correios_transp', '`id` = '.(int)$id_transp);
			Db::getInstance()->delete('fkcarrier_regioes_precos', '`id_correios_transp` = '.(int)$id_transp);
	
			// Exclui logos
			if (file_exists(_PS_SHIP_IMG_DIR_.'/'.$correios_transp['id_carrier'].'.jpg')) {
				unlink(_PS_SHIP_IMG_DIR_.'/'.$correios_transp['id_carrier'].'.jpg');
			}
	
			if (file_exists($this->_path_logo.$id_transp.'.jpg')) {
				unlink($this->_path_logo.$id_transp.'.jpg');
			}
		}
	}
	
	private function excluiRegioesTransp() {
		
		// Array com as regioes excluidas
		$regioes_excluidas = Tools::getValue('fkcarrier_transp_excluir_regioes');
	
		if ($regioes_excluidas) {
	
			foreach ($regioes_excluidas as $regiao) {
				Db::getInstance()->delete('fkcarrier_regioes_precos', '`id` = '.(int)$regiao);
			}
		}
	}
	
	private function salvaTransp($sessao) {
		
		// Recupera id da transportadora a ser alterada
		$id_transp = Tools::getValue('id_correios_transp');
		
		// Altera fkcarrier_correios_transp
		$dados = array(
				'nome_carrier' 	=> Tools::getValue('fkcarrier_transp_nome_'.$id_transp),
				'grade' 		=> Tools::getValue('fkcarrier_transp_grade_'.$id_transp),
				'ativo' 		=> (!Tools::getValue('fkcarrier_transp_ativo_'.$id_transp) ? '0' : '1')
		);
		
		Db::getInstance()->update('fkcarrier_correios_transp', $dados, 'id = '.(int)$id_transp);
			
		// Altera fkcarrier_regioes_precos
		$regioes_precos = Db::getInstance()->executeS('SELECT `id` FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$id_transp);
			
		foreach ($regioes_precos as $reg_regioes_precos) {
		
			$nome_regiao = Tools::getValue('fkcarrier_transp_nome_regiao_'.$reg_regioes_precos['id']);
			$prazo_entrega = Tools::getValue('fkcarrier_transp_prazo_ent_esp_'.$reg_regioes_precos['id']);
			$transp_uf = Tools::getValue('fkcarrier_transp_uf_'.$reg_regioes_precos['id']);
				
			$regiao_uf = '';
			
			if ($transp_uf) {
				foreach ($transp_uf as $uf) {
					$regiao_uf .= $uf.'/';
				}
			}
		
			$tipo_preco = Tools::getValue('fkcarrier_transp_tipo_preco_'.$reg_regioes_precos['id']);
		
			$preco_1 = '0';
			$preco_2 = '';
			$preco_3 = '';
			$valor_adicional = '0';
			$percentual_seguro = '0';
			$valor_pedagio = '0';
			$fator_cubagem = '0';
		
			if ($tipo_preco == 1) {
				$preco_1 = str_replace(',', '.', Tools::getValue('fkcarrier_transp_preco_1_'.$reg_regioes_precos['id']));
			}else {
				if ($tipo_preco == 2) {
					$preco_2 = str_replace(',', '.', Tools::getValue('fkcarrier_transp_preco_2_'.$reg_regioes_precos['id']));
				}else {
					$preco_3 = str_replace(',', '.', Tools::getValue('fkcarrier_transp_preco_3_'.$reg_regioes_precos['id']));
				}
		
				$valor_adicional = Tools::getValue('fkcarrier_transp_valor_kilo_excedente_'.$reg_regioes_precos['id']);
				$percentual_seguro = Tools::getValue('fkcarrier_transp_seguro_'.$reg_regioes_precos['id']);
				$valor_pedagio = Tools::getValue('fkcarrier_transp_pedagio_'.$reg_regioes_precos['id']);
				$fator_cubagem = Tools::getValue('fkcarrier_transp_cubagem_'.$reg_regioes_precos['id']);
			}
		
			$dados = array(
					'nome_regiao'						=> $nome_regiao,
					'prazo_entrega_especifico'			=> $prazo_entrega,
					'regiao_uf' 						=> $regiao_uf,
					'regiao_cep' 						=> Tools::getValue('fkcarrier_transp_intervalos_cep_'.$reg_regioes_precos['id']),
					'tipo_preco'						=> $tipo_preco,
					'preco_1'							=> $preco_1,
					'preco_2'							=> $preco_2,
					'preco_3'							=> $preco_3,
					'valor_adicional_excedente_kilo'	=> $valor_adicional,
					'percentual_seguro'					=> $percentual_seguro,
					'valor_pedagio'						=> $valor_pedagio,
					'fator_cubagem'						=> $fator_cubagem
			);
		
			Db::getInstance()->update('fkcarrier_regioes_precos', $dados, 'id = '.(int)$reg_regioes_precos['id']);
		}
			
		// Upload logo
		$this->uploadLogo($sessao, $id_transp, Tools::getValue('fkcarrier_transp_nome_'.$id_transp));
			
		// Recupera dados da transportadora
		$correios_transp = Db::getInstance()->getRow('SELECT `id_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id` = '.(int)$id_transp);
		
		if ($correios_transp['id_carrier'] == 0) {
		
			$configCarrier = array(
					'name' 					=> Tools::getValue('fkcarrier_transp_nome_'.$id_transp),
					'id_tax_rules_group' 	=> 0,
					'active' 				=> (!Tools::getValue('fkcarrier_transp_ativo_'.$id_transp) ? '0' : '1'),
					'deleted' 				=> false,
					'shipping_handling' 	=> false,
					'range_behavior' 		=> true,
					'is_module' 			=> true,
					'shipping_external' 	=> true,
					'shipping_method' 		=> 0,
					'external_module_name' 	=> $this->name,
					'need_range' 			=> true,
					'url' 					=> '',
					'is_free' 				=> false,
					'grade' 				=> Tools::getValue('fkcarrier_transp_grade_'.$id_transp),
					'id_shop'				=> $this->context->shop->id,
					'id_correios_transp'	=> $id_transp
			);
				
			// Atualiza o campo id_carrier
			$id_carrier = $this->instalaCarrier($configCarrier);
			Db::getInstance()->update('fkcarrier_correios_transp', array('id_carrier' => $id_carrier), '`id` = '.(int)$id_transp);
		}else {
			$configCarrier = array(
					'id_carrier' 			=> $correios_transp['id_carrier'],
					'id_correios_transp'	=> $id_transp,
					'name' 					=> Tools::getValue('fkcarrier_transp_nome_'.$id_transp),
					'active'				=> (!Tools::getValue('fkcarrier_transp_ativo_'.$id_transp) ? '0' : '1'),
					'grade' 				=> Tools::getValue('fkcarrier_transp_grade_'.$id_transp),
					'id_shop'				=> $this->context->shop->id
			);
				
			$this->alteraCarrier($configCarrier);
		}
	}

    private function incluiFreteGratis() {

        // Insere registro em fkcarrier_frete_gratis
        $dados = array(
            'id_shop'               => $this->context->shop->id,
            'id_correios_transp'	=> 0,
            'nome_regiao'			=> 'Nova Região',
            'regiao_uf' 			=> '',
            'regiao_cep'			=> '',
            'valor_pedido'			=> '0',
            'id_produtos'			=> '',
            'ativo'					=> '1'
        );

        Db::getInstance()->insert('fkcarrier_frete_gratis', $dados);
    }

    private function excluiFreteGratis() {

        // Recupera id da regiao frete gratis a ser excluida
        $id_frete_gratis = Tools::getValue('id_frete_gratis');
        Db::getInstance()->delete('fkcarrier_frete_gratis', '`id` = '.(int)$id_frete_gratis);
    }

    private function salvaFreteGratis() {

        // Recupera id da regiao frete gratis salva
        $id_frete_gratis = Tools::getValue('id_frete_gratis');

        // Processa regiao uf
        $frete_gratis_uf = Tools::getValue('fkcarrier_frete_gratis_uf_'.$id_frete_gratis);

        $regiao_uf = '';

        if ($frete_gratis_uf) {
            foreach ($frete_gratis_uf as $uf) {
                $regiao_uf .= $uf.'/';
            }
        }

        $dados = array(
            'id_correios_transp'    => Tools::getValue('fkcarrier_frete_gratis_transp_'.$id_frete_gratis),
            'nome_regiao'           => Tools::getValue('fkcarrier_frete_gratis_nome_regiao_'.$id_frete_gratis),
            'regiao_uf'             => $regiao_uf,
            'regiao_cep'            => Tools::getValue('fkcarrier_frete_gratis_intervalos_cep_'.$id_frete_gratis),
            'valor_pedido'          => Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$id_frete_gratis),
            'id_produtos'           => Tools::getValue('fkcarrier_frete_gratis_relacao_produtos_'.$id_frete_gratis),
            'ativo'                 => (!Tools::getValue('fkcarrier_frete_gratis_ativo_'.$id_frete_gratis) ? '0' : '1')
        );

        Db::getInstance()->update('fkcarrier_frete_gratis', $dados, 'id = '.(int)$id_frete_gratis);

    }

    private function excluiTabelasOff() {

        // Recupera id ser excluida
        $id_correios_transp = Tools::getValue('id_correios_transp');
        Db::getInstance()->delete('fkcarrier_tabelas_offline', '`id_correios_transp` = '.(int)$id_correios_transp);
    }

    private function salvaTabelasOff() {

        // Recupera id
        $id_correios_transp = Tools::getValue('id_correios_transp');

        // Processa tabelas offline
        $tabelas_off = Db::getInstance()->executeS('SELECT `id`, `minha_cidade` FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` WHERE `id_correios_transp` = '.(int)$id_correios_transp);

        foreach ($tabelas_off as $reg_tabelas_off) {

            if ($reg_tabelas_off['minha_cidade'] == 0) {
                $tabela_interior = Tools::getValue('fkcarrier_tabelas_off_interior_'.$reg_tabelas_off['id']);
            }else {
                $tabela_interior = '';
            }

            $dados = array(
                'tabela_capital'    => Tools::getValue('fkcarrier_tabelas_off_capital_'.$reg_tabelas_off['id']),
                'tabela_interior'   => $tabela_interior
            );

            Db::getInstance()->update('fkcarrier_tabelas_offline', $dados, '`id` = '.(int)$reg_tabelas_off['id']);
        }

        // Exclui o cache
        $this->excluiCache();
    }

	private function uploadLogo($sessao, $id, $nome_carrier) {
		
		$extensoes_permitidas = array('0' => 'jpg');
		
		if ($sessao == 'configServicosCorreios') {

			if(!empty($_FILES['fkcarrier_correios_imagem_'.$id]['name'])) {
		
				// Verifica se houve algum erro com o upload
				if ($_FILES['fkcarrier_correios_imagem_'.$id]['error'] != 0) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Erro durante upload da imagem.');
					return;
				}
		
				// Verifica extensão do arquivo
				$array = explode('.', $_FILES['fkcarrier_correios_imagem_'.$id]['name']);
				$extensao = end($array);
				$extensao = strtolower($extensao);
		
				if (array_search($extensao, $extensoes_permitidas) === false) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Permitido somente arquivos com extensões jpg.');
					return;
				}
		
				// Move o logo para a pasta upload dando rename
				if (!move_uploaded_file($_FILES['fkcarrier_correios_imagem_'.$id]['tmp_name'], $this->_path_logo.$id.'.'.$extensao)) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Não foi possível mover o arquivo para a pasta img.');
					return;
				}
			}
		}else {
			if(!empty($_FILES['fkcarrier_transp_imagem_'.$id]['name'])) {
				
				// Verifica se houve algum erro com o upload
				if ($_FILES['fkcarrier_transp_imagem_'.$id]['error'] != 0) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Erro durante upload da imagem.');
					return;
				}
					
				// Verifica extensão do arquivo
				$array = explode('.', $_FILES['fkcarrier_transp_imagem_'.$id]['name']);
				$extensao = end($array);
				$extensao = strtolower($extensao);
					
				if (array_search($extensao, $extensoes_permitidas) === false) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Permitido somente arquivos com extensões jpg.');
					return;
				}
					
				// Move o logo para a pasta upload dando rename
				if (!move_uploaded_file($_FILES['fkcarrier_transp_imagem_'.$id]['tmp_name'], $this->_path_logo.$id.'.'.$extensao)) {
					$this->_postErrors[] = $nome_carrier.': '.$this->l('Não foi possível mover o arquivo para a pasta img.');
					return;
				}
			}
		}
	}
	
	private function instalaCarrier($configCarrier) {
		
		$carrier = new Carrier();
		$carrier->name 					= $configCarrier['name'];
		$carrier->id_tax_rules_group 	= $configCarrier['id_tax_rules_group'];
		$carrier->active 				= $configCarrier['active'];
		$carrier->deleted 				= $configCarrier['deleted'];
		$carrier->shipping_handling 	= $configCarrier['shipping_handling'];
		$carrier->range_behavior 		= $configCarrier['range_behavior'];
		$carrier->is_module 			= $configCarrier['is_module'];
		$carrier->shipping_external 	= $configCarrier['shipping_external'];
		$carrier->shipping_method 		= $configCarrier['shipping_method'];
		$carrier->external_module_name 	= $configCarrier['external_module_name'];
		$carrier->need_range 			= $configCarrier['need_range'];
		$carrier->url 					= $configCarrier['url'];
		$carrier->is_free 				= $configCarrier['is_free'];
		$carrier->grade 				= $configCarrier['grade'];
		
		$languages = Language::getLanguages(true);
		foreach ($languages as $language) {
			$carrier->delay[(int)$language['id_lang']] = 'Prazo de Entrega';
		}
		
		if ($carrier->add()) {
		
			// Liga carrier ao shop
			$carrier->associateTo($configCarrier['id_shop']);
			
			// Liga carrier aos grupos de clientes
            $grupos = Group::getGroups(true);

            if (version_compare(_PS_VERSION_, '1.5.5.0', '<')) {

                foreach ($grupos as $grupo) {

                    $dados = array(
                        'id_carrier'    => $carrier->id,
                        'id_group'      => $grupo['id_group']
                    );

                    Db::getInstance()->insert('carrier_group', $dados);
                }
            }else {
                $grupos_clientes = array();

                foreach ($grupos as $grupo) {
                    $grupos_clientes[] = $grupo['id_group'];
                }

                $carrier->setGroups($grupos_clientes);
            }

			// Define intervalo de precos
			$intervalo_preco = new RangePrice();
			
			if (!$intervalo_preco->rangeExist($carrier->id, '0', '100000')) {
				$intervalo_preco->id_carrier = $carrier->id;
				$intervalo_preco->delimiter1 = '0';
				$intervalo_preco->delimiter2 = '100000';
				$intervalo_preco->add();
			}
		
			// Define intervalo de pesos
			$intervalo_peso = new RangeWeight();
			
			if (!$intervalo_peso->rangeExist($carrier->id, '0', '10000')) {
				$intervalo_peso->id_carrier = $carrier->id;
				$intervalo_peso->delimiter1 = '0';
				$intervalo_peso->delimiter2 = '10000';
				$intervalo_peso->add();;
			}
		
			// Liga carrier as regioes
			$regioes = Zone::getZones(true);
			foreach ($regioes as $regiao) {
				
				if (!$carrier->checkCarrierZone($carrier->id, $regiao['id_zone'])) {
					$carrier->addZone($regiao['id_zone']);
				}
				
			}
		
			// Copia logo
			$logo = $this->_path_logo.$configCarrier['id_correios_transp'].'.jpg';
			
			if (file_exists($logo)) {
				
				// Exclui logo da pasta tmp
				if (file_exists(_PS_TMP_IMG_DIR_.'carrier_mini_'.$carrier->id.'_'.$configCarrier['id_shop'].'.jpg')) {
					unlink(_PS_TMP_IMG_DIR_.'carrier_mini_'.$carrier->id.'_'.$configCarrier['id_shop'].'.jpg');
				}
				
				copy($logo, _PS_SHIP_IMG_DIR_.$carrier->id.'.jpg');
			}
			
			// Retorna o ID Carrier
			return $carrier->id;
		}

        return false;
	}
	
	private function alteraCarrier($configCarrier) {
		
		$carrier = new Carrier($configCarrier['id_carrier']);
		$carrier->name 		= $configCarrier['name'];
		$carrier->active 	= $configCarrier['active'];
		$carrier->grade		= $configCarrier['grade'];
		$carrier->update();
		
		// Copia logo
		$logo = $this->_path_logo.$configCarrier['id_correios_transp'].'.jpg';
			
		if (file_exists($logo)) {

			// Exclui logo da pasta tmp
			if (file_exists(_PS_TMP_IMG_DIR_.'carrier_mini_'.$carrier->id.'_'.$configCarrier['id_shop'].'.jpg')) {
				unlink(_PS_TMP_IMG_DIR_.'carrier_mini_'.$carrier->id.'_'.$configCarrier['id_shop'].'.jpg');
			}
		
			copy($logo, _PS_SHIP_IMG_DIR_.$carrier->id.'.jpg');
		}
		
	}
	
	private function excluiCarrier() {
		
		$correios_transp = Db::getInstance()->executeS('SELECT `id_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp`');
		
		foreach ($correios_transp as $reg) {

            // Exclui logos
            if (file_exists(_PS_SHIP_IMG_DIR_.'/'.$reg['id_carrier'].'.jpg')) {
                unlink(_PS_SHIP_IMG_DIR_.'/'.$reg['id_carrier'].'.jpg');
            }

            // Marca como excluido o carrier do Prestashop
			$carrier = new Carrier($reg['id_carrier']);

            if ($carrier->id) {
                $carrier->deleted = true;
                $carrier->update();
            }
		}
		
		return true;
	}
	
	private function displayForm() {
		
		$this->_html .= '<fieldset>';
		$this->_html .= '<legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Status do Módulo FKcarrier').'</legend>';
		
		$alert = array();
		$enviarAlert = false;
		
		// Verifica instalacao do SOAP
		if (!extension_loaded('soap')) {
			$enviarAlert = true;
			$alert['soapMsg'] = $this->l('Ative a função SOAP em seu PHP.');
			$alert['soapImg'] = '<img src="'._PS_IMG_.'admin/warn2.png" />';
		}

		// Verifica Configuracoes Gerais
		if (!Configuration::get('FKCARRIER_MEU_CEP')) {
			$enviarAlert = true;
			$alert['confGeralMsg'] = $this->l('Configurações Gerais não preenchidas.');
			$alert['confGeralImg'] = '<img src="'._PS_IMG_.'admin/warn2.png" />';
		}

		// Verifica embalagens
		if (Configuration::get('FKCARRIER_EMBALAGEM') == '1') {

			$embalagens = Db::getInstance()->ExecuteS('SELECT `id` FROM `'._DB_PREFIX_.'fkcarrier_embalagens` Where `ativo` = 1 And `id_shop` = '.$this->context->shop->id);

			if (!$embalagens) {
				$enviarAlert = true;
				$alert['embMsg'] = $this->l('Embalagens Padrão não definidas.');
				$alert['embImg'] = '<img src="'._PS_IMG_.'admin/warn2.png" />';
			}
		}

		// Verifica ativacao dos servicos dos Correios e transportadoras
		$correios_transp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `ativo` = 1 AND `id_shop` = '.$this->context->shop->id);

		if (!$correios_transp) {
			$enviarAlert = true;
			$alert['correiosTranspMsg'] = $this->l('Serviços dos Correios e/ou Tranportadoras não definidos.');
			$alert['correiosTranspImg'] = '<img src="'._PS_IMG_.'admin/warn2.png" />';
		}


		// Verifica tabelas offline
		$tabelas_off = Db::getInstance()->executeS('SELECT `tabela_capital` FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` ');

		foreach ($tabelas_off as $reg) {

			if ($reg['tabela_capital'] == '' Or $reg['tabela_capital'] == NULL) {
				$enviarAlert = true;
				$alert['tabOffMsg'] = $this->l('Tabelas Offline não definidas. Por favor utilize o navegador Firefox para gerar as tabelas.');
				$alert['tabOffImg'] = '<img src="'._PS_IMG_.'admin/warn2.png" />';
			}
		}

		// Mensagens
		if ($enviarAlert == false) {
			$this->_html .= '<img src="'._PS_IMG_ .'admin/module_install.png" /><strong>'.$this->l('FKcarrier está configurado e online!').'</strong>';
		}else {
			$this->_html .= '<strong>'.$this->l('FKcarrier ainda não configurado, por favor verifique os alertas abaixo:').'</strong>';
			$this->_html .= '<br><br>';

			if (isset($alert['soapMsg'])) {
				$this->_html .= $alert['soapImg'].$alert['soapMsg'];
				$this->_html .= '<br>';
			}

			if (isset($alert['confGeralMsg'])) {
				$this->_html .= $alert['confGeralImg'].$alert['confGeralMsg'];
				$this->_html .= '<br>';
			}

			if (isset($alert['embMsg'])) {
				$this->_html .= $alert['embImg'].$alert['embMsg'];
				$this->_html .= '<br>';
			}

			if (isset($alert['correiosTranspMsg'])) {
				$this->_html .= $alert['correiosTranspImg'].$alert['correiosTranspMsg'];
				$this->_html .= '<br>';
			}

			if (isset($alert['tabOffMsg'])) {
				$this->_html .= $alert['tabOffImg'].$alert['tabOffMsg'];
				$this->_html .= '<br>';
			}
		}

		$this->_html .= '</fieldset>';
		$this->displayFormConfig();
		
		return $this->_html;
		
	}
	
	private function displayFormConfig() {
		
		//Identificacao das abas
		$id_config_geral = $this->l('Configurações Gerais');
		$id_cadastro_cep = $this->l('Cadastro CEP');
		$id_prazos_entrega = $this->l('Prazos de Entrega');
		$id_embalagens = $this->l('Embalagens Padrão');
		$id_especificacoes_correios = $this->l('Especificações Correios');
		$id_servicos_correios = $this->l('Serviços Correios');
		$id_transp = $this->l('Transportadoras');
        $id_frete_gratis = $this->l('Frete Grátis');
        $id_tabelas_off = $this->l('Tabelas Offline');

		$this->_html .= '<ul id="fkcarrier_menuTab">';
		$this->_html .= '   <li id="menuTab2" class="menuTabButton selected">'.$id_config_geral.'</li>';
		$this->_html .= '   <li id="menuTab3" class="menuTabButton">'.$id_cadastro_cep.'</li>';
		$this->_html .= '   <li id="menuTab4" class="menuTabButton">'.$id_prazos_entrega.'</li>';
		$this->_html .= '   <li id="menuTab5" class="menuTabButton">'.$id_embalagens.'</li>';
		$this->_html .= '   <li id="menuTab6" class="menuTabButton">'.$id_especificacoes_correios.'</li>';
		$this->_html .= '   <li id="menuTab7" class="menuTabButton">'.$id_servicos_correios.'</li>';
		$this->_html .= '   <li id="menuTab8" class="menuTabButton">'.$id_transp.'</li>';
		$this->_html .= '   <li id="menuTab9" class="menuTabButton">'.$id_frete_gratis.'</li>';
		$this->_html .= '   <li id="menuTab10" class="menuTabButton">'.$id_tabelas_off.'</li>';
		$this->_html .= '</ul>';

		$this->_html .= '<div id="fkcarrier_tabList">';

		$this->_html .= '   <div id="menuTab2Sheet" class="fkcarrier_tabItem selected">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayConfigGeral.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab3Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayCadastroCep.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab4Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayPrazoEntrega.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab5Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayEmbalagens.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab6Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayEspecificacoesCorreios.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab7Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayServicosCorreios.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab8Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayTransp.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab9Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayFreteGratis.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '   <div id="menuTab10Sheet" class="fkcarrier_tabItem">';
		ob_start();
		include_once dirname(__FILE__).'/config/displayTabelasOff.php';
		$this->_html .= ob_get_contents();
		ob_end_clean();
		$this->_html .= '   </div>';

		$this->_html .= '</div>';

		$this->_html .= '<script>';
		$this->_html .= '   $(".menuTabButton").click(function () {';
		$this->_html .= '       $(".menuTabButton.selected").removeClass("selected");';
		$this->_html .= '       $(this).addClass("selected");';
		$this->_html .= '       $(".fkcarrier_tabItem.selected").removeClass("selected");';
		$this->_html .= '       $("#" + this.id + "Sheet").addClass("selected");';
		$this->_html .= '   });';
		$this->_html .= '</script>';
		
		if (isset($_GET['id_tab'])) {
			$this->_html .= '<script>';
			$this->_html .= '   $(".menuTabButton.selected").removeClass("selected");';
			$this->_html .= '   $("#menuTab'.Tools::safeOutput(Tools::getValue('id_tab')).'").addClass("selected");';
			$this->_html .= '   $(".fkcarrier_tabItem.selected").removeClass("selected");';
			$this->_html .= '   $("#menuTab'.Tools::safeOutput(Tools::getValue('id_tab')).'Sheet").addClass("selected");';
			$this->_html .= '</script>';
		}
		
		return $this->_html;
		
	}
	
	public function getOrderShippingCost($params, $shipping_cost) {

        $cep_destino = '';
        $uf_destino = '';

        // Inicializa a classe funcoes
        $funcoes = new FKcarrierFuncoes();

        // Verifica se o cliente está logado e recupera os dados do endereco de entrega
        if ($this->context->customer->isLogged()) {

            $address = new Address($params->id_address_delivery);

            // Recupera CEP destino
            if ($address->postcode) {
                $cep_destino = $address->postcode;
            }
        }else {
            if (Configuration::get('FKCARRIER_CALCULO_LOGADO') != 'on') {

                // Recupera dados do CEP informado
                if ($this->context->cookie->fkcarrier_cep) {
                    $cep_destino = $this->context->cookie->fkcarrier_cep;
                }
            }
        }

        // Para pedidos efetuados via Admin
        if (!$cep_destino) {
            $address = new Address($params->id_address_delivery);

            // Ignora Carrier se não existir CEP
            if (!$address->postcode) {
                return false;
            }

            $cep_destino = $address->postcode;
        }

        // Valida CEP destino
        $cep_destino = trim(preg_replace("/[^0-9]/", "", $cep_destino));

        // Ignora Carrier se o CEP for invalido
        if (strlen($cep_destino) <> 8) {
            return false;
        }

        // Recupera UF
        $uf_destino = $funcoes->retornaUF($cep_destino);

        if ($uf_destino == 'erro') {
            return false;
        }

        // Recupera UF origem
        $uf_origem = $funcoes->retornaUF(trim(preg_replace("/[^0-9]/", "", Configuration::get('FKCARRIER_MEU_CEP'))));

        // Verifica se o Carrier atende a regiao e recupera os dados cadastrados
        $dados_carrier = $this->verificaRegiaoAtendida($this->id_carrier, $cep_destino, $uf_destino);

        // Ignora Carrier se ele nao atende a regiao
        if ($dados_carrier['regiao_atendida'] == false) {
            return false;
        }

        // Recupera valor do pedido
        if (isset($this->context->cart)) {
            $valor_pedido = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        }else {
            // Para pedidos efetuados via Admin
            $cart = new cart($params->id);
            $valor_pedido = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
        }

        // Verifica se e frete gratis considerando valor do pedido
        $pedido_frete_gratis = $this->verificaPedidoFreteGratis($cep_destino, $uf_destino, $valor_pedido);

        // Instacia Carrier
        $carrier = new Carrier($this->id_carrier);
        
        // Recupera produtos
        $produtos = array();
        $existe_produto_frete_gratis = false;

        foreach ($params->getProducts() as $prod) {

            // Ignora o produto se for virtual
            if ($prod['is_virtual'] == 1) {
                continue;
            }
            
            // Ignora o Carrier se não atender ao filtro
            if (!$this->processaFiltros($this->id_carrier, $carrier->id_reference, $prod['id_product'], $this->context->shop->id)) {
                return false;
            }
                    
            // Calcula cubagem
            $cubagem = $prod['height'] * $prod['width'] * $prod['depth'];

            // Verifica se o produto e frete gratis
            $produto_frete_gratis = $this->verificaProdutoFreteGratis($cep_destino, $uf_destino, $prod['id_product']);

            if ($produto_frete_gratis['frete_gratis'] == true) {

                $existe_produto_frete_gratis = true;

                // Se nao existir carrier frete gratis definido no pedido assume o do produto
                if ($pedido_frete_gratis['carrier_frete_gratis'] == 0) {
                    $pedido_frete_gratis['carrier_frete_gratis'] = $produto_frete_gratis['carrier_frete_gratis'];
                }
            }

            for ($qty = 0; $qty < $prod['quantity']; $qty++) {

                $produtos[] = array(
                    'id'            => $prod['id_product'],
                    'altura'        => $prod['height'],
                    'largura'       => $prod['width'],
                    'comprimento'   => $prod['depth'],
                    'peso'          => $prod['weight'],
                    'cubagem'       => $cubagem,
                    'valor_produto' => $prod['price_wt'],
                    'frete_gratis'  => $produto_frete_gratis['frete_gratis']
                );
            }
        }

        // Processa embalagens
        if (Configuration::get('FKCARRIER_EMBALAGEM') == '0') {
            $embalagens = $this->processaEmbalagemIndividual($this->id_carrier, $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
        }else {
            if (Configuration::get('FKCARRIER_EMBALAGEM') == '1') {
                $embalagens = $this->processaEmbalagemPadrao($this->id_carrier, $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
            }else {
                $embalagens = $this->processaEmbalagemVirtual($this->id_carrier, $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
            }
        }

        // Ignora carrier se nao existirem embalagens (ou seja as dimensoes estao fora do permitido
        if (!$embalagens) {
            return false;
        }

        $fkcarrier = array(
            'cep_origem'                        => trim(preg_replace("/[^0-9]/", "", Configuration::get('FKCARRIER_MEU_CEP'))),
            'uf_origem'                         => $uf_origem,
            'cep_destino'                       => $cep_destino,
            'uf_destino'                        => $uf_destino,
            'tempo_preparacao'                  => Configuration::get('FKCARRIER_TEMPO_PREPARACAO'),
            'servico_correios'                  => $dados_carrier['servico_correios'],
            'cod_servico'                       => $dados_carrier['cod_servico'],
            'cod_administrativo'                => $dados_carrier['cod_administrativo'],
            'senha'                             => $dados_carrier['senha'],
            'prazo_entrega_especifico'          => $dados_carrier['prazo_entrega_especifico'],
            'tipo_preco'                        => $dados_carrier['tipo_preco'],
            'tabela_preco'                      => $dados_carrier['tabela_preco'],
            'valor_adicional_excedente_kilo'    => $dados_carrier['valor_adicional_excedente_kilo'],
            'percentual_seguro'                 => $dados_carrier['percentual_seguro'],
            'valor_pedagio'                     => $dados_carrier['valor_pedagio'],
            'fator_cubagem'                     => $dados_carrier['fator_cubagem'],
            'valor_pedido'                      => $valor_pedido,
            'pedido_frete_gratis'               => $pedido_frete_gratis['frete_gratis'],
            'produto_frete_gratis'              => $existe_produto_frete_gratis,
            'mostrar_todos_carrier'             => ((Configuration::get('FKCARRIER_FRETE_GRATIS_TRANSP') == 'on') ? true : false),
            'carrier_atual'                     => $this->id_carrier,
            'carrier_frete_gratis'              => $pedido_frete_gratis['carrier_frete_gratis'],
            'produtos'                          => $produtos,
            'embalagens'                        => $embalagens
        );

        // Ignora Carrier se o Pedido for Frete Gratis e configurado para mostrar somente o Carrier de Frete Gratis
        if ($fkcarrier['pedido_frete_gratis'] == true And $fkcarrier['mostrar_todos_carrier'] == false And $fkcarrier['carrier_atual'] != $fkcarrier['carrier_frete_gratis']) {
            return false;
        }

        // Ignora Carrier se existe Produto com Frete Gratis e configurado para mostrar somente o Carrier de Frete Gratis
        if ($fkcarrier['produto_frete_gratis'] == true And $fkcarrier['mostrar_todos_carrier'] == false And $fkcarrier['carrier_atual'] != $fkcarrier['carrier_frete_gratis']) {
            return false;
        }

        if ($fkcarrier['servico_correios'] == true) {
            // Calcula valor do frete dos Correios
            if (Configuration::get('FKCARRIER_OFFLINE') == 'on') {
                return $this->processaCorreiosOffline($fkcarrier);
            }else {
                return $this->processaCorreiosOnline($fkcarrier);
            }
        }else {
            // Calcula valor do frete das transportadoras
            return $this->processaTransp($fkcarrier);
        }

	}
	
	public function getOrderShippingCostExternal($params) {
		return $this->getOrderShippingCost($params, 0);
	}
	
	public function hookDisplayBackOfficeHeader() {

        // CSS
        if (version_compare(substr(_PS_VERSION_, 0, 5), '1.6.0', '<')) {
            $this->context->controller->addCSS($this->_path.'css/fkcarrier_admin_15x.css');
        }else {
            $this->context->controller->addCSS($this->_path.'css/fkcarrier_admin_16x.css');
        }

		$this->context->controller->addCSS($this->_path.'css/fkcarrier_tab.css');

		// JS
		$this->context->controller->addJS($this->_path.'js/fkcarrier_admin.js');
		$this->context->controller->addJS($this->_path.'js/maskedinput.js');
	}

    public function hookdisplayHeader($params) {
        // CSS
        if (version_compare(substr(_PS_VERSION_, 0, 5), '1.6.0', '<')) {
            $this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.css');
			$this->context->controller->addCSS($this->_path.'css/fkcarrier_front_15x.css');
        }else {
            $this->context->controller->addCSS($this->_path.'css/fkcarrier_front_16x.css');
        }

		// JS
		$this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');

		// Adiciona Fancybox caso QuickView esteja desativado
		if (version_compare(substr(_PS_VERSION_, 0, 5), '1.6.0', '>=')) {
			if (!Configuration::get('PS_QUICK_VIEW')) {
				$this->context->controller->addjqueryPlugin('fancybox');
			}
		}

		$this->context->controller->addJS($this->_path.'js/fkcarrier_fancybox.js');
		$this->context->controller->addJS($this->_path.'js/fkcarrier_front.js');
        $this->context->controller->addJS($this->_path.'js/maskedinput.js');
    }
	
	public function hookactionCarrierUpdate($params) {

        $atualizado = false;

        $correios_transp = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id_carrier` = '.(int)$params['id_carrier']);

        // Verifica se houve alteracao no id
        if ((int)$correios_transp['id_carrier'] != (int)$params['carrier']->id) {
            $novo_id = $params['carrier']->id;
            $atualizado = true;
        }else {
            $novo_id = $correios_transp['id_carrier'];
        }

        // Verifica se houve alteracao no nome
        if ($correios_transp['nome_carrier'] != $params['carrier']->name) {
            $novo_nome = $params['carrier']->name;
            $atualizado = true;
        }else {
            $novo_nome = $correios_transp['nome_carrier'];
        }

        // Verifica se houve alteracao na grade
        if ((int)$correios_transp['grade'] != (int)$params['carrier']->grade) {
            $nova_grade = $params['carrier']->grade;
            $atualizado = true;
        }else {
            $nova_grade = $correios_transp['grade'];
        }

        // Verifica se houve alteracao no campo ativo
        if ($correios_transp['ativo'] != $params['carrier']->active) {
            $novo_ativo = $params['carrier']->active;
            $atualizado = true;
        }else {
            $novo_ativo = $correios_transp['ativo'];
        }

        if ($atualizado == true) {

            $dados = array(
                'id_carrier' => $novo_id,
                'nome_carrier' => $novo_nome,
                'grade' => $nova_grade,
                'ativo' => $novo_ativo
            );

            Db::getInstance()->update('fkcarrier_correios_transp', $dados, '`id_carrier` = '.(int)$correios_transp['id_carrier']);
        }

	}

    public function hookdisplayBeforeCarrier($params) {

        if (!isset($this->context->smarty->tpl_vars['delivery_option_list'])) {
            return;
        }

        $delivery_option_list = $this->context->smarty->tpl_vars['delivery_option_list'];

        foreach ($delivery_option_list->value as $id_address) {

            foreach ($id_address as $key) {

                foreach ($key['carrier_list'] as $id_carrier) {

                    if (isset($this->_prazo_entrega[$id_carrier['instance']->id])) {

                        if (is_numeric($this->_prazo_entrega[$id_carrier['instance']->id])) {

                            if ($this->_prazo_entrega[$id_carrier['instance']->id] == 0) {
                                $msg = $this->l(' no mesmo dia');
                            }else {
                                if ($this->_prazo_entrega[$id_carrier['instance']->id] > 1) {
                                    $msg = $this->_prazo_entrega[$id_carrier['instance']->id].$this->l(' dias úteis');
                                }else {
                                    $msg = $this->_prazo_entrega[$id_carrier['instance']->id].$this->l(' dia útil');
                                }
                            }
                        }else {
                            $msg = $this->_prazo_entrega[$id_carrier['instance']->id];
                        }

                        $id_carrier['instance']->delay[$this->context->cart->id_lang] = $this->l('Prazo de entrega:').' '.$msg;
                    }
                }
            }
        }

    }

    public function hookdisplayShoppingCartFooter($params) {

        // Retorna se nao for para mostrar no carrinho
        if (Configuration::get('FKCARRIER_BLOCO_CARRINHO') != 'on') {
            return false;
        }

        // Retorna se o carrinho estiver vazio
        if (!$params['products']) {
            return false;
        }
        
        // Retorna se carrinho contiver somente produtos virtuais
        $virtual = true;
        
        foreach ($this->context->cart->getProducts() as $prod) {
            if ($prod['is_virtual'] == 0) {
                $virtual = false;
            }    
        }
        
        if ($virtual == true) {
            return false;
        }

        $this->processaFrete($params, 'carrinho');
        
        return $this->display(__FILE__, 'views/carrinho.tpl');
    }
    
    public function hookDisplayRightColumnProduct($params) {
        
        // Retorna se for versao 1.5.x
        if (version_compare(substr(_PS_VERSION_, 0 ,5), '1.6.0', '<')) {
            return false;
        }
        
        // Retorna se nao for para mostrar em produtos
        if (Configuration::get('FKCARRIER_BLOCO_PRODUTO') != 'on') {
            return false;
        }
        
        // Retorna se nao for para mostrar no resumo
        if (Configuration::get('FKCARRIER_BLOCO_POSICAO') != '0') {
            return false;
        }
        
        // Retorna se $params nao contem dados do produto (override ainda nao executado)
        if (!isset($params['product'])) {
            return false;
        }

        // Retorna se for produto virtual
        if ($params['product']->is_virtual == 1) {
            return false;
        }
        
        $this->processaFrete($params, 'produto');
        
        // Informa se e para usar lightBox - smarty
        $this->smarty->assign(array('fkcarrier_lightbox' => false));
        
        // Informa id do produto - smarty
        $this->smarty->assign(array('fkcarrier_id_produto' => $params['product']->id));
        
        if (Configuration::get('FKCARRIER_BLOCO_PRODUTO_LB') == 'on') {
            $this->context->controller->addJS($this->_path.'js/fkcarrier_fancybox.js');
            $this->smarty->assign(array('fkcarrier_lightbox' => true));
        }
        
        return $this->display(__FILE__, 'views/produto_resumo.tpl');           
    }

    public function hookdisplayFooterProduct($params) {

        // Retorna se nao for para mostrar em produtos
        if (Configuration::get('FKCARRIER_BLOCO_PRODUTO') != 'on') {
            return false;
        }
        
        // Retorna se nao for para mostrar no resumo (somente Prestashop 1.6.x)
        if (version_compare(substr(_PS_VERSION_, 0 ,5), '1.6.0', '>=')) {
            if (Configuration::get('FKCARRIER_BLOCO_POSICAO') != '1') {
                return false;
            }
        }
        
        // Retorna se for produto virtual
        if ($params['product']->is_virtual == 1) {
            return false;
        }
        
        $this->processaFrete($params, 'produto');

		// Informa id do produto - smarty
		$this->smarty->assign(array('fkcarrier_id_produto' => $params['product']->id));

		// Informa se e para usar lightBox - smarty
        if (Configuration::get('FKCARRIER_BLOCO_PRODUTO_LB') == 'on') {
            $this->smarty->assign(array('fkcarrier_lightbox' => true));
        }else {
			$this->smarty->assign(array('fkcarrier_lightbox' => false));
		}
        
        return $this->display(__FILE__, 'views/produto.tpl'); 
    }

	public function hookdisplayLeftColumn($params) {

		// Retorna se nao for para mostrar na coluna esquerda
		if (Configuration::get('FKCARRIER_RASTREIO_LEFT') != 'on') {
			return false;
		}

		return $this->display(__FILE__, 'views/rastreio_col_left.tpl');
	}

	public function hookdisplayRightColumn($params) {

		// Retorna se nao for para mostrar na coluna direita
		if (Configuration::get('FKCARRIER_RASTREIO_RIGHT') != 'on') {
			return false;
		}

		return $this->display(__FILE__, 'views/rastreio_col_right.tpl');
	}

	public function hookdisplayFooter($params) {

		// Retorna se nao for para mostrar no footer
		if (Configuration::get('FKCARRIER_RASTREIO_FOOTER') != 'on') {
			return false;
		}

		return $this->display(__FILE__, 'views/rastreio_footer.tpl');
	}

	public function hookdisplayCustomerAccount($params) {

		// Retorna se nao for para mostrar no account
		if (Configuration::get('FKCARRIER_RASTREIO_ACCOUNT') != 'on') {
			return false;
		}

		return $this->display(__FILE__, 'views/rastreio_account.tpl');
	}

    private function processaFrete($params, $origem) {
        
        // Inicializa variaveis
        $cep_destino = '';
        $uf_destino = '';
        $valor_frete = 0;
        
        // Inicializa CEP - smarty
        $this->smarty->assign(array('fkcarrier_cep' => ''));
        if ($this->context->cookie->fkcarrier_cep) {
            $this->smarty->assign(array('fkcarrier_cep' => $this->context->cookie->fkcarrier_cep));
        }
        
        // Inicializa foco - smarty
        $this->smarty->assign(array('fkcarrier_foco' => false));
        
        // Inicializa variavel de mensagem
        $fkcarrier_msg = $this->l('Aguardando CEP');

        // Inicializa variavel do valor frete
        $fkcarrier_frete = array();
        
        // Inicializa a classe funcoes
        $funcoes = new fkcarrierFuncoes();
        
        // Inicializa variavel que indica se deve ou não processar o frete
        $processar = false;
        
        // Recupera CEP Destino
        $validar_cep = false;
        
        if ($origem == 'produto' and Tools::isSubmit('submitProd')) {
            
            $processar = true;
            $validar_cep = true;
            $cep_destino = $_POST['fkcarrier_cep_prod'];
            
            // Grava cookie do foco
            $this->context->cookie->fkcarrier_foco = true;
                
        }elseif ($origem == 'carrinho' and (Tools::isSubmit('submitCarrinho') Or $this->context->customer->isLogged() Or isset($this->context->cookie->fkcarrier_cep))) {
             
            $processar = true;
            $validar_cep = true;
             
             // Se enviado CEP via submit
            if (Tools::isSubmit('submitCarrinho')){
                $cep_destino = $_POST['fkcarrier_cep_carrinho'];
                
                // Grava cookie do foco
                $this->context->cookie->fkcarrier_foco = true;
            }else {
                // Se o cliente esta logado
                if ($this->context->customer->isLogged()) {
                    $cep_destino = $params['delivery']->postcode;
                }else {
                    $cep_destino = $this->context->cookie->fkcarrier_cep;
                }
            }
        }
        
        // Valida CEP
        if ($validar_cep) {
            
            // Valida CEP destino
            $cep_destino = trim(preg_replace("/[^0-9]/", "", $cep_destino));

            // Envia mensagem de erro se o CEP for invalido
            if (strlen($cep_destino) <> 8) {

                $processar = false;
                $fkcarrier_msg = $this->l('CEP inválido');
            }else {
                // Recupera UF
                $uf_destino = $funcoes->retornaUF($cep_destino);

                // Envia mensagem de erro se UF não localizada
                if ($uf_destino == 'erro') {
                    $processar = false;
                    $fkcarrier_msg = $this->l('CEP inválido');
                }    
            }
        }
        
        // Processa o frete
        if ($processar) {
            
            // Recupera dados dos servicos dos Correios
            $sql = "SELECT
                        "._DB_PREFIX_."fkcarrier_correios_transp.id_carrier,
                        "._DB_PREFIX_."carrier.id_reference,
                        "._DB_PREFIX_."fkcarrier_correios_transp.nome_carrier
                    FROM "._DB_PREFIX_."fkcarrier_correios_transp
                        INNER JOIN "._DB_PREFIX_."carrier
                            ON "._DB_PREFIX_."fkcarrier_correios_transp.id_carrier = "._DB_PREFIX_."carrier.id_carrier
                    WHERE "._DB_PREFIX_."fkcarrier_correios_transp.ativo = 1";
            
            $correios_transp = Db::getInstance()->executeS($sql);
            
            foreach ($correios_transp as $reg) {
                
                // Recupera UF origem
                $uf_origem = $funcoes->retornaUF(trim(preg_replace("/[^0-9]/", "", Configuration::get('FKCARRIER_MEU_CEP'))));

                // Verifica se o Carrier atende a regiao e recupera os dados cadastrados
                $dados_carrier = $this->verificaRegiaoAtendida($reg['id_carrier'], $cep_destino, $uf_destino);

                // Ignora Carrier se ele nao atende a regiao
                if ($dados_carrier['regiao_atendida'] == false) {
                    continue;
                }
                
                // Recupera valor do pedido, verifica frete gratis e grava dados dos produtos
                if ($origem == 'produto') {
                    
                    // Ignora o Carrier se não atender ao filtro
                    if (!$this->processaFiltros($reg['id_carrier'], $reg['id_reference'], $params['product']->id, $this->context->shop->id)) {
                        continue;
                    }
                
                    $preco = $params['product']->price;
                    $impostos = $params['product']->tax_rate;
                    $valor_pedido = $preco * (1+($impostos/100));
                    
                    // Verifica se e frete gratis considerando valor do produto
                    $pedido_frete_gratis = $this->verificaPedidoFreteGratis($cep_destino, $uf_destino, $valor_pedido);

                    // Calcula cubagem
                    $cubagem = $params['product']->height * $params['product']->width * $params['product']->depth;

                    // Verifica se o produto e frete gratis
                    $produto_frete_gratis = $this->verificaProdutoFreteGratis($cep_destino, $uf_destino, $params['product']->id);

                    $existe_produto_frete_gratis = false;

                    if ($produto_frete_gratis['frete_gratis'] == true) {

                        $existe_produto_frete_gratis = true;

                        // Se nao existir carrier frete gratis definido no pedido assume o do produto
                        if ($pedido_frete_gratis['carrier_frete_gratis'] == 0) {
                            $pedido_frete_gratis['carrier_frete_gratis'] = $produto_frete_gratis['carrier_frete_gratis'];
                        }
                    }

                    // Grava array com dados do produto
                    $produtos = array();
                    $produtos[] = array(
                        'id'            => $params['product']->id,
                        'altura'        => $params['product']->height,
                        'largura'       => $params['product']->width,
                        'comprimento'   => $params['product']->depth,
                        'peso'          => $params['product']->weight,
                        'cubagem'       => $cubagem,
                        'valor_produto' => $valor_pedido,
                        'frete_gratis'  => $produto_frete_gratis['frete_gratis']
                    );    
                }elseif ($origem == 'carrinho') {
                    $valor_pedido = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
                    
                    // Verifica se e frete gratis considerando valor do produto
                    $pedido_frete_gratis = $this->verificaPedidoFreteGratis($cep_destino, $uf_destino, $valor_pedido);

                    // Recupera produtos
                    $produtos = array();
                    $existe_produto_frete_gratis = false;

                    foreach ($this->context->cart->getProducts() as $prod) {

                        // Ignora o produto se for virtual
                        if ($prod['is_virtual'] == 1) {
                            continue;
                        }
                        
                        // Ignora o Carrier se não atender ao filtro
                        if (!$this->processaFiltros($reg['id_carrier'], $reg['id_reference'], $prod['id_product'], $this->context->shop->id)) {
                            continue 2;
                        }
                        
                        // Calcula cubagem
                        $cubagem = $prod['height'] * $prod['width'] * $prod['depth'];

                        // Verifica se o produto e frete gratis
                        $produto_frete_gratis = $this->verificaProdutoFreteGratis($cep_destino, $uf_destino, $prod['id_product']);

                        if ($produto_frete_gratis['frete_gratis'] == true) {

                            $existe_produto_frete_gratis = true;

                            // Se nao existir carrier frete gratis definido no pedido assume o do produto
                            if ($pedido_frete_gratis['carrier_frete_gratis'] == 0) {
                                $pedido_frete_gratis['carrier_frete_gratis'] = $produto_frete_gratis['carrier_frete_gratis'];
                            }
                        }

                        for ($qty = 0; $qty < $prod['quantity']; $qty++) {

                            $produtos[] = array(
                                'id'            => $prod['id_product'],
                                'altura'        => $prod['height'],
                                'largura'       => $prod['width'],
                                'comprimento'   => $prod['depth'],
                                'peso'          => $prod['weight'],
                                'cubagem'       => $cubagem,
                                'valor_produto' => $prod['price_wt'],
                                'frete_gratis'  => $produto_frete_gratis['frete_gratis']
                            );
                        }
                    }    
                }
                
                // Processa embalagens
                if (Configuration::get('FKCARRIER_EMBALAGEM') == '0') {
                    $embalagens = $this->processaEmbalagemIndividual($reg['id_carrier'], $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
                }else {
                    if (Configuration::get('FKCARRIER_EMBALAGEM') == '1') {
                        $embalagens = $this->processaEmbalagemPadrao($reg['id_carrier'], $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
                    }else {
                        $embalagens = $this->processaEmbalagemVirtual($reg['id_carrier'], $produtos, $dados_carrier['servico_correios'], $uf_origem, $uf_destino);
                    }
                }

                // Ignora carrier se nao existirem embalagens (ou seja as dimensoes estao fora do permitido
                if (!$embalagens) {
                    continue;
                }

                $fkcarrier = array(
                    'cep_origem'                        => trim(preg_replace("/[^0-9]/", "", Configuration::get('FKCARRIER_MEU_CEP'))),
                    'uf_origem'                         => $uf_origem,
                    'cep_destino'                       => $cep_destino,
                    'uf_destino'                        => $uf_destino,
                    'tempo_preparacao'                  => Configuration::get('FKCARRIER_TEMPO_PREPARACAO'),
                    'servico_correios'                  => $dados_carrier['servico_correios'],
                    'cod_servico'                       => $dados_carrier['cod_servico'],
                    'cod_administrativo'                => $dados_carrier['cod_administrativo'],
                    'senha'                             => $dados_carrier['senha'],
                    'prazo_entrega_especifico'          => $dados_carrier['prazo_entrega_especifico'],
                    'tipo_preco'                        => $dados_carrier['tipo_preco'],
                    'tabela_preco'                      => $dados_carrier['tabela_preco'],
                    'valor_adicional_excedente_kilo'    => $dados_carrier['valor_adicional_excedente_kilo'],
                    'percentual_seguro'                 => $dados_carrier['percentual_seguro'],
                    'valor_pedagio'                     => $dados_carrier['valor_pedagio'],
                    'fator_cubagem'                     => $dados_carrier['fator_cubagem'],
                    'valor_pedido'                      => $valor_pedido,
                    'pedido_frete_gratis'               => $pedido_frete_gratis['frete_gratis'],
                    'produto_frete_gratis'              => $existe_produto_frete_gratis,
                    'mostrar_todos_carrier'             => ((Configuration::get('FKCARRIER_FRETE_GRATIS_TRANSP') == 'on') ? true : false),
                    'carrier_atual'                     => $reg['id_carrier'],
                    'carrier_frete_gratis'              => $pedido_frete_gratis['carrier_frete_gratis'],
                    'produtos'                          => $produtos,
                    'embalagens'                        => $embalagens
                );
                
                // Ignora Carrier se o Pedido for Frete Gratis e configurado para mostrar somente o Carrier de Frete Gratis
                if ($fkcarrier['pedido_frete_gratis'] == true And $fkcarrier['mostrar_todos_carrier'] == false And $fkcarrier['carrier_atual'] != $fkcarrier['carrier_frete_gratis']) {
                    continue;
                }

                // Ignora Carrier se existe Produto com Frete Gratis e configurado para mostrar somente o Carrier de Frete Gratis
                if ($fkcarrier['produto_frete_gratis'] == true And $fkcarrier['mostrar_todos_carrier'] == false And $fkcarrier['carrier_atual'] != $fkcarrier['carrier_frete_gratis']) {
                    continue;
                }

                if ($fkcarrier['servico_correios'] == true) {
                    // Calcula valor do frete dos Correios
                    if (Configuration::get('FKCARRIER_OFFLINE') == 'on') {
                        $valor_frete = $this->processaCorreiosOffline($fkcarrier);
                    }else {
                        $valor_frete = $this->processaCorreiosOnline($fkcarrier);
                    }
                }else {
                    // Calcula valor do frete das transportadoras
                    $valor_frete = $this->processaTransp($fkcarrier);
                }

                // Ignora Carrier se valor do frete for false
                if ($valor_frete === false) {
                    continue;
                }
                
                // Path do logotipo
                $path_logo = Tools::getShopDomainSsl(true, true)._PS_IMG_.'s/'.$reg['id_carrier'].'.jpg';

                if (!file_exists(_PS_IMG_DIR_.'s/'.$reg['id_carrier'].'.jpg')) {
                    $path_logo = '';
                }

                $fkcarrier_msg = $this->l('Frete calculado');

                // Formata prazo de entrega
                if (is_numeric($this->_prazo_entrega[$reg['id_carrier']])) {
                    if ($this->_prazo_entrega[$reg['id_carrier']] == 0) {
                        $prazo_entrega = $this->l(' no mesmo dia');
                    }else {
                        if ($this->_prazo_entrega[$reg['id_carrier']] > 1) {
                            $prazo_entrega = $this->_prazo_entrega[$reg['id_carrier']].$this->l(' dias úteis');
                        }else {
                            $prazo_entrega = $this->_prazo_entrega[$reg['id_carrier']].$this->l(' dia útil');
                        }
                    }
                }else {
                    $prazo_entrega = $this->_prazo_entrega[$reg['id_carrier']];
                }

                $fkcarrier_frete[] = array(
                    'url_imagem'    => $path_logo,
                    'nome_carrier'  => $reg['nome_carrier'],
                    'prazo_entrega' => $prazo_entrega,
                    'valor_frete'   => $valor_frete
                );
            }
            
            // Grava cookie do CEP
            $this->context->cookie->fkcarrier_cep = $cep_destino;
            $this->smarty->assign(array('fkcarrier_cep' => $cep_destino));

            // Classifica array por valor do frete
            usort($fkcarrier_frete, array($this, 'ordenaValorFrete'));

            $this->context->smarty->assign(array('fkcarrier_frete' => $fkcarrier_frete));
            
            if ($origem == 'carrinho') {
                // Recarrega a pagina para atualizar o valor do frete do carrinho se o cliente não estiver logado
                if (Tools::isSubmit('submitCarrinho') And !$this->context->customer->isLogged()){
                    if (Configuration::get('PS_REWRITING_SETTINGS') == 0) {
                        $atualPage = $_SERVER['REQUEST_URI'];
                    }else {
                        $atualPage = Tools::getShopDomainSsl(true,true).$_SERVER['REQUEST_URI'];
                    }

                    Tools::redirect($atualPage);
                }
            }    
        }
        
        // Grava foco no smarty
        if ($this->context->cookie->fkcarrier_foco) {
            $this->smarty->assign(array('fkcarrier_foco' => true));
            $this->context->cookie->fkcarrier_foco = '';   
        }
        
        // Grava mensagem no Smarty
        if ($processar and count($fkcarrier_frete) == 0) {
            $fkcarrier_msg = $this->l('Não foi possível selecionar transportadora para a localidade. Favor entrar em contato com o Atendimento ao Cliente.');
            $this->context->smarty->assign(array('fkcarrier_msg' => $fkcarrier_msg));
        }else {
            $this->context->smarty->assign(array('fkcarrier_msg' => $fkcarrier_msg));
        }
        
        return true;
        
    }
    
    private function processaFiltros($id_carrier, $id_carrier_reference, $id_produto, $id_shop) {
        
        $context = Context::getContext();
        
        // Filtro de produto por transportadora
		$sql = "SELECT "._DB_PREFIX_."product_carrier.id_carrier_reference
				FROM  "._DB_PREFIX_."product_carrier
  					INNER JOIN  "._DB_PREFIX_."carrier
    					ON  "._DB_PREFIX_."product_carrier.id_carrier_reference =  "._DB_PREFIX_."carrier.id_reference
				WHERE   "._DB_PREFIX_."product_carrier.id_product = ".(int)$id_produto." AND
						"._DB_PREFIX_."carrier.deleted = 0 AND
						"._DB_PREFIX_."product_carrier.id_shop = ".(int)$id_shop;

        $dados = Db::getInstance()->executeS($sql);
        
        if ($dados) {
            
            $carrier_valido = false;
            
            foreach ($dados as $reg) {
                
                if ($reg['id_carrier_reference'] == $id_carrier_reference) {
                    $carrier_valido = true;
                    break;    
                }
            }
            
            if (!$carrier_valido) {
                return false;
            }
        }
        
        // Filtro de transportadora por grupo de clientes
        if ($context->customer->logged) {
            $sql = "SELECT *
                    FROM "._DB_PREFIX_."customer_group
                        INNER JOIN "._DB_PREFIX_."carrier_group
                            ON "._DB_PREFIX_."customer_group.id_group = "._DB_PREFIX_."carrier_group.id_group
                    WHERE "._DB_PREFIX_."customer_group.id_customer = ".(int)$context->customer->id." AND 
                          "._DB_PREFIX_."carrier_group.id_carrier = ".(int)$id_carrier;    
        }else {
            $grupo = Configuration::get('PS_UNIDENTIFIED_GROUP');
             
            $sql = "SELECT *
                    FROM "._DB_PREFIX_."carrier_group
                    WHERE "._DB_PREFIX_."carrier_group.id_carrier = ".(int)$id_carrier." AND 
                          "._DB_PREFIX_."carrier_group.id_group = ".(int)$grupo;
        }
                          
        $dados = Db::getInstance()->executeS($sql);
        
        if (!$dados) {
            return false;
        }
        
        // Filtro de transportadora por dimensoes e peso
        $produto = new product($id_produto);
        
        if ($produto->width > 0 or $produto->height > 0 or $produto->depth > 0 or $produto->weight > 0) {
        
            $carrier = new Carrier($id_carrier);
            
            if (($carrier->max_width > 0 and $carrier->max_width < $produto->width) or
                ($carrier->max_height > 0 and $carrier->max_height < $produto->height) or
                ($carrier->max_depth > 0 and $carrier->max_depth < $produto->depth) or
                ($carrier->max_weight > 0 && $carrier->max_weight < $produto->weight)) {
                
                return false;    
            }    
        }
        
        return true;
    }

    private function verificaRegiaoAtendida($id_carrier, $cep_destino, $uf_destino) {

        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.regiao_uf,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.regiao_cep,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.prazo_entrega_especifico,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.tipo_preco,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.preco_1,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.preco_2,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.preco_3,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.valor_adicional_excedente_kilo,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.percentual_seguro,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.valor_pedagio,
                        '._DB_PREFIX_.'fkcarrier_regioes_precos.fator_cubagem
                FROM '._DB_PREFIX_.'fkcarrier_correios_transp
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_regioes_precos
                    ON '._DB_PREFIX_.'fkcarrier_correios_transp.id = '._DB_PREFIX_.'fkcarrier_regioes_precos.id_correios_transp
                WHERE   '._DB_PREFIX_.'fkcarrier_correios_transp.ativo = 1 AND
                        '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id.' AND
                        '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$id_carrier;

        $regiao = Db::getInstance()->executeS($sql);

        if (!$regiao) {
            return array('regiao_atendida' => false);
        }

        foreach ($regiao as $reg) {

            // Verifica se a UF esta contida no intervalo de UF
            if ($reg['regiao_uf']) {

                if (strpos($reg['regiao_uf'], $uf_destino) === false) {
                }else {
                    return $this->recuperaDadosRegioes($reg);
                }
            }

            // Verifica se o CEP esta contido no intervalo de CEPs
            $cepArray = explode('/', $reg['regiao_cep']);

            foreach ($cepArray as $intervalo_cep) {
                if ($intervalo_cep == '') {
                    continue;
                }

                if ($cep_destino >= substr($intervalo_cep, 0, 8) And $cep_destino <= substr($intervalo_cep, 9, 8)) {
                    return $this->recuperaDadosRegioes($reg);
                }
            }
        }

        return array('regiao_atendida' => false);
    }

    private function recuperaDadosRegioes($reg) {

        $retorno = array(
            'regiao_atendida'                   => true,
            'servico_correios'                  => '',
            'cod_servico'                       => '',
            'cod_administrativo'                => '',
            'senha'                             => '',
            'valor_declarado_max'               => '',
            'prazo_entrega_especifico'          => '',
            'tipo_preco'                        => '',


            'tabela_preco'                      => '',
            'valor_adicional_excedente_kilo'    => '',
            'percentual_seguro'                 => '',
            'valor_pedagio'                     => '',
            'fator_cubagem'                     => ''
        );

        if ($reg['id_correios'] > 0) {

            $retorno['servico_correios'] = true;

            $sql = 'SELECT `cod_servico`, `cod_administrativo`, `senha`, `valor_declarado_max`
                    FROM `'._DB_PREFIX_.'fkcarrier_especificacoes_correios`
                    WHERE `id` = '.(int)$reg['id_correios'];

            $especif_correios = Db::getInstance()->getRow($sql);

            if ($especif_correios) {
                $retorno['cod_servico'] = $especif_correios['cod_servico'];
                $retorno['cod_administrativo'] = $especif_correios['cod_administrativo'];
                $retorno['senha'] = $especif_correios['senha'];
                $retorno['valor_declarado_max'] = $especif_correios['valor_declarado_max'];
            }
        }else {
            $retorno['servico_correios'] = false;
            $retorno['prazo_entrega_especifico'] = $reg['prazo_entrega_especifico'];
            $retorno['tipo_preco'] = $reg['tipo_preco'];

            switch ($reg['tipo_preco']) {

                case '1':
                    $retorno['tabela_preco'] = $reg['preco_1'];
                    break;

                case '2':
                    $retorno['tabela_preco'] = $reg['preco_2'];
                    break;

                case '3':
                    $retorno['tabela_preco'] = $reg['preco_3'];
                    break;
            }

            $retorno['valor_adicional_excedente_kilo'] = $reg['valor_adicional_excedente_kilo'];
            $retorno['percentual_seguro'] = $reg['percentual_seguro'];
            $retorno['valor_pedagio'] = $reg['valor_pedagio'];
            $retorno['fator_cubagem'] = $reg['fator_cubagem'];
        }

        return $retorno;
    }

    private function verificaPedidoFreteGratis($cep_destino, $uf_destino, $valor_pedido) {

        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier,
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.regiao_uf,
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.regiao_cep
                FROM '._DB_PREFIX_.'fkcarrier_frete_gratis
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_correios_transp
                    ON '._DB_PREFIX_.'fkcarrier_frete_gratis.id_correios_transp = '._DB_PREFIX_.'fkcarrier_correios_transp.id
                WHERE   '._DB_PREFIX_.'fkcarrier_frete_gratis.ativo = 1 AND
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.valor_pedido > 0 AND
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.valor_pedido <= '.$valor_pedido.' AND
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.id_shop = '.$this->context->shop->id;

        $frete_gratis = Db::getInstance()->executeS($sql);

        if ($frete_gratis) {

            foreach ($frete_gratis as $reg) {

                // Verifica se a UF esta contida no intervalo de UF
                if ($reg['regiao_uf']) {

                    if (strpos($reg['regiao_uf'], $uf_destino) === false) {
                    }else {
                        return array('frete_gratis' => true, 'carrier_frete_gratis' => $reg['id_carrier']);
                    }
                }

                // Verifica se o CEP esta contido no intervalo de CEPs
                $cepArray = explode('/', $reg['regiao_cep']);

                foreach ($cepArray as $intervalo_cep) {

                    if ($intervalo_cep == '') {
                        continue;
                    }

                    if ($cep_destino >= substr($intervalo_cep, 0, 8) And $cep_destino <= substr($intervalo_cep, 9, 8)) {
                        return array('frete_gratis' => true, 'carrier_frete_gratis' => $reg['id_carrier']);
                    }
                }
            }
        }

        return array('frete_gratis' => false, 'carrier_frete_gratis' => '0');
    }

    private function verificaProdutoFreteGratis($cep_destino, $uf_destino, $id_produto) {

        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier,
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.regiao_uf,
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.regiao_cep,
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.id_produtos
                FROM '._DB_PREFIX_.'fkcarrier_frete_gratis
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_correios_transp
                    ON '._DB_PREFIX_.'fkcarrier_frete_gratis.id_correios_transp = '._DB_PREFIX_.'fkcarrier_correios_transp.id
                WHERE   '._DB_PREFIX_.'fkcarrier_frete_gratis.ativo = 1 AND
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.id_produtos IS NOT NULL AND
                        '._DB_PREFIX_.'fkcarrier_frete_gratis.id_shop = '.$this->context->shop->id;

        $frete_gratis = Db::getInstance()->executeS($sql);

        if ($frete_gratis) {

            foreach ($frete_gratis as $reg) {

                // Verifica se o produto esta contido no intervalo de produtos
                $produtos_frete_gratis = '/'.$reg['id_produtos'];

                if (strpos($produtos_frete_gratis, '/'.$id_produto.'/') === false) {
                }else {
                    // Verifica se a UF esta contida no intervalo de UF
                    if ($reg['regiao_uf']) {

                        if (strpos($reg['regiao_uf'], $uf_destino) === false) {
                        }else {
                            return array('frete_gratis' => true, 'carrier_frete_gratis' => $reg['id_carrier']);
                        }
                    }

                    // Verifica se o CEP esta contido no intervalo de CEPs
                    $cepArray = explode('/', $reg['regiao_cep']);

                    foreach ($cepArray as $intervalo_cep) {

                        if ($intervalo_cep == '') {
                            continue;
                        }
                        if ($cep_destino >= substr($intervalo_cep, 0, 8) And $cep_destino <= substr($intervalo_cep, 9, 8)) {
                            return array('frete_gratis' => true, 'carrier_frete_gratis' => $reg['id_carrier']);
                            break 2;
                        }
                    }
                }
            }
        }

        return array('frete_gratis' => false, 'carrier_frete_gratis' => '0');
    }

    private function processaEmbalagemIndividual($id_carrier, $produtos, $servico_correios, $uf_origem, $uf_destino) {

        $embalagens = array();

        // Recupera as dimensoes permitidas
        $dimensoes = $this->recuperaDimensoes($id_carrier, $uf_origem, $uf_destino);

        foreach ($produtos as $prod) {

            // Retorna vazio (ignora carrier) se as dimensoes e peso estiverem fora do permitido
            if ($servico_correios) {
                if ($prod['altura'] > $dimensoes['altura_max'] Or $prod['largura'] > $dimensoes['largura_max'] Or $prod['comprimento'] > $dimensoes['comprimento_max'] Or
                    $prod['peso']  > $dimensoes['peso_maximo'] Or
                    $prod['altura'] + $prod['largura'] + $prod['comprimento'] > $dimensoes['somatoria_dimensoes_max']) {

                    return array();
                }
            }

            $embalagens[] = array(
                'altura'            => ($prod['altura'] < $dimensoes['altura_min'] ? $dimensoes['altura_min'] : $prod['altura']),
                'largura'           => ($prod['largura'] < $dimensoes['largura_min'] ? $dimensoes['largura_min'] : $prod['largura']),
                'comprimento'       => ($prod['comprimento'] < $dimensoes['comprimento_min'] ? $dimensoes['comprimento_min'] : $prod['comprimento']),
                'peso_embalagem'    => '0',
                'custo_embalagem'   => '0',
                'cubagem'           => $prod['cubagem'],
                'peso_produtos'     => $prod['peso'],
                'valor_produtos'    => $prod['valor_produto'],
                'frete_gratis'      => $prod['frete_gratis']
            );
        }

        return $embalagens;
    }

    private function processaEmbalagemPadrao($id_carrier, $produtos, $servico_correios, $uf_origem, $uf_destino) {

        // Recupera as dimensoes permitidas
        $dimensoes = $this->recuperaDimensoes($id_carrier, $uf_origem, $uf_destino);

        // Recupera as embalagens
        if ($servico_correios) {
            // Seleciona as embalagens validas para os Correios
            $sql = 'SELECT *
                    FROM `'._DB_PREFIX_.'fkcarrier_embalagens`
                    WHERE   `ativo` = 1 AND `id_shop` = '.$this->context->shop->id.' AND
                            `comprimento` >= '.$dimensoes['comprimento_min'].' AND `comprimento` <= '.$dimensoes['comprimento_max'].' AND
                            `altura` >= '.$dimensoes['altura_min'].' AND `altura` <= '.$dimensoes['altura_max'].' AND
                            `largura` >= '.$dimensoes['largura_min'].' AND `largura` <= '.$dimensoes['largura_max'].' AND
                            `comprimento` + `altura` + `largura` <= '.$dimensoes['somatoria_dimensoes_max'].'
                    ORDER BY `cubagem`';
        }else {
            // Seleciona as embalagens para transportadoras
            $sql = 'SELECT *
                    FROM `'._DB_PREFIX_.'fkcarrier_embalagens`
                    WHERE `ativo` = 1 AND `id_shop` = '.$this->context->shop->id. '
                    ORDER BY `cubagem`';
        }

        $caixas = Db::getInstance()->executeS($sql);

        // Classifica produtos por cubagem
        usort($produtos, array($this, 'ordenaCubagem'));

        // Inicializa variaveis das embalagens
        $embalagens = array();

        $altura_embalagem = 0;
        $largura_embalagem = 0;
        $comprimento_embalagem = 0;
        $peso_embalagem = 0;
        $custo_embalagem = 0;
        $cubagem_embalagem = 0;

        $peso_acumulado_produtos = 0;
        $valor_acumulado_produtos = 0;
        $cubagem_acumulada_produtos = 0;

        // Adiciona os produtos em suas embalagens
        foreach ($produtos as $prod) {

            // Se peso do produto for igual a zero assume valor minimo
            if ($prod['peso'] > 0) {
                $peso_produto = $prod['peso'];
            }else {
                $peso_produto = 0.01;
            }

            // Retorna vazio (ignora carrier) se as dimensoes e peso estiverem fora do permitido
            $embalagem_selecionada = $this->selecionaEmbalagem($caixas, $prod['cubagem']);

            if ($servico_correios) {
                
                if ($prod['altura'] > $dimensoes['altura_max'] Or 
                    $prod['largura'] > $dimensoes['largura_max'] Or 
                    $prod['comprimento'] > $dimensoes['comprimento_max'] Or
                    $peso_produto  > $dimensoes['peso_maximo'] Or
                    $prod['altura'] + $prod['largura'] + $prod['comprimento'] > $dimensoes['somatoria_dimensoes_max']) {

                    return array();
                }
                    
                if ($embalagem_selecionada) {
                    if (($peso_produto + $embalagem_selecionada['peso']) > $dimensoes['peso_maximo']) {
                        return array();
                    }
                }
            }

            // Grava embalagem se produto for frete gratis
            if ($prod['frete_gratis'] == true) {

                // Grava dados considerando as dimensoes minimas (somente Correios, pois se for transportadoras os valores minimos estarao zerados)
                $embalagens[] = array(
                    'altura'            => ($prod['altura'] < $dimensoes['altura_min'] ? $dimensoes['altura_min'] : $prod['altura']),
                    'largura'           => ($prod['largura'] < $dimensoes['largura_min'] ? $dimensoes['largura_min'] : $prod['largura']),
                    'comprimento'       => ($prod['comprimento'] < $dimensoes['comprimento_min'] ? $dimensoes['comprimento_min'] : $prod['comprimento']),
                    'peso_embalagem'    => 0,
                    'custo_embalagem'   => 0,
                    'cubagem'           => $prod['cubagem'],
                    'peso_produtos'     => $peso_produto,
                    'valor_produtos'    => $prod['valor_produto'],
                    'frete_gratis'      => true
                );

                continue;
            }

            // Grava embalagem se nao existe embalagem para o produto
            if (!$embalagem_selecionada) {
                $embalagens[] = array(
                    'altura'            => ($prod['altura'] < $dimensoes['altura_min'] ? $dimensoes['altura_min'] : $prod['altura']),
                    'largura'           => ($prod['largura'] < $dimensoes['largura_min'] ? $dimensoes['largura_min'] : $prod['largura']),
                    'comprimento'       => ($prod['comprimento'] < $dimensoes['comprimento_min'] ? $dimensoes['comprimento_min'] : $prod['comprimento']),
                    'peso_embalagem'    => 0,
                    'custo_embalagem'   => 0,
                    'cubagem'           => $prod['cubagem'],
                    'peso_produtos'     => $peso_produto,
                    'valor_produtos'    => $prod['valor_produto'],
                    'frete_gratis'      => false
                );

                continue;
            }

            // Verifica se existe caixa para a cubagem acumulada somada a cubagem do produto atual
            $embalagem_selecionada = $this->selecionaEmbalagem($caixas, ($prod['cubagem'] + $cubagem_acumulada_produtos));

            // Se embalagem nao localizada
            if (!$embalagem_selecionada Or (($peso_acumulado_produtos + $peso_produto + $peso_embalagem) > $dimensoes['peso_maximo'] And $dimensoes['peso_maximo'] > 0)) {

                // Grava dados acumulados
                $embalagens[] = array(
                    'altura'            => $altura_embalagem,
                    'largura'           => $largura_embalagem,
                    'comprimento'       => $comprimento_embalagem,
                    'peso_embalagem'    => $peso_embalagem,
                    'custo_embalagem'   => $custo_embalagem,
                    'cubagem'           => $cubagem_embalagem,
                    'peso_produtos'     => $peso_acumulado_produtos,
                    'valor_produtos'    => $valor_acumulado_produtos,
                    'frete_gratis'      => false
                );

                // Seleciona embalagem para o produto
                $embalagem_selecionada = $this->selecionaEmbalagem($caixas, $prod['cubagem']);

                // Inicializa variaveis
                $peso_acumulado_produtos = 0;
                $valor_acumulado_produtos = 0;
                $cubagem_acumulada_produtos = 0;
            }

            // Guarda os campos da embalagem
            $altura_embalagem = $embalagem_selecionada['altura'];
            $largura_embalagem = $embalagem_selecionada['largura'];
            $comprimento_embalagem = $embalagem_selecionada['comprimento'];
            $peso_embalagem = $embalagem_selecionada['peso'];
            $custo_embalagem = $embalagem_selecionada['custo'];
            $cubagem_embalagem = $embalagem_selecionada['cubagem'];

            // Acumula valores
            $peso_acumulado_produtos += $peso_produto;
            $valor_acumulado_produtos += $prod['valor_produto'];
            $cubagem_acumulada_produtos += $prod['cubagem'];
        }

        // Grava a ultima embalagem
        if ($peso_acumulado_produtos > 0) {

            $embalagens[] = array(
                'altura'            => $altura_embalagem,
                'largura'           => $largura_embalagem,
                'comprimento'       => $comprimento_embalagem,
                'peso_embalagem'    => $peso_embalagem,
                'custo_embalagem'   => $custo_embalagem,
                'cubagem'           => $cubagem_embalagem,
                'peso_produtos'     => $peso_acumulado_produtos,
                'valor_produtos'    => $valor_acumulado_produtos,
                'frete_gratis'      => false
            );
        }

        return $embalagens;
    }
    
    private function processaEmbalagemVirtual($id_carrier, $produtos, $servico_correios, $uf_origem, $uf_destino) {
        
        // Recupera as dimensoes permitidas
        $dimensoes = $this->recuperaDimensoes($id_carrier, $uf_origem, $uf_destino);
    
        // Classifica produtos por cubagem
        usort($produtos, array($this, 'ordenaCubagem'));
    
        // Inicializa variaveis
        $embalagens = array();
        $altura = $dimensoes['altura_min'];
        $largura = $dimensoes['largura_min'];
        $comprimento = $dimensoes['comprimento_min'];
        $valor_acumulado = 0;
        $peso_acumulado = 0;
        $volume_acumulado = 0;
    
        // Adiciona os produtos em embalagens virtuais
        foreach ($produtos as $prod) {
            
            // Se peso do produto for igual a zero assume valor minimo
            if ($prod['peso'] > 0) {
                $peso_produto = $prod['peso'];
            }else {
                $peso_produto = 0.01;
            }
            
            // Retorna vazio (ignora carrier) se as dimensoes e peso estiverem fora do permitido
            if ($servico_correios) {
                if ($prod['altura'] > $dimensoes['altura_max'] Or 
                    $prod['largura'] > $dimensoes['largura_max'] Or 
                    $prod['comprimento'] > $dimensoes['comprimento_max'] Or
                    $peso_produto  > $dimensoes['peso_maximo'] Or
                    $prod['altura'] + $prod['largura'] + $prod['comprimento'] > $dimensoes['somatoria_dimensoes_max']) {

                    return array();
                }
            }
            
            // Grava embalagem virtual se produto for frete gratis
            if ($prod['frete_gratis'] == true) {

                // Grava dados considerando as dimensoes minimas
                $embalagens[] = array(
                    'altura'            => ($prod['altura'] < $dimensoes['altura_min'] ? $dimensoes['altura_min'] : $prod['altura']),
                    'largura'           => ($prod['largura'] < $dimensoes['largura_min'] ? $dimensoes['largura_min'] : $prod['largura']),
                    'comprimento'       => ($prod['comprimento'] < $dimensoes['comprimento_min'] ? $dimensoes['comprimento_min'] : $prod['comprimento']),
                    'peso_embalagem'    => 0,
                    'custo_embalagem'   => 0,
                    'cubagem'           => $prod['cubagem'],
                    'peso_produtos'     => $peso_produto,
                    'valor_produtos'    => $prod['valor_produto'],
                    'frete_gratis'      => true
                );

                continue;
            }
            
            // Grava embalagem virtual ou acumula valores
            if ($servico_correios) {
                if (($prod['cubagem'] + $volume_acumulado) > $dimensoes['volume_max'] or
                    ($peso_produto + $peso_acumulado) > $dimensoes['peso_maximo']) {
                    
                    // Grava    
                    $embalagens[] = array(
                        'altura'            => $altura,
                        'largura'           => $largura,
                        'comprimento'       => $comprimento,
                        'peso_embalagem'    => 0,
                        'custo_embalagem'   => 0,
                        'cubagem'           => $volume_acumulado,
                        'peso_produtos'     => $peso_acumulado,
                        'valor_produtos'    => $valor_acumulado,
                        'frete_gratis'      => false
                    );
                    
                    // reinicializa variaveis
                    $altura = $dimensoes['altura_min'];
                    $largura = $dimensoes['largura_min'];
                    $comprimento = $dimensoes['comprimento_min'];
                    $valor_acumulado = 0;
                    $peso_acumulado = 0;
                    $volume_acumulado = 0;
                }
            }
            
            // Acumula os dados
            $valor_acumulado += $prod['valor_produto'];
            $peso_acumulado += $peso_produto;
            $volume_acumulado += $prod['cubagem'];
            
            // Calcula volume virtual atual
            $volume_virtual = $altura * $largura * $comprimento;    
                
            if ($volume_acumulado <= $volume_virtual) {
                continue;
            }
            
            while ($volume_virtual < $volume_acumulado){
                
                if ($servico_correios) {
                    if ($altura < $dimensoes['altura_max']) {
                        // Soma 1 em altura
                        $altura++;
                        
                        // Calcula volume virtual atual
                        $volume_virtual = $altura * $largura * $comprimento;
                    }
                }else {
                    // Soma 1 em altura
                    $altura++;
                    
                    // Calcula volume virtual atual
                    $volume_virtual = $altura * $largura * $comprimento;    
                }
                
                if ($servico_correios) {
                    if ($largura < $dimensoes['largura_max'] and $volume_virtual < $volume_acumulado) {
                        // Soma 1 em largura
                        $largura++;
                        
                        // Calcula volume virtual atual
                        $volume_virtual = $altura * $largura * $comprimento;
                    }
                }else {
                    if ($volume_virtual < $volume_acumulado) {
                        // Soma 1 em largura
                        $largura++;
                        
                        // Calcula volume virtual atual
                        $volume_virtual = $altura * $largura * $comprimento;
                    }    
                }
                
                if ($servico_correios) {
                    if ($comprimento < $dimensoes['comprimento_max'] and $volume_virtual < $volume_acumulado) {
                        // Soma 1 em comprimento
                        $comprimento++;
                        
                        // Calcula volume virtual atual
                        $volume_virtual = $altura * $largura * $comprimento;
                    }
                }else {
                    if ($volume_virtual < $volume_acumulado) {
                        // Soma 1 em comprimento
                        $comprimento++;
                        
                        // Calcula volume virtual atual
                        $volume_virtual = $altura * $largura * $comprimento;
                    }
                }
            }    
        }
        
        // Grava a ultima embalagem virtual
        if ($peso_acumulado > 0) {
            $embalagens[] = array(
                'altura'            => $altura,
                'largura'           => $largura,
                'comprimento'       => $comprimento,
                'peso_embalagem'    => 0,
                'custo_embalagem'   => 0,
                'cubagem'           => $volume_acumulado,
                'peso_produtos'     => $peso_acumulado,
                'valor_produtos'    => $valor_acumulado,
                'frete_gratis'      => false
            );

        }
        
        return $embalagens;
    }

    private function recuperaDimensoes($id_carrier, $uf_origem, $uf_destino) {

        // Recupera as dimensoes mínimas/maximas e pesos permitidos para os Correios
        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_especificacoes_correios.comprimento_min,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.comprimento_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.largura_min,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.largura_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.altura_min,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.altura_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.somatoria_dimensoes_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.volume_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.peso_estadual_max,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.peso_nacional_max
                FROM '._DB_PREFIX_.'fkcarrier_correios_transp
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_especificacoes_correios
                    ON '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios = '._DB_PREFIX_.'fkcarrier_especificacoes_correios.id
                WHERE   '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id.' AND
                        '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$id_carrier;

        $especif_correios = Db::getInstance()->getRow($sql);

        if ($especif_correios) {

            if ($uf_origem == $uf_destino) {
                $peso_maximo = $especif_correios['peso_estadual_max'];
            }else {
                $peso_maximo = $especif_correios['peso_nacional_max'];
            }

            return array(
                'comprimento_min'           => $especif_correios['comprimento_min'],
                'comprimento_max'           => $especif_correios['comprimento_max'],
                'largura_min'               => $especif_correios['largura_min'],
                'largura_max'               => $especif_correios['largura_max'],
                'altura_min'                => $especif_correios['altura_min'],
                'altura_max'                => $especif_correios['altura_max'],
                'somatoria_dimensoes_max'   => $especif_correios['somatoria_dimensoes_max'],
                'volume_max'                => $especif_correios['volume_max'],
                'peso_maximo'               => $peso_maximo
            );
        }

        return array(
            'comprimento_min'           => 0,
            'comprimento_max'           => 0,
            'largura_min'               => 0,
            'largura_max'               => 0,
            'altura_min'                => 0,
            'altura_max'                => 0,
            'somatoria_dimensoes_max'   => 0,
            'volume_max'                => 0,
            'peso_maximo'               => 0
        );
    }

    private function selecionaEmbalagem($caixas, $cubagem) {

        foreach ($caixas as $caixa) {

            if ($cubagem <= $caixa['cubagem']) {

                return array(
                    'altura'        => $caixa['altura'],
                    'largura'       => $caixa['largura'],
                    'comprimento'   => $caixa['comprimento'],
                    'peso'          => $caixa['peso'],
                    'custo'         => $caixa['custo'],
                    'cubagem'       => $caixa['cubagem']
                );
            }
        }

        return array();
    }

    private function montaHash($fkcarrier, $embalagem) {

        $hash = $this->context->shop->id.':'.
            $this->context->cart->id.':'.
            $fkcarrier['carrier_atual'].':'.
            $fkcarrier['cep_origem'].':'.
            $fkcarrier['cep_destino'].':'.
            Configuration::get('FKCARRIER_MAO_PROPRIA').':'.
            Configuration::get('FKCARRIER_VALOR_DECLARADO').':'.
            Configuration::get('FKCARRIER_AVISO_RECEBIMENTO').':'.
            $embalagem['altura'].'/'.
            $embalagem['largura'].'/'.
            $embalagem['comprimento'].'/'.
            $embalagem['cubagem'].'/'.
            $embalagem['valor_produtos'].'/'.
            $embalagem['peso_produtos'];

        return md5($hash);
    }

    private function gravaCache($hash, $valor_frete, $prazo_entrega) {

        $dados = array(
            'hash' => $hash,
            'valor_frete' => $valor_frete,
            'prazo_entrega' => $prazo_entrega
        );

        Db::getInstance()->insert('fkcarrier_cache', $dados);

    }

    private function verificaCache($hash) {

        $cache = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_cache` WHERE `hash` = "'.$hash.'"');

        if ($cache) {
            return array('localizado' => true, 'valor_frete' => $cache['valor_frete'], 'prazo_entrega' => $cache['prazo_entrega']);
        }else {
            return array('localizado' => false, 'valor_frete' => '0', 'prazo_entrega' => '0');
        }
    }

    private function processaCorreiosOnline($fkcarrier) {

        // Inicializa variaveis
        $prazo_entrega = 0;
        $valor_frete = 0;
        $total_frete = 0;
        $valor_produtos = 0;

        // Recupera dados das especificacoes dos Correios
        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_especificacoes_correios.mao_propria_valor,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.aviso_recebimento_valor,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.valor_declarado_percentual,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.seguro_automatico_valor
                FROM '._DB_PREFIX_.'fkcarrier_correios_transp
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_especificacoes_correios
                    ON '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios = '._DB_PREFIX_.'fkcarrier_especificacoes_correios.id
                WHERE   '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id .' AND
                        '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$fkcarrier['carrier_atual'];

        $especif_correios = Db::getInstance()->getRow($sql);

        // Instancia a classe correios
        $ws = new FKcarrierCorreios();

        foreach ($fkcarrier['embalagens'] as $embalagem) {

            // Verifica se existe no cache
            $hash = $this->montaHash($fkcarrier, $embalagem);
            $cache = $this->verificaCache($hash);

            if ($cache['localizado'] == true) {
                // Recupera valores
                $prazo_entrega = $cache['prazo_entrega'];
                $valor_frete = $cache['valor_frete'];
            }else {
                // Consome web services dos Correios
                $ws->setEmpresa($fkcarrier['cod_administrativo']);
                $ws->setSenha($fkcarrier['senha']);
                $ws->setCodServico($fkcarrier['cod_servico']);
                $ws->setCepOrigem($fkcarrier['cep_origem']);
                $ws->setCepDestino($fkcarrier['cep_destino']);
                $ws->setPeso($embalagem['peso_produtos'] + $embalagem['peso_embalagem']);
                $ws->setFormato('1');
                $ws->setComprimento($embalagem['comprimento']);
                $ws->setAltura($embalagem['altura']);
                $ws->setLargura($embalagem['largura']);
                $ws->setDiametro('0');
                $ws->setCubagem($embalagem['cubagem']);
                $ws->setMaoPropria('N');
                $ws->setValorDeclarado('0');
                $ws->setAvisoRecebimento('N');

                // Se ocorreu erro na consulta aos Correios
                if (!$ws->Calcular()) {

                    // Ignora o Carrier se for um erro relativo a impossibilidade de entrega dos produtos pelos Correios
                    $trata_erro = $ws->trataErro();

                    if ($trata_erro['calculo_offline'] == 'false') {
                        return false;
                    }

                    // Retorna calculo offline
                    return  $this->processaCorreiosOffline($fkcarrier);

                }

                // Recupera valores
                $prazo_entrega = $ws->getPrazoEntrega();
                $valor_frete = $ws->getValor();

                // Grava cache
                $hash = $this->montaHash($fkcarrier, $embalagem);
                $this->gravaCache($hash, $ws->getValor(), $ws->getPrazoEntrega());
            }

            // Grava prazo de entrega
            $this->_prazo_entrega[$fkcarrier['carrier_atual']] = $prazo_entrega + $fkcarrier['tempo_preparacao'];

            // Verifica se o pedido e frete gratis e o Carrier é o Carrier Frete Gratis
            if ($fkcarrier['pedido_frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
                return 0;
            }

            // Verifica se o produto e frete gratis e o Carrier e o Carrier Frete Gratis
            if ($embalagem['frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
                continue;
            }

            // Verifica se os servicos adicionais devem ser calculados por embalagem
            if (Configuration::get('FKCARRIER_CALCULO_SERV_ADIC') == '0') {

                // Verifica Mao Propria
                if (Configuration::get('FKCARRIER_MAO_PROPRIA') == 'on') {
                    $valor_frete += $especif_correios['mao_propria_valor'];
                }

                // Verifica Valor Declarado
                if (Configuration::get('FKCARRIER_VALOR_DECLARADO') == 'on') {

                    if ($embalagem['valor_produtos'] > $especif_correios['seguro_automatico_valor']) {
                        $valor_frete += ($embalagem['valor_produtos'] - $especif_correios['seguro_automatico_valor']) * $especif_correios['valor_declarado_percentual'] / 100;
                    }
                }

                // Verifica Aviso de Recebimento
                if (Configuration::get('FKCARRIER_AVISO_RECEBIMENTO') == 'on') {
                    $valor_frete += $especif_correios['aviso_recebimento_valor'];
                }

            }

            // Acumula valor dos produtos para uso no Valor Declarado por pedido
            $valor_produtos += $embalagem['valor_produtos'];

            // Acumula valor do frete
            $total_frete += $valor_frete + $embalagem['custo_embalagem'];

        }

        // Verifica se os servicos adicionais devem ser calculados por pedido
        if (Configuration::get('FKCARRIER_CALCULO_SERV_ADIC') == '1' And $total_frete > 0) {

            // Verifica Mao Propria
            if (Configuration::get('FKCARRIER_MAO_PROPRIA') == 'on') {
                $total_frete += $especif_correios['mao_propria_valor'];
            }

            // Verifica Valor Declarado
            if (Configuration::get('FKCARRIER_VALOR_DECLARADO') == 'on') {

                if ($valor_produtos > $especif_correios['seguro_automatico_valor']) {
                    $total_frete += ($valor_produtos - $especif_correios['seguro_automatico_valor']) * $especif_correios['valor_declarado_percentual'] / 100;
                }
            }

            // Verifica Aviso de Recebimento
            if (Configuration::get('FKCARRIER_AVISO_RECEBIMENTO') == 'on') {
                $total_frete += $especif_correios['aviso_recebimento_valor'];
            }

        }

        // Verifica se o Custo de Envio deve ser adicionado ao valor do frete da transportadora
        if ($total_frete > 0) {

			if (Configuration::get('PS_SHIPPING_HANDLING') > 0) {
				$carrier = new Carrier($fkcarrier['carrier_atual']);

				if ($carrier->shipping_handling) {
					$total_frete += Configuration::get('PS_SHIPPING_HANDLING');
				}
			}

        }
        
        return $total_frete;

    }

    private function processaCorreiosOffline($fkcarrier) {

        // Inicializa a classe funcoes
        $funcoes = new FKcarrierFuncoes();

        // Verifica o destino da entrega
        $destino_entrega = '';

        // Verifica se o destino da entrega e minha cidade
        $minha_cidade = explode('/', Configuration::get('FKCARRIER_CEP_CIDADE'));

        foreach ($minha_cidade as $cep) {
            if ($cep == '') {
                continue;
            }

            if ($fkcarrier['cep_destino'] >= substr($cep, 0, 8) And $fkcarrier['cep_destino'] <= substr($cep, 9, 8)) {
                $destino_entrega = 'local';
                break;
            }
        }

        // Verifica se o destino da entrega e capital
        if ($destino_entrega == '') {

            $destino_entrega = 'interior';

            if ($funcoes->verificaUfCapital($fkcarrier['cep_destino'])) {
                $destino_entrega = 'capital';
            }

        }

        // Recupera e grava prazo de entrega
        $prazo_entrega = $this->retornaPrazoEntrega($fkcarrier['uf_destino'], true, $funcoes->verificaUfCapital($fkcarrier['cep_destino']));
        $this->_prazo_entrega[$fkcarrier['carrier_atual']] = $prazo_entrega + $fkcarrier['tempo_preparacao'];

        // Retorna se o pedido e frete gratis e o Carrier e o Carrier Frete Gratis
        if ($fkcarrier['pedido_frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
            return 0;
        }

        // Recupera dados das especificacoes dos Correios
        $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_especificacoes_correios.cubagem_max_isenta,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.cubagem_base_calculo,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.mao_propria_valor,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.aviso_recebimento_valor,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.valor_declarado_percentual,
                        '._DB_PREFIX_.'fkcarrier_especificacoes_correios.seguro_automatico_valor
                FROM '._DB_PREFIX_.'fkcarrier_correios_transp
                    INNER JOIN '._DB_PREFIX_.'fkcarrier_especificacoes_correios
                    ON '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios = '._DB_PREFIX_.'fkcarrier_especificacoes_correios.id
                WHERE   '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id .' AND
                        '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$fkcarrier['carrier_atual'];

        $especif_correios = Db::getInstance()->getRow($sql);

        // Inicializa variaveis
        $prazo_entrega = 0;
        $total_frete = 0;
        $valor_produtos = 0;

        // Calcula o frete
        foreach ($fkcarrier['embalagens'] as $embalagem) {

            // Verifica se deve considerar a cubagem
            $peso_produtos = $embalagem['peso_produtos'] + $embalagem['peso_embalagem'];

            if ($embalagem['cubagem'] > $especif_correios['cubagem_max_isenta']) {

                $peso_cubico = $embalagem['cubagem'] / $especif_correios['cubagem_base_calculo'];

                if ($peso_cubico > $peso_produtos) {
                    $peso_produtos = $peso_cubico;
                }
            }

            if ($destino_entrega == 'local') {

                $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_capital,
                                    '._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_interior
                            FROM '._DB_PREFIX_.'fkcarrier_tabelas_offline
                                INNER JOIN '._DB_PREFIX_.'fkcarrier_correios_transp
                                ON '._DB_PREFIX_.'fkcarrier_tabelas_offline.id_correios_transp = '._DB_PREFIX_.'fkcarrier_correios_transp.id
                            WHERE   '._DB_PREFIX_.'fkcarrier_tabelas_offline.minha_cidade = 1 AND
                                    '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id.' AND
                                    '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$fkcarrier['carrier_atual'];


            }else {
                $sql = 'SELECT  '._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_capital,
                                    '._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_interior
                            FROM '._DB_PREFIX_.'fkcarrier_tabelas_offline
                                INNER JOIN '._DB_PREFIX_.'fkcarrier_cadastro_cep
                                ON '._DB_PREFIX_.'fkcarrier_tabelas_offline.id_cadastro_cep = '._DB_PREFIX_.'fkcarrier_cadastro_cep.id
                                INNER JOIN '._DB_PREFIX_.'fkcarrier_correios_transp
                                ON '._DB_PREFIX_.'fkcarrier_tabelas_offline.id_correios_transp = '._DB_PREFIX_.'fkcarrier_correios_transp.id
                            WHERE   '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id.' AND
                                    '._DB_PREFIX_.'fkcarrier_correios_transp.id_carrier = '.(int)$fkcarrier['carrier_atual'].' AND
                                    '._DB_PREFIX_.'fkcarrier_cadastro_cep.estado = "'.$fkcarrier['uf_destino'].'"';
            }

            $tabelas_offline = Db::getInstance()->getRow($sql);

            // Ignora Carrier se tabela offline não localizada
            if (!$tabelas_offline){
                return false;
            }

            if ($destino_entrega == 'local' Or $destino_entrega == 'capital') {
                $tabela_preço = $tabelas_offline['tabela_capital'];
            }else {
                $tabela_preço = $tabelas_offline['tabela_interior'];
            }

            // Recupera o valor a ser cobrado
            $precos = explode('/', $tabela_preço);

            $valor_frete = 0;

            foreach ($precos as $itens_preco) {

                if ($itens_preco == '') {
                    continue;
                }

                $pos = strpos($itens_preco, ':');

                // Ignora o Carrier pois a tabela está configurada errada
                if ($pos === false) {
                    return false;
                }

                $peso_tabela = substr($itens_preco, 0, $pos);

                if ($peso_produtos <= $peso_tabela) {
                    $valor_frete = substr($itens_preco, $pos + 1);
                    break;
                }
            }

            // Ignora Carrier caso não tenho localizado o valor a ser cobrado
            if ($valor_frete == 0) {
                return false;
            }

            // Verifica se o produto e frete gratis e o Carrier e o Carrier Frete Gratis
            if ($embalagem['frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
                continue;
            }

            // Verifica se os servicos adicionais devem ser calculados por embalagem
            if (Configuration::get('FKCARRIER_CALCULO_SERV_ADIC') == '0') {

                // Verifica Mao Propria
                if (Configuration::get('FKCARRIER_MAO_PROPRIA') == 'on') {
                    $valor_frete += $especif_correios['mao_propria_valor'];
                }

                // Verifica Valor Declarado
                if (Configuration::get('FKCARRIER_VALOR_DECLARADO') == 'on') {

                    if ($embalagem['valor_produtos'] > $especif_correios['seguro_automatico_valor']) {
                        $valor_frete += ($embalagem['valor_produtos'] - $especif_correios['seguro_automatico_valor']) * $especif_correios['valor_declarado_percentual'] / 100;
                    }
                }

                // Verifica Aviso de Recebimento
                if (Configuration::get('FKCARRIER_AVISO_RECEBIMENTO') == 'on') {
                    $valor_frete += $especif_correios['aviso_recebimento_valor'];
                }

            }

            // Acumula valor dos produtos para uso no Valor Declarado por pedido
            $valor_produtos += $embalagem['valor_produtos'];

            // Acumula valor do frete
            $total_frete += $valor_frete + $embalagem['custo_embalagem'];

        }

        // Verifica se os servicos adicionais devem ser calculados por pedido
        if (Configuration::get('FKCARRIER_CALCULO_SERV_ADIC') == '1' And $total_frete > 0) {

            // Verifica Mao Propria
            if (Configuration::get('FKCARRIER_MAO_PROPRIA') == 'on') {
                $total_frete += $especif_correios['mao_propria_valor'];
            }

            // Verifica Valor Declarado
            if (Configuration::get('FKCARRIER_VALOR_DECLARADO') == 'on') {

                if ($valor_produtos > $especif_correios['seguro_automatico_valor']) {
                    $total_frete += ($valor_produtos - $especif_correios['seguro_automatico_valor']) * $especif_correios['valor_declarado_percentual'] / 100;
                }
            }

            // Verifica Aviso de Recebimento
            if (Configuration::get('FKCARRIER_AVISO_RECEBIMENTO') == 'on') {
                $total_frete += $especif_correios['aviso_recebimento_valor'];
            }
        }

        // Verifica se o Custo de Envio deve ser adicionado ao valor do frete da transportadora
        if ($total_frete > 0) {

			if (Configuration::get('PS_SHIPPING_HANDLING') > 0) {
				$carrier = new Carrier($fkcarrier['carrier_atual']);

				if ($carrier->shipping_handling) {
					$total_frete += Configuration::get('PS_SHIPPING_HANDLING');
				}
			}
        }
        
        return $total_frete;

    }

    private function processaTransp($fkcarrier) {

        // Inicializa a classe funcoes
        $funcoes = new FKcarrierFuncoes();

        // Grava prazo de entrega
        if ($fkcarrier['prazo_entrega_especifico'] == '') {
            $prazo_entrega = $this->retornaPrazoEntrega($fkcarrier['uf_destino'], false, $funcoes->verificaUfCapital($fkcarrier['cep_destino']));
            $this->_prazo_entrega[$fkcarrier['carrier_atual']] = $prazo_entrega + $fkcarrier['tempo_preparacao'];
        }else {
            $this->_prazo_entrega[$fkcarrier['carrier_atual']] = $fkcarrier['prazo_entrega_especifico'];
        }

        // Retorna se o pedido é frete gratis e o Carrier é o Carrier Frete Gratis
        if ($fkcarrier['pedido_frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
            return 0;
        }

        // Inicializa variaveis
        $total_frete = 0;

        foreach ($fkcarrier['embalagens'] as $embalagem) {

            // Verifica se o produto e frete gratis e o Carrier e o Carrier Frete Gratis
            if ($embalagem['frete_gratis'] == true And $fkcarrier['carrier_atual'] == $fkcarrier['carrier_frete_gratis']) {
                continue;
            }

            $valor_frete = 0;
            $ultimo_peso_tabela = 0;
            $ultimo_valor_tabela = 0;
            $peso_excedente = 0;

            // Retorna com valor fixo se o preco for tipo 1
            if ($fkcarrier['tipo_preco'] == '1') {
                return $fkcarrier['tabela_preco'];
            }else {
                // Verifica se deve considerar o peso real ou peso cubico
                $peso_cubico = ($embalagem['cubagem'] / 1000000) * $fkcarrier['fator_cubagem'];

                if ($peso_cubico > ($embalagem['peso_produtos'] + $embalagem['peso_embalagem'])) {
                    $peso_produtos = $peso_cubico;
                }else {
                    $peso_produtos = $embalagem['peso_produtos'] + $embalagem['peso_embalagem'];
                }

                // Recupera o valor a ser cobrado
                $precos = explode('/', $fkcarrier['tabela_preco']);

                foreach ($precos as $itens_preco) {

                    if ($itens_preco == '') {
                        continue;
                    }

                    $pos = strpos($itens_preco, ':');

                    // Ignora o Carrier pois a tabela está configurada errada
                    if ($pos === false) {
                        return false;
                    }

                    $peso_tabela = substr($itens_preco, 0, $pos);

                    // Guarda os ultimos valores de peso e valor
                    $ultimo_peso_tabela = $peso_tabela;
                    $ultimo_valor_tabela = substr($itens_preco, $pos + 1);

                    // Verifica se é o valor a ser adotado
                    if ($peso_produtos <= $peso_tabela) {
                        $valor_frete = substr($itens_preco, $pos + 1);
                        break;
                    }
                }
            }

            // Se o valor do frete é 0 recupera o valor com base nos ultimo valores da tabela e verifica se existe existe adicional por peso excedido
            if ($valor_frete == 0) {
                if ($peso_produtos > $ultimo_peso_tabela) {
                    $peso_excedente = $peso_produtos - $ultimo_peso_tabela;
                    $peso_produtos = $ultimo_peso_tabela;
                    $valor_frete = $ultimo_valor_tabela;
                }else {
                    // Ignora o Carrier pois a tabela está configurada errada
                    return false;
                }
            }

            // Calcula o valor do frete considerando peso x valor kilo por intervalo de peso
            if ($fkcarrier['tipo_preco'] == '3') {
                $valor_frete *= $peso_produtos;
            }

            // Inclui o adicional por excedente de peso
            $valor_frete += ($peso_excedente * $fkcarrier['valor_adicional_excedente_kilo']);

            // Inclui o percentual referente ao seguro
            $valor_frete += $embalagem['valor_produtos'] * $fkcarrier['percentual_seguro'] / 100;

            // Inclui valor referente ao pedagio
            $valor_frete += $fkcarrier['valor_pedagio'];

            // Acumula valor do frete
            $total_frete += $valor_frete + $embalagem['custo_embalagem'];
        }

        // Verifica se o Custo de Envio deve ser adicionado ao valor do frete da transportadora
        if ($total_frete > 0) {

			if (Configuration::get('PS_SHIPPING_HANDLING') > 0) {
				$carrier = new Carrier($fkcarrier['carrier_atual']);

				if ($carrier->shipping_handling) {
					$total_frete += Configuration::get('PS_SHIPPING_HANDLING');
				}
			}
        }
        
        return $total_frete;

    }

    private function retornaPrazoEntrega($uf_destino, $servico_correios = true, $capital = false) {

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'fkcarrier_prazos_entrega` WHERE `estado` = "'.$uf_destino.'"';
        $prazo_entrega = Db::getInstance()->getRow($sql);

        if (!$prazo_entrega) {
            return 0;
        }

        if ($servico_correios == true) {

            if ($capital == true) {
                return $prazo_entrega['correios_capital'];
            }else {
                return $prazo_entrega['correios_interior'];
            }
        }else {

            if ($capital == true) {
                return $prazo_entrega['transp_capital'];
            }else {
                return $prazo_entrega['transp_interior'];
            }
        }

    }

    static function ordenaCubagem($a, $b) {

        if ($a['cubagem'] == $b['cubagem']) {
            return 0;
        }
        return ($a['cubagem'] < $b['cubagem']) ? -1 : 1;
    }

    static function ordenaValorFrete($a, $b) {

        if ($a['valor_frete'] == $b['valor_frete']) {
            return 0;
        }
        return ($a['valor_frete'] < $b['valor_frete']) ? -1 : 1;
    }

    private function criaTabelas() {
		
		// Exclui tabelas anteriores, se existirem
		$this->excluiTabelas();
		
		$db = Db::getInstance();
		
		// Cria a tabela de cadastro de cep
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_cadastro_cep` (
            	`id` 			    int(10)     NOT NULL AUTO_INCREMENT,
            	`estado` 		    varchar(2),
            	`capital` 		    varchar(50),
            	`cep_estado` 	    varchar(150),
           	 	`cep_capital` 	    varchar(150),
           	 	`cep_base_capital` 	varchar(9),
           	 	`cep_base_interior`	varchar(9),
            	PRIMARY KEY  (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);
		
		// Insere intervalo de cep dos estados e capitais
		$sql = "INSERT INTO `"._DB_PREFIX_."fkcarrier_cadastro_cep` (`estado`, `capital`, `cep_estado`, `cep_capital`, `cep_base_capital`, `cep_base_interior`) VALUES
            ('AC', 'Rio Branco', 		'69900000:69999999', 						'69900001:69923999',                    '69900-001', '69985-000'),
            ('AL', 'Maceió', 			'57000000:57999999', 						'57000001:57099999',                    '57000-001', '57770-000'),
            ('AM', 'Manaus', 			'69000000:69299999/69400000:69899999', 		'69000001:69099999',                    '69000-001', '69158-000'),
            ('AP', 'Macapá', 			'68900000:68999999', 						'68900001:68911999',                    '68900-001', '68950-000'),
            ('BA', 'Salvador', 			'40000000:48999999', 						'40000001:42499999',                    '40000-001', '44500-000'),
            ('CE', 'Fortaleza', 		'60000000:63999999', 						'60000001:61599999',                    '60000-001', '62750-000'),
            ('DF', 'Brasília', 			'70000000:72799999/73000000:73699999', 		'70000001:72799999/73000001:73699999',  '70000-001', '70000-001'),
            ('ES', 'Vitória', 			'29000000:29999999', 						'29000001:29099999',                    '29000-001', '29700-001'),
            ('GO', 'Goiãnia', 			'72800000:72999999/73700000:76799999', 		'74000001:74899999',                    '74000-001', '75000-001'),
            ('MA', 'São Luiz', 			'65000000:65999999', 						'65000001:65109999',                    '65000-001', '65250-000'),
            ('MG', 'Belo Horizonte', 	'30000000:39999999', 						'30000001:31999999',                    '30000-001', '37130-000'),
            ('MS', 'Campo Grande', 		'79000000:79999999', 						'79000001:79124999',                    '79000-001', '79300-001'),
            ('MT', 'Cuiabá¡', 			'78000000:78899999', 						'78000001:78099999',                    '78000-001', '78200-000'),
            ('PA', 'Belém', 			'66000000:68899999', 						'66000001:66999999',                    '66000-001', '68370-001'),
            ('PB', 'João Pessoa', 		'58000000:58999999', 						'58000001:58099999',                    '58000-001', '58930-000'),
            ('PE', 'Recife', 			'50000000:56999999', 						'50000001:52999999',                    '50000-001', '53690-000'),
            ('PI', 'Teresina', 			'64000000:64999999', 						'64000001:64099999',                    '64000-001', '64235-000'),
            ('PR', 'Curitiba', 			'80000000:87999999', 						'80000001:82999999',                    '80000-001', '86800-001'),
            ('RJ', 'Rio de Janeiro', 	'20000000:28999999', 						'20000001:23799999',                    '20000-001', '27300-001'),
            ('RN', 'Natal', 			'59000000:59999999', 						'59000001:59139999',                    '59000-001', '59780-000'),
            ('RO', 'Porto Velho', 		'76800000:76999999', 						'76800001:76834999',                    '76800-001', '76870-001'),
            ('RR', 'Boa Vista', 		'69300000:69399999', 						'69300001:69339999',                    '69300-001', '69343-000'),
            ('RS', 'Porto Alegre', 		'90000000:99999999', 						'90000001:91999999',                    '90000-001', '97540-001'),
            ('SC', 'Florianópolis', 	'88000000:89999999', 						'88000001:88099999',                    '88000-001', '89245-000'),
            ('SE', 'Aracajú', 			'49000000:49999999', 						'49000001:49098999',                    '49000-001', '49500-000'),
            ('SP', 'São Paulo', 		'01000000:19999999', 						'01000001:05999999/08000000:08499999',  '01000-001', '17800-000'),
            ('TO', 'Palmas', 			'77000000:77999999', 						'77000001:77249999',                    '77000-001', '77645-000');";
		$db-> Execute($sql);
		
		// Cria a tabela de prazos de entrega
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_prazos_entrega` (
	            `id`		 		int(10) 	NOT NULL AUTO_INCREMENT,
				`id_shop`			int(10),
	            `estado` 			varchar(2),
	            `correios_capital` 	int(10),
	            `correios_interior` int(10),
	            `transp_capital` 	int(10),
	            `transp_interior` 	int(10),
	            PRIMARY KEY (`id`)
	            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);
		
		// Cria tabela com as medidas e peso de caixas
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_embalagens` (
	            `id` 			int(10) 		NOT NULL AUTO_INCREMENT,
				`id_shop`		int(10),
	            `descricao` 	varchar(50),
	            `comprimento` 	decimal(20,2),
	            `altura` 		decimal(20,2),
	            `largura` 		decimal(20,2),
	            `peso` 			decimal(20,2),
	            `cubagem` 		decimal(20,6),
	            `custo` 		decimal(20,2),
	            `ativo` 		tinyint(1),
	            PRIMARY KEY (`id`)
	            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);
		
		// Cria tabela com as Especificacoes dos Correios
        // id_interno: 1= E-SEDEX, 2= PAC, 3= SEDEX, 4= SEDEX 10, 5= SEDEX 12, 6= SEDEX HOJE, 7= PAC-GF
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_especificacoes_correios` (
	            `id` 							int(10) 		NOT NULL AUTO_INCREMENT,
	            `id_shop`		                int(10),
	            `id_interno`			        int(10),
	            `servico` 						varchar(50),
				`cod_servico`					varchar(50),
				`cod_administrativo` 			varchar(50),
            	`senha` 						varchar(10),
	            `comprimento_min` 				decimal(20,2),
				`comprimento_max` 				decimal(20,2),
				`largura_min` 					decimal(20,2),
				`largura_max` 					decimal(20,2),
	            `altura_min` 					decimal(20,2),
				`altura_max` 					decimal(20,2),
				`somatoria_dimensoes_max` 		decimal(20,2),
                `volume_max`                    decimal(20,2),
	            `peso_estadual_max`				decimal(20,2),
				`peso_nacional_max`				decimal(20,2),
				`intervalo_pesos_estadual`		varchar(250),
				`intervalo_pesos_nacional`		varchar(250),
				`cubagem_max_isenta`			decimal(20,5),
				`cubagem_base_calculo`			decimal(20,5),
				`mao_propria_valor`				decimal(20,2),
				`aviso_recebimento_valor`		decimal(20,2),
				`valor_declarado_percentual`	decimal(20,2),
				`valor_declarado_max`			decimal(20,2),
				`seguro_automatico_valor`       decimal(20,2),
	            PRIMARY KEY (`id`)
	            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);

		// Cria tabela com os servicos dos correios e transportadoras controladas pelo FKcarrier
		$sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcarrier_correios_transp` (
            	`id` 				int(10) 	NOT NULL AUTO_INCREMENT,
				`id_shop`			int(10),
            	`id_carrier` 		int(10),
				`id_correios` 		int(10),
				`nome_carrier`  	varchar(64),
            	`grade` 			int(10),
            	`ativo` 			tinyint(1),
            	PRIMARY KEY (`id`),
				INDEX (`id_carrier`, `id_shop`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);
		
		// Cria tabela com as regioes e precos dos carrier
		$sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcarrier_regioes_precos` (
            	`id` 								int(10) 		NOT NULL AUTO_INCREMENT,
				`id_correios_transp`				int(10),
				`nome_regiao`  						varchar(100),
				`prazo_entrega_especifico`			varchar(50),
				`regiao_uf`							varchar(100),
				`regiao_cep`						text,
				`tipo_preco`						int(10),
				`preco_1`							decimal(20,2),
				`preco_2`							text,
				`preco_3`							text,
				`valor_adicional_excedente_kilo`	decimal(20,2),
				`percentual_seguro`					decimal(20,2),
				`valor_pedagio`						decimal(20,2),
				`fator_cubagem`						decimal(20,5),
				INDEX (`id_correios_transp`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$db-> Execute($sql);

        // Cria tabela com as configuracoes do frete gratis
        $sql = 'CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_. 'fkcarrier_frete_gratis` (
            	`id` 					int(10) 		NOT NULL AUTO_INCREMENT,
            	`id_shop`			    int(10),
				`id_correios_transp`	int(10),
				`nome_regiao`  			varchar(100),
				`regiao_uf`				varchar(100),
				`regiao_cep`			text,
				`valor_pedido`			decimal(20,2),
				`id_produtos`			text,
				`ativo` 			    tinyint(1),
				INDEX (`id_correios_transp`),
            	PRIMARY KEY (`id`)
            	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria a tabela de precos offline dos correios
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_tabelas_offline` (
                `id`                    int(10)     NOT NULL AUTO_INCREMENT,
                `id_correios_transp`    int(10),
                `id_cadastro_cep`       int(10),
                `tabela_capital`        text,
                `tabela_interior`       text,
                `minha_cidade` 		    tinyint(1),
                INDEX (`id_correios_transp`),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

        // Cria a tabela de cache
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fkcarrier_cache` (
                `id`            int(10)     NOT NULL AUTO_INCREMENT,
                `hash`          varchar(32),
                `valor_frete`   decimal(20,2),
                `prazo_entrega` int(10),
                INDEX (`hash`),
                PRIMARY KEY  (`id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $db-> Execute($sql);

		return true;
	}
	
	private function excluiTabelas() {
		
		$db = Db::getInstance();
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_cadastro_cep`;";
		$db-> Execute($sql);
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_prazos_entrega`;";
		$db-> Execute($sql);
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_embalagens`;";
		$db-> Execute($sql);
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_especificacoes_correios`;";
		$db-> Execute($sql);
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_correios_transp`;";
		$db-> Execute($sql);
		
		$sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_regioes_precos`;";
		$db-> Execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_frete_gratis`;";
        $db-> Execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_tabelas_offline`;";
        $db-> Execute($sql);

        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."fkcarrier_cache`;";
        $db-> Execute($sql);
		
		return true;
	}

    private function instalaRegioes(){

        $regioes = new Zone();

        // Cria regiao Brasil
        $nome = 'Brasil';

        if (!$regioes->getIdByName($nome)) {
            $regioes->name = $nome;
            $regioes->active = true;
            $regioes->add();

            // Liga a regiao aos Shops
            $regioes->associateTo($regioes->id_shop_list);

            // Liga country padrao a regiao
            $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            $country->id_zone = $regioes->id;
            $country->save();

            // Liga Estados a regiao Brasil
            $states = new State();
            $estados_brasil = $states->getStatesByIdCountry($country->id);

            foreach ($estados_brasil as $estado) {
                Db::getInstance()->update('state', array('id_zone' => $regioes->id), 'id_state = '.(int)$estado['id_state']);
            }

        }

        return true;
    }

}