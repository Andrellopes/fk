<html>

	<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=8&section=configTransp" method="post" class="form" id="configTranspOpcoes">
		<div class="fkcarrier_opcoes">
			<input id="fkcarrier_submit_add" name="submitAdd" type="submit" value="">
			<p>Adicionar Transportadora</p>
			
			<input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/transp.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
			<p>Ajuda</p>
		</div>
	</form>

	<?php
	// Recupera as transportadoras cadastradas
	$sql_correios_transp = 'SELECT * FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id_correios` = 0 AND `id_shop` = '.$this->context->shop->id.' ORDER BY `nome_carrier`';
	$correios_transp = Db::getInstance()->executeS($sql_correios_transp);
	
	foreach($correios_transp as $reg_correios_transp) {
	?>
	
	<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=8&section=configTransp&id_correios_transp=<?php echo $reg_correios_transp['id']?>" method="post" class="form" id="configTransp" enctype="multipart/form-data">
	
		<div class="fkcarrier_margin_form" id="fkcarrier_transp">
		
            <div class="fkcarrier_toggle_titulo" onclick="fkcarrierToggle('fkcarrier_transp_itens_<?php echo $reg_correios_transp['id'];?>')">
                <?php echo $reg_correios_transp['nome_carrier'];?>
            </div>

            <div class="fkcarrier_toggle_itens" id="fkcarrier_transp_itens_<?php echo $reg_correios_transp['id'];?>">

                <div class="fkcarrier_form_group">
                    <label>Nome transportadora:</label>
                    <input type="text" size="60" name="fkcarrier_transp_nome_<?php echo $reg_correios_transp['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_nome_'.$reg_correios_transp['id']) ? $reg_correios_transp['nome_carrier'] : Tools::getValue('fkcarrier_transp_nome_'.$reg_correios_transp['id']));?>" onkeyup="$('#fkcarrier_transp_titulo_<?php echo $reg_correios_transp['id'];?>').val($(this).val());"/>
                </div>

                <div class="fkcarrier_form_group">
                    <label>Transportadora ativa:</label>
                    <input type="checkbox" name="fkcarrier_transp_ativo_<?php echo $reg_correios_transp['id'];?>" value="on" <?php echo ($reg_correios_transp['ativo'] == 1 ? 'checked="checked"' : '');?>/>
                </div>

                <div class="fkcarrier_form_group">
                    <label>Grade velocidade:</label>
                    <input type="number" name="fkcarrier_transp_grade_<?php echo $reg_correios_transp['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_grade_'.$reg_correios_transp['id']) ? $reg_correios_transp['grade'] : Tools::getValue('fkcarrier_transp_grade_'.$reg_correios_transp['id']));?>"/>
                </div>

                <label>Logo:</label>

                <div class="fkcarrier_img" id="fkcarrier_transp_img">
                    <?php
                        $path_logo = Tools::getShopDomainSsl(true, true)._PS_IMG_.'s/'.$reg_correios_transp['id_carrier'].'.jpg';

                        if (!file_exists(_PS_IMG_DIR_.'s/'.$reg_correios_transp['id_carrier'].'.jpg')) {
                            $path_logo = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcarrier/upload/'.'no_image.jpg';
                        }
                    ?>

                    <div class="fkcarrier_form_group">
                        <img id="fkcarrier_logo_transp_<?php echo $reg_correios_transp['id_carrier'];?>" alt="Logo transportadora" src="<?php echo $path_logo;?>">
                    </div>

                    <div class="fkcarrier_form_group">
                        <input type="file" name="fkcarrier_transp_imagem_<?php echo $reg_correios_transp['id'];?>">
                    </div>

                    <script type="text/javascript">
                        d = new Date();
                        $("#fkcarrier_logo_transp_<?php echo $reg_correios_transp['id_carrier'];?>").attr("src", "<?php echo $path_logo;?>?" + d.getTime());
                    </script>

                </div>

                <?php
                // Recupera regioes e precos
                $sql_regioes_precos = 'SELECT * FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$reg_correios_transp['id'].' ORDER BY `nome_regiao`';
                $regioes_precos = Db::getInstance()->executeS($sql_regioes_precos);

                // Processa UF
                foreach($regioes_precos as $reg_regioes_precos) {
                ?>

                <div class="fkcarrier_divisao" id="fkcarrier_transp_divisao" onclick="fkcarrierToggle('fkcarrier_transp_itens_regioes_<?php echo $reg_regioes_precos['id'];?>')">
                    <div><?php echo $reg_regioes_precos['nome_regiao'];?></div>
                </div>

                <div id="fkcarrier_transp_itens_regioes_<?php echo $reg_regioes_precos['id'];?>" style="display: none;">
                    <div class="fkcarrier_form_group">
                        <label>Nome região:</label>
                        <input type="text" size="60" name="fkcarrier_transp_nome_regiao_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_nome_regiao_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['nome_regiao'] : Tools::getValue('fkcarrier_transp_nome_regiao_'.$reg_regioes_precos['id']));?>" onkeyup="$('#fkcarrier_transp_regioes_<?php echo $reg_regioes_precos['id'];?>').val($(this).val());"/>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label>Prazo entrega específico:</label>
                        <input type="text" size="40" name="fkcarrier_transp_prazo_ent_esp_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_prazo_ent_esp_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['prazo_entrega_especifico'] : Tools::getValue('fkcarrier_transp_prazo_ent_esp_'.$reg_regioes_precos['id']));?>"/>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label><?php echo $this->l('Estados atendidos:')?></label>
                    </div>

                    <div class="fkcarrier_estados_atendidos">

                        <?php
                        $sql_estados = 'SELECT `estado` FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep` ORDER BY `estado`';
                        $estados = Db::getInstance()->executeS($sql_estados);
                        foreach($estados as $estado) {

                            // Pesquisa a UF
                            if (isset($reg_regioes_precos['regiao_uf'])) {
                                if (strpos($reg_regioes_precos['regiao_uf'], $estado['estado']) === false) {
                                    $localizado = false;
                                }else {
                                    $localizado = true;
                                }
                            }else {
                                $localizado = false;
                            }
                            ?>

                            <div>
                                <input type="checkbox" class="fkcarrier_transp_uf_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_uf_<?php echo $reg_regioes_precos['id'].'[]';?>" value="<?php echo $estado['estado'];?>" <?php echo ($localizado == true ? 'checked="checked"' : '')?>><?php echo ' '.$estado['estado'];?>
                            </div>

                        <?php
                        }
                        ?>

                        <br><br>
                        <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Marcar todos');?>" onclick="<?php echo 'fkcarrierMarcar(\'fkcarrier_transp_uf_'.$reg_regioes_precos['id'].'\');';?>">
                        <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Desmarcar todos');?>" onclick="<?php echo 'fkcarrierDesmarcar(\'fkcarrier_transp_uf_'.$reg_regioes_precos['id'].'\');';?>">

                    </div>

                    <div class="fkcarrier_form_group">
                        <label><?php echo $this->l('Intervalo CEPs atendidos:')?></label>
                        <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_transp_cep1_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_cep1_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                        a
                        <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_transp_cep2_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_cep2_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                        <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierIncluirCepTransp('.$reg_regioes_precos['id'].');';?>">
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <textarea style="width: 492px;" rows="4" id="fkcarrier_transp_intervalos_cep_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_intervalos_cep_<?php echo $reg_regioes_precos['id'];?>"><?php echo (!Tools::getValue('fkcarrier_transp_intervalos_cep_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['regiao_cep'] : Tools::getValue('fkcarrier_transp_intervalos_cep_'.$reg_regioes_precos['id']));?></textarea>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label>Cobrança do frete:</label>
                        <input type="radio" name="fkcarrier_transp_tipo_preco_<?php echo $reg_regioes_precos['id'];?>" value="1" <?php echo ($reg_regioes_precos['tipo_preco'] == 1 ? 'checked="checked"' : '');?> onclick="fkcarrierTranspPreco1(<?php echo $reg_regioes_precos['id'];?>);"/><?php echo ' '.$this->l('Preço fixo');?>
                    </div>
                    <div class="fkcarrier_transp_preco" id="fkcarrier_transp_preco1_<?php echo $reg_regioes_precos['id'];?>" <?php echo ($reg_regioes_precos['tipo_preco'] == 1 ? 'style="display:block"' : 'style="display:none"');?>>
                        <div class="fkcarrier_form_group">
                            <label></label>
                            <?php echo $this->l('cobrar o valor de:')?>
                            <input type="text" size="6" name="fkcarrier_transp_preco_1_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_preco_1_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['preco_1'] : Tools::getValue('fkcarrier_transp_preco_1_'.$reg_regioes_precos['id']));?>"/>
                        </div>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <input type="radio" name="fkcarrier_transp_tipo_preco_<?php echo $reg_regioes_precos['id'];?>" value="2" <?php echo ($reg_regioes_precos['tipo_preco'] == 2 ? 'checked="checked"' : '');?> onclick="fkcarrierTranspPreco2(<?php echo $reg_regioes_precos['id'];?>);"/><?php echo ' '.$this->l('Preço fixo por intervalo de peso');?>
                    </div>
                    <div class="fkcarrier_transp_preco" id="fkcarrier_transp_preco2_<?php echo $reg_regioes_precos['id'];?>" <?php echo ($reg_regioes_precos['tipo_preco'] == 2 ? 'style="display:block"' : 'style="display:none"');?>>
                        <div class="fkcarrier_form_group">
                            <label></label>
                            <?php echo $this->l('até:')?>
                            <input type="text" size="4" id="fkcarrier_transp_preco2_kilo_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco2_kilo_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                            <?php echo $this->l('kilos, cobrar o valor de:')?>
                            <input type="text" size="6" id="fkcarrier_transp_preco2_valor_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco2_valor_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                            <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierTranspIncluiPreco2('.$reg_regioes_precos['id'].');';?>">
                        </div>

                        <div class="fkcarrier_form_group">
                            <label></label>
                            <textarea style="width: 492px;" rows="4" id="fkcarrier_transp_preco_2_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco_2_<?php echo $reg_regioes_precos['id'];?>"><?php echo (!Tools::getValue('fkcarrier_transp_preco_2_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['preco_2'] : Tools::getValue('fkcarrier_transp_preco_2_'.$reg_regioes_precos['id']));?></textarea>
                        </div>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <input type="radio" name="fkcarrier_transp_tipo_preco_<?php echo $reg_regioes_precos['id'];?>" value="3" <?php echo ($reg_regioes_precos['tipo_preco'] == 3 ? 'checked="checked"' : '');?> onclick="fkcarrierTranspPreco3(<?php echo $reg_regioes_precos['id'];?>);"/><?php echo ' '.$this->l('Preço peso x valor kilo por intervalo de peso');?>
                    </div>
                    <div class="fkcarrier_transp_preco" id="fkcarrier_transp_preco3_<?php echo $reg_regioes_precos['id'];?>" <?php echo ($reg_regioes_precos['tipo_preco'] == 3 ? 'style="display:block"' : 'style="display:none"');?>>
                        <div class="fkcarrier_form_group">
                            <label></label>
                            <?php echo $this->l('até:')?>
                            <input type="text" size="4" id="fkcarrier_transp_preco3_kilo_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco3_kilo_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                            <?php echo $this->l('kilos, cobrar o valor por kilo de:')?>
                            <input type="text" size="6" id="fkcarrier_transp_preco3_valor_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco3_valor_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                            <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierTranspIncluiPreco3('.$reg_regioes_precos['id'].');';?>">
                        </div>

                        <div class="fkcarrier_form_group">
                            <label></label>
                            <textarea style="width: 475px;" rows="4" id="fkcarrier_transp_preco_3_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_transp_preco_3_<?php echo $reg_regioes_precos['id'];?>"><?php echo (!Tools::getValue('fkcarrier_transp_preco_3_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['preco_3'] : Tools::getValue('fkcarrier_transp_preco_3_'.$reg_regioes_precos['id']));?></textarea>
                        </div>
                    </div>

                    <div class="fkcarrier_transp_demais_itens" id="fkcarrier_preco_demais_itens_<?php echo $reg_regioes_precos['id'];?>" <?php echo ($reg_regioes_precos['tipo_preco'] == 1 ? 'style="display:none"' : 'style="display:block"');?>>

                        <div class="fkcarrier_form_group">
                            <label><?php echo $this->l('Valor kilo excedente:');?></label>
                            <input type="text" size="4" name="fkcarrier_transp_valor_kilo_excedente_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_valor_kilo_excedente_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['valor_adicional_excedente_kilo'] : Tools::getValue('fkcarrier_transp_valor_kilo_excedente_'.$reg_regioes_precos['id']));?>"/>
                        </div>

                        <div class="fkcarrier_form_group">
                            <label><?php echo $this->l('Percentual seguro:');?></label>
                            <input type="text" size="4" name="fkcarrier_transp_seguro_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_seguro_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['percentual_seguro'] : Tools::getValue('fkcarrier_transp_seguro_'.$reg_regioes_precos['id']));?>"/>
                        </div>

                        <div class="fkcarrier_form_group">
                            <label><?php echo $this->l('Valor pedágio:');?></label>
                            <input type="text" size="4" name="fkcarrier_transp_pedagio_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_pedagio_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['valor_pedagio'] : Tools::getValue('fkcarrier_transp_pedagio_'.$reg_regioes_precos['id']));?>"/>
                        </div>

                        <div class="fkcarrier_form_group">
                            <label><?php echo $this->l('Fator cubagem:');?></label>
                            <input type="text" size="8" name="fkcarrier_transp_cubagem_<?php echo $reg_regioes_precos['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_transp_cubagem_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['fator_cubagem'] : Tools::getValue('fkcarrier_transp_cubagem_'.$reg_regioes_precos['id']));?>"/>
                        </div>

                    </div>

                    <div class="fkcarrier_form_group">
                        <label>Excluir região:</label>
                        <input type="checkbox" name="fkcarrier_transp_excluir_regioes[]" value="<?php echo $reg_regioes_precos['id'];?>"/>
                    </div>

                </div>

                <?php
                }
                ?>

                <div class="fkcarrier_div_button">
                    <input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">
                    <input class="fkcarrier_button" name="submitAddRegiao" type="submit" value="<?php echo $this->l('Adicionar nova região');?>">

                    <div>
                        <input class="fkcarrier_button_warning" name="submitDel" type="submit" value="<?php echo $this->l('Excluir transportadora');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma a exclusão da transportadora?')?>');">
                        <input class="fkcarrier_button_warning" name="submitDelRegioes" type="submit" value="<?php echo $this->l('Excluir regiões selecionadas');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma a exclusão das regiões selecionadas?')?>');">
                    </div>
                </div>
            </div>
		</div>
	
	</form>
	
	<?php 
	}
	?>

</html>