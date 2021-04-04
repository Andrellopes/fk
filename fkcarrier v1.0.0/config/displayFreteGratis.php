<html>

    <form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=9&section=configFreteGratis" method="post" class="form" id="configFreteGratisOpcoes">
        <div class="fkcarrier_opcoes">
            <input id="fkcarrier_submit_add" name="submitAdd" type="submit" value="">
            <p>Adicionar Região</p>

            <input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/frete_gratis.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
            <p>Ajuda</p>
        </div>
    </form>

    <?php
    // Recupera os dados das regioes de frete gratis
    $sql_frete_gratis = 'SELECT * FROM `'._DB_PREFIX_.'fkcarrier_frete_gratis` WHERE `id_shop` = '.$this->context->shop->id;
    $frete_gratis = Db::getInstance()->executeS($sql_frete_gratis);

    foreach($frete_gratis as $reg_frete_gratis) {
    ?>

    <form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=9&section=configFreteGratis&id_frete_gratis=<?php echo $reg_frete_gratis['id']?>" method="post" class="form" id="configFreteGratis">

        <div class="fkcarrier_margin_form" id="fkcarrier_frete_gratis">

            <div class="fkcarrier_toggle_titulo" onclick="fkcarrierToggle('fkcarrier_frete_gratis_itens_<?php echo $reg_frete_gratis['id'];?>')">
                <?php echo $reg_frete_gratis['nome_regiao'];?>
            </div>

            <div class="fkcarrier_toggle_itens" id="fkcarrier_frete_gratis_itens_<?php echo $reg_frete_gratis['id'];?>">

                <div class="fkcarrier_form_group">
                    <label>Nome região:</label>
                    <input type="text" size="60" name="fkcarrier_frete_gratis_nome_regiao_<?php echo $reg_frete_gratis['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_frete_gratis_nome_regiao_'.$reg_frete_gratis['id']) ? $reg_frete_gratis['nome_regiao'] : Tools::getValue('fkcarrier_frete_gratis_nome_regiao_'.$reg_frete_gratis['id']));?>"/>
                </div>

                <div class="fkcarrier_form_group">
                    <label>Região ativa:</label>
                    <input type="checkbox" name="fkcarrier_frete_gratis_ativo_<?php echo $reg_frete_gratis['id'];?>" value="on" <?php echo ($reg_frete_gratis['ativo'] == 1 ? 'checked="checked"' : '');?>/>
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
                        if (isset($reg_frete_gratis['regiao_uf'])) {
                            if (strpos($reg_frete_gratis['regiao_uf'], $estado['estado']) === false) {
                                $localizado = false;
                            }else {
                                $localizado = true;
                            }
                        }else {
                            $localizado = false;
                        }
                        ?>

                        <div>
                            <input type="checkbox" class="fkcarrier_frete_gratis_uf_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_uf_<?php echo $reg_frete_gratis['id'].'[]';?>" value="<?php echo $estado['estado'];?>" <?php echo ($localizado == true ? 'checked="checked"' : '')?>><?php echo ' '.$estado['estado'];?>
                        </div>

                    <?php
                    }
                    ?>

                    <br><br>
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Marcar todos');?>" onclick="<?php echo 'fkcarrierMarcar(\'fkcarrier_frete_gratis_uf_'.$reg_frete_gratis['id'].'\');';?>">
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Desmarcar todos');?>" onclick="<?php echo 'fkcarrierDesmarcar(\'fkcarrier_frete_gratis_uf_'.$reg_frete_gratis['id'].'\');';?>">

                </div>

                <div class="fkcarrier_form_group">
                    <label><?php echo $this->l('Intervalo CEPs atendidos:')?></label>
                    <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_frete_gratis_cep1_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_cep1_<?php echo $reg_frete_gratis['id'];?>" value=""/>
                    a
                    <input class="fkcarrier_text_cep" type="text" size="10" id="fkcarrier_frete_gratis_cep2_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_cep2_<?php echo $reg_frete_gratis['id'];?>" value=""/>
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierIncluirCepFreteGratis('.$reg_frete_gratis['id'].');';?>">
                </div>

                <div class="fkcarrier_form_group">
                    <label></label>
                    <textarea style="width: 492px;" rows="4" id="fkcarrier_frete_gratis_intervalos_cep_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_intervalos_cep_<?php echo $reg_frete_gratis['id'];?>"><?php echo (!Tools::getValue('fkcarrier_frete_gratis_intervalos_cep_'.$reg_frete_gratis['id']) ? $reg_frete_gratis['regiao_cep'] : Tools::getValue('fkcarrier_frete_gratis_intervalos_cep_'.$reg_frete_gratis['id']));?></textarea>
                </div>

                <div class="fkcarrier_form_group">
                    <label><?php echo $this->l('Valor pedido:');?></label>
                    <input type="text" size="8" name="fkcarrier_frete_gratis_valor_pedido_<?php echo $reg_frete_gratis['id'];?>" value="<?php echo (!Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$reg_frete_gratis['id']) ? $reg_frete_gratis['valor_pedido'] : Tools::getValue('fkcarrier_frete_gratis_valor_pedido_'.$reg_frete_gratis['id']));?>"/>
                </div>

                <div class="fkcarrier_form_group">
                    <label></label>
                    <span>Valor 0 (zero) indica que a região não será selecionada de acordo com o valor do pedido.</span>
                </div>

                <div class="fkcarrier_form_group">
                    <label><?php echo $this->l('Produtos:')?></label>
                    <input style="width: 70px;" type="number" id="fkcarrier_frete_gratis_produto_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_produto_<?php echo $reg_frete_gratis['id'];?>" value=""/>
                    <input class="fkcarrier_button" name="button" type="button" value="<?php echo $this->l('Incluir');?>" onclick="<?php echo 'fkcarrierIncluirProdutosFreteGratis('.$reg_frete_gratis['id'].');';?>">
                </div>

                <div class="fkcarrier_form_group">
                    <label></label>
                    <textarea style="width: 492px;" rows="4" id="fkcarrier_frete_gratis_relacao_produtos_<?php echo $reg_frete_gratis['id'];?>" name="fkcarrier_frete_gratis_relacao_produtos_<?php echo $reg_frete_gratis['id'];?>"><?php echo (!Tools::getValue('fkcarrier_frete_gratis_relacao_produtos_'.$reg_frete_gratis['id']) ? $reg_frete_gratis['id_produtos'] : Tools::getValue('fkcarrier_frete_gratis_relacao_produtos_'.$reg_frete_gratis['id']));?></textarea>
                </div>

                <div class="fkcarrier_form_group">
                    <label><?php echo $this->l('Transportadora:')?></label>
                </div>

                <br>

                <?php
                $correios_transp = Db::getInstance()->executeS('SELECT `id`, `nome_carrier` FROM `'._DB_PREFIX_.'fkcarrier_correios_transp` WHERE `id_shop` = '.$this->context->shop->id.' ORDER BY `nome_carrier`');

                foreach($correios_transp as $reg_correios_transp) {
                ?>

                <div class="fkcarrier_form_group">
                    <label></label>
                    <input type="radio" name="fkcarrier_frete_gratis_transp_<?php echo $reg_frete_gratis['id'];?>" value="<?php echo $reg_correios_transp['id'];?>" <?php echo ($reg_frete_gratis['id_correios_transp'] == $reg_correios_transp['id'] ? 'checked="checked"' : '');?>><?php echo ' '.$reg_correios_transp['nome_carrier'];?>
                </div>

                <?php
                }
                ?>

                <div class="fkcarrier_div_button">
                    <input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">

                    <div>
                        <input class="fkcarrier_button_warning" name="submitDel" type="submit" value="<?php echo $this->l('Excluir região');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma a exclusão da região?')?>');">
                    </div>
                </div>
            </div>
        </div>

    </form>

    <?php
    }
    ?>

</html>
