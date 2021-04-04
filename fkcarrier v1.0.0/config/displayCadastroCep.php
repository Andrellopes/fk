<html>

<form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=3&section=configCadastroCep" method="post" class="form" id="configCadastroCep">

	<div class="fkcarrier_opcoes">
		<input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/cadastro_cep.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
		<p>Ajuda</p>
	</div>
	
	<div class="fkcarrier_margin_form" id="fkcarrier_cadastro_cep">
        <table>
        	<tr>
            	<th><?php echo $this->l('Estado');?></th>
                <th><?php echo $this->l('Intervalo de CEP dos Estados')?></th>
                <th><?php echo $this->l('Intervalo de CEP das Capitais')?></th>
                <th><?php echo $this->l('CEP base - Capital')?></th>
                <th><?php echo $this->l('CEP base - Interior')?></th>
          	</tr>
            
            <?php 
            $estados_capitais = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep` ORDER BY estado');
            foreach($estados_capitais as $reg) {
            ?>

            <tr>
                <td><?php echo $reg['estado'];?></td>
                <td>
                    <p>&nbsp;</p>
                    <input type="text" size="45" name="fkcarrier_cep_estado_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_cep_estado_'.$reg['id']) ? $reg['cep_estado'] : Tools::getValue('fkcarrier_cep_estado_'.$reg['id']));?>"/>
                </td>
	    		<td>
                    <p id="fkcarrier_cadastro_cep_capital"><?php echo $reg['capital']?></p>
                    <input type="text" size="45" name="fkcarrier_cep_capital_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_cep_capital_'.$reg['id']) ? $reg['cep_capital'] : Tools::getValue('fkcarrier_cep_capital_'.$reg['id']));?>"/>
                </td>
                <td>
                    <p>&nbsp;</p>
                    <input class="fkcarrier_text_cep" type="text" size="10" name="fkcarrier_cep_base_capital_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_cep_base_capital_'.$reg['id']) ? $reg['cep_base_capital'] : Tools::getValue('fkcarrier_cep_base_capital_'.$reg['id']));?>"/>
                </td>
                <td>
                    <p>&nbsp;</p>
                    <input class="fkcarrier_text_cep" type="text" size="10" name="fkcarrier_cep_base_interior_<?php echo $reg['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_cep_base_interior_'.$reg['id']) ? $reg['cep_base_interior'] : Tools::getValue('fkcarrier_cep_base_interior_'.$reg['id']));?>"/>
                </td>
			</tr>
             
            <?php  
            }
            ?>
            
    	</table>
    	
    	<div class="fkcarrier_div_button">
			<input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">
		</div>
	</div>

</form>

</html>
