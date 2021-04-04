<html>

<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=5&section=configEmbalagens" method="post" class="form" id="configEmbalagens">

    <div class="fkcarrier_opcoes">
        <input id="fkcarrier_submit_add" name="submitAdd" type="submit" value="">
        <p>Adicionar Nova Embalagem</p>

        <input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/embalagens.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
        <p>Ajuda</p>
    </div>

    <?php
    $embalagens = Db::getInstance() -> ExecuteS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_embalagens` Where `id_shop` = '.$this->context->shop->id.' Order By `cubagem`');

    if ($embalagens) {
    ?>

	<div class="fkcarrier_margin_form" id="fkcarrier_embalagens">

		<table>
	        <tr>
		        <th><?php echo $this->l('Descrição')?></th>
		        <th><?php echo $this->l('Comprimento (cm)')?></th>
		        <th><?php echo $this->l('Altura (cm)')?></th>
		        <th><?php echo $this->l('Largura (cm)')?></th>
		        <th><?php echo $this->l('Peso (kg)')?></th>
		        <th><?php echo $this->l('Preço de Custo')?></th>
		        <th><?php echo $this->l('Ativo')?></th>
		        <th><?php echo $this->l('Excluir')?></th>
	        </tr>
		
			<?php 	        
		    foreach ($embalagens as $reg) {
			?>
			        
		    <tr>
				<td><input type="text" size="30" name="fkcarrier_descricao_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_descricao_'.$reg['id']) ? $reg['descricao'] : Tools::getValue('fkcarrier_descricao_'.$reg['id']));?>"/></td>
		        <td><input type="text" size="5" name="fkcarrier_comprimento_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_comprimento_'.$reg['id']) ? $reg['comprimento'] : Tools::getValue('fkcarrier_comprimento_'.$reg['id']));?>"/></td>
			    <td><input type="text" size="5" name="fkcarrier_altura_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_altura_'.$reg['id']) ? $reg['altura'] : Tools::getValue('fkcarrier_altura_'.$reg['id']));?>"/></td>
		        <td><input type="text" size="5" name="fkcarrier_largura_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_largura_'.$reg['id']) ? $reg['largura'] : Tools::getValue('fkcarrier_largura_'.$reg['id']));?>"/></td>
			    <td><input type="text" size="5" name="fkcarrier_peso_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_peso_'.$reg['id']) ? $reg['peso'] : Tools::getValue('fkcarrier_peso_'.$reg['id']));?>"/></td>
		        <td><input type="text" size="5" name="fkcarrier_custo_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_custo_'.$reg['id']) ? $reg['custo'] : Tools::getValue('fkcarrier_custo_'.$reg['id']));?>"/></td>
		        <td><input type="checkbox" name="fkcarrier_ativo[]" value="<?php echo $reg['id'];?>"<?php echo (($reg['ativo'] == 1) ? 'checked="checked"' : '')?>/></td>
		        <td><input type="checkbox" class="fkcarrier_embalagens_excluir" name="fkcarrier_excluir[]" value="<?php echo $reg['id'];?>"/></td>
		    </tr>
			
		    <?php 
		    }
	        ?>
		
		</table>
		
		<div class="fkcarrier_div_button">
			<input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">
			
			<div>
				<input class="fkcarrier_button_warning" name="submitDel" type="submit" value="<?php echo $this->l('Excluir embalagens selecionadas');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma a exclusão das embalagens?')?>');">
			</div>
		</div>
	</div>

    <?php
    }
    ?>

</form>

</html>
