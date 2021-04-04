<html>

	<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=7&section=configServicosCorreios" method="post" class="form" id="configServicosCorreios">
	
		<?php 
		// Selecao dos servicos dos Correios disponiveis
		$sql_servicos = 'SELECT `id`, `servico`
		                 FROM `'._DB_PREFIX_.'fkcarrier_especificacoes_correios`
						 WHERE   `id_shop` = '.$this->context->shop->id.' AND
						         `id` NOT IN (SELECT `id_correios` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id_shop` = '.$this->context->shop->id.')';
		
		$servicos_correios = Db::getInstance()->executeS($sql_servicos);
		?>
	
		<div class="fkcarrier_opcoes">
	
			<?php 
			if ($servicos_correios) {
			?>
			
			<input id="fkcarrier_submit_add" name="submitAdd" type="submit" value="">
			<p>Adicionar Serviços Selecionados</p>
			
			<?php 
			}
			?>
			
			<input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/servicos_correios.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
			<p>Ajuda</p>
			
		</div>

        <?php
        if ($servicos_correios) {
        ?>
		
		<div class="fkcarrier_margin_form" id="fkcarrier_servicos_correios">

            <p><?php echo $this->l('Serviços disponíveis:');?></p>

            <?php
            // Recupera os servicos ainda nao selecionados
            foreach ($servicos_correios as $reg_servicos_correios) {
            ?>
                <input type="checkbox" name="fkcarrier_servicos_correios[]" value="<?php echo $reg_servicos_correios['id']?>"><?php echo ' '.$reg_servicos_correios['servico']?>
            <?php
            }
            ?>

		</div>

        <?php
        }
        ?>

	</form>

	<?php
	// Recupera os servicos dos Correios disponibilizados 
	$sql_correios_transp = 'SELECT `id`, `id_carrier`, `nome_carrier`, `grade`, `ativo` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id_correios` > 0 AND `id_shop` = '.$this->context->shop->id.' ORDER BY `nome_carrier`';
	$correios_transp = Db::getInstance()->executeS($sql_correios_transp);
	
	foreach($correios_transp as $reg_correios_transp) {
	?>
	
	<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=7&section=configServicosCorreios&id_correios_transp=<?php echo $reg_correios_transp['id']?>" method="post" class="form" id="configServicosCorreios" enctype="multipart/form-data">

		<div class="fkcarrier_margin_form" id="fkcarrier_correios_selecionados">
		
            <div class="fkcarrier_toggle_titulo" onclick="fkcarrierToggle('fkcarrier_correios_selecionados_itens_<?php echo $reg_correios_transp['id'];?>')">
                <?php echo $reg_correios_transp['nome_carrier'];?>
            </div>

            <div class="fkcarrier_toggle_itens" id="fkcarrier_correios_selecionados_itens_<?php echo $reg_correios_transp['id'];?>">

                <div class="fkcarrier_form_group">
                    <label>Serviço ativo:</label>
                    <input type="checkbox" name="fkcarrier_correios_ativo_<?php echo $reg_correios_transp['id'];?>" value="on" <?php echo ($reg_correios_transp['ativo'] == 1 ? 'checked="checked"' : '');?>/>
                </div>

                <div class="fkcarrier_form_group">
                    <label>Grade velocidade:</label>
                    <input type="number" name="fkcarrier_correios_grade_<?php echo $reg_correios_transp['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_correios_grade_'.$reg_correios_transp['id']) ? $reg_correios_transp['grade'] : Tools::getValue('fkcarrier_correios_grade_'.$reg_correios_transp['id']));?>"/>
                </div>

                <?php
                // Recupera regioes e precos
                $sql_regioes_precos = 'SELECT `id`, `regiao_uf`, `regiao_cep` FROM `'._DB_PREFIX_.'fkcarrier_regioes_precos` WHERE `id_correios_transp` = '.(int)$reg_correios_transp['id'];
                $regioes_precos = Db::getInstance()->executeS($sql_regioes_precos);

                // Processa UF
                foreach($regioes_precos as $reg_regioes_precos) {
                ?>

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
                        <input type="checkbox" class="fkcarrier_correios_uf_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_correios_uf_<?php echo $reg_regioes_precos['id'].'[]';?>" value="<?php echo $estado['estado'];?>" <?php echo ($localizado == true ? 'checked="checked"' : '');?>/><?php echo ' '.$estado['estado'];?>
                    </div>

                    <?php
                    }
                    ?>

                    <br><br>
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Marcar todos');?>" onclick="<?php echo 'fkcarrierMarcar(\'fkcarrier_correios_uf_'.$reg_regioes_precos['id'].'\');';?>">
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Desmarcar todos');?>" onclick="<?php echo 'fkcarrierDesmarcar(\'fkcarrier_correios_uf_'.$reg_regioes_precos['id'].'\');';?>">

                </div>

                <div class="fkcarrier_form_group">
                    <label><?php echo $this->l('Intervalo CEPs atendidos:')?></label>
                    <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_correios_cep1_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_correios_cep1_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                    a
                    <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_correios_cep2_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_correios_cep2_<?php echo $reg_regioes_precos['id'];?>" value=""/>
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierIncluirCepCorreios('.$reg_regioes_precos['id'].');';?>">
                </div>

                <div class="fkcarrier_form_group">
                    <label></label>
                    <textarea style="width: 492px;" rows="4" id="fkcarrier_correios_intervalos_cep_<?php echo $reg_regioes_precos['id'];?>" name="fkcarrier_correios_intervalos_cep_<?php echo $reg_regioes_precos['id'];?>"><?php echo (!Tools::getValue('fkcarrier_correios_intervalos_cep_'.$reg_regioes_precos['id']) ? $reg_regioes_precos['regiao_cep'] : Tools::getValue('fkcarrier_correios_intervalos_cep_'.$reg_regioes_precos['id']));?></textarea>
                </div>

                <?php
                }
                ?>

                <label>Logo:</label>

                <div class="fkcarrier_img" id="fkcarrier_correios_img">
                    <?php
                        $path_logo = Tools::getShopDomainSsl(true, true)._PS_IMG_.'s/'.$reg_correios_transp['id_carrier'].'.jpg';

                        if (!file_exists(_PS_IMG_DIR_.'s/'.$reg_correios_transp['id_carrier'].'.jpg')) {
                            $path_logo = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcarrier/upload/'.'no_image.jpg';
                        }
                    ?>

                    <div class="fkcarrier_form_group">
                        <img id="fkcarrier_logo_correios_<?php echo $reg_correios_transp['id_carrier'];?>" alt="Logo serviço" src="<?php echo $path_logo;?>">
                    </div>
                    <div class="fkcarrier_form_group">
                        <input type="file" name="fkcarrier_correios_imagem_<?php echo $reg_correios_transp['id'];?>">
                    </div>

                    <script type="text/javascript">
                        d = new Date();
                        $("#fkcarrier_logo_correios_<?php echo $reg_correios_transp['id_carrier'];?>").attr("src", "<?php echo $path_logo;?>?" + d.getTime());
                    </script>
                </div>

                <div class="fkcarrier_div_button">
                    <input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">

                    <div>
                        <input class="fkcarrier_button_warning" name="submitDel" type="submit" value="<?php echo $this->l('Excluir serviço');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma a exclusão do serviço?')?>');">
                    </div>
                </div>

            </div>
		</div>
			
	</form>

	<?php 
	}
	?>

</html>