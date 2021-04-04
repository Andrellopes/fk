<html>

    <form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=10&section=configTabelasOff" method="post" class="form" id="configTabelasOffOpcoes">
        <div class="fkcarrier_opcoes">
            <input id="fkcarrier_button_ajuda" name="fkcarrier_button_ajuda" type="button" value="" onClick="window.open('<?php echo _MODULE_DIR_?>/fkcarrier/ajuda/tabelas_off.html','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=700,height=500,left=500,top=150'); return false;">
            <p>Ajuda</p>
        </div>

    </form>

    <?php
    $sql_correios_transp = 'SELECT '._DB_PREFIX_.'fkcarrier_correios_transp.id, '._DB_PREFIX_.'fkcarrier_correios_transp.nome_carrier
                            FROM '._DB_PREFIX_.'fkcarrier_correios_transp
                                INNER JOIN '._DB_PREFIX_.'fkcarrier_especificacoes_correios
                                ON  '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios = '._DB_PREFIX_.'fkcarrier_especificacoes_correios.id
                            WHERE  ('._DB_PREFIX_.'fkcarrier_especificacoes_correios.id_interno = 2 OR '._DB_PREFIX_.'fkcarrier_especificacoes_correios.id_interno = 3) AND
                                    '._DB_PREFIX_.'fkcarrier_correios_transp.id_correios > 0 AND
                                    '._DB_PREFIX_.'fkcarrier_correios_transp.id_shop = '.$this->context->shop->id.'
                            ORDER BY `nome_carrier`';

    $correios_transp = Db::getInstance()->executeS($sql_correios_transp);

    foreach($correios_transp as $reg_correios_transp) {

        $tabelas_off = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` WHERE `id_correios_transp` = '.(int)$reg_correios_transp['id']);

        if (!$tabelas_off) {

            // cria registro minha cidade
            $dados = array(
                'id_correios_transp'    => $reg_correios_transp['id'],
                'id_cadastro_cep'       => '0',
                'tabela_capital'        => '',
                'tabela_interior'       => '',
                'minha_cidade'          => '1'
            );

            Db::getInstance()->insert('fkcarrier_tabelas_offline', $dados);

            // cria registro para cada estado
            $cadastro_cep = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'fkcarrier_cadastro_cep`');
            foreach ($cadastro_cep as $reg_cadastro_cep) {

                $dados = array(
                    'id_correios_transp'    => $reg_correios_transp['id'],
                    'id_cadastro_cep'       => $reg_cadastro_cep['id'],
                    'tabela_capital'        => '',
                    'tabela_interior'       => '',
                    'minha_cidade'          => '0'
                );

                Db::getInstance()->insert('fkcarrier_tabelas_offline', $dados);
            }

        }
    ?>

        <form action="<?php echo Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI'])?>&id_tab=10&section=configTabelasOff&id_correios_transp=<?php echo $reg_correios_transp['id'];?>" method="post" class="form" id="configTabelasOff">

            <div class="fkcarrier_margin_form" id="fkcarrier_tabelas_off">

                <div class="fkcarrier_toggle_titulo" onclick="fkcarrierToggle('fkcarrier_tabelas_off_itens_<?php echo $reg_correios_transp['id'];?>')">
                    <?php echo $reg_correios_transp['nome_carrier'];?>
                </div>

                <div class="fkcarrier_toggle_itens" id="fkcarrier_tabelas_off_itens_<?php echo $reg_correios_transp['id'];?>">

                    <div class="fkcarrier_form_group" id="fkcarrier_tabelas_off_proc_todos">
                        <input class="fkcarrier_button" type="button" name="button" value="<?php echo $this->l('Processar todos');?>" onclick="fkcarrierProcTabOffGeral('<?php echo $this->_url_funcoes;?>', '<?php echo $reg_correios_transp['id'];?>')">
                        <div class="fkcarrier_tabelas_off_status" id="fkcarrier_tabelas_off_status_<?php echo $reg_correios_transp['id'];?>"></div>
                    </div>

                    <div class="fkcarrier_divisao">
                        <div><?php echo $this->l('Tabela de preços');?></div>
                    </div>

                    <?php
                    // Recupera dados das tabelas offline de minha cidade
                    $sql_tabelas_off = 'SELECT `id`, `tabela_capital` FROM `'._DB_PREFIX_.'fkcarrier_tabelas_offline` WHERE `id_correios_transp` = '.(int)$reg_correios_transp['id'];
                    $tabelas_off = Db::getInstance()->getRow($sql_tabelas_off);
                    ?>

                    <div class="fkcarrier_form_group">
                        <label><?php echo $this->l('Minha cidade:')?></label>
                        <input class="fkcarrier_button" type="button" name="button" value="<?php echo $this->l('Processar esta tabela');?>" onclick="fkcarrierProcTabOffEspecifica('<?php echo $this->_url_funcoes;?>', '<?php echo $reg_correios_transp['id'];?>', '<?php echo $tabelas_off['id'];?>', 'capital')">
                        CEP base: <?php echo Configuration::get('FKCARRIER_MEU_CEP');?>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <textarea style="width: 492px;" rows="4" class="fkcarrier_tabelas_off_capital_<?php echo $reg_correios_transp['id'];?>" id="fkcarrier_tabelas_off_capital_<?php echo $tabelas_off['id'];?>" name="fkcarrier_tabelas_off_capital_<?php echo $tabelas_off['id'];?>"><?php echo (!Tools::getValue('fkcarrier_tabelas_off_capital_'.$tabelas_off['id']) ? $tabelas_off['tabela_capital'] : Tools::getValue('fkcarrier_tabelas_off_capital_'.$tabelas_off['id']));?></textarea>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <span id="fkcarrier_tabelas_off_alert_capital_<?php echo $tabelas_off['id'];?>" name="fkcarrier_tabelas_off_alert_capital_<?php echo $tabelas_off['id'];?>"></span>
                    </div>

                    <?php
                    // Recupera dados das tabelas offline dos estados
                    $sql_tabelas_off = 'SELECT '._DB_PREFIX_.'fkcarrier_tabelas_offline.id, '
                                                ._DB_PREFIX_.'fkcarrier_cadastro_cep.estado, '._DB_PREFIX_.'fkcarrier_cadastro_cep.capital, '
                                                ._DB_PREFIX_.'fkcarrier_cadastro_cep.cep_base_capital, '._DB_PREFIX_.'fkcarrier_cadastro_cep.cep_base_interior, '
                                                ._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_capital, '._DB_PREFIX_.'fkcarrier_tabelas_offline.tabela_interior
                                        FROM '._DB_PREFIX_.'fkcarrier_tabelas_offline
                                            INNER JOIN '._DB_PREFIX_.'fkcarrier_cadastro_cep
                                            ON '._DB_PREFIX_.'fkcarrier_tabelas_offline.id_cadastro_cep = '._DB_PREFIX_.'fkcarrier_cadastro_cep.id
                                        WHERE '._DB_PREFIX_.'fkcarrier_tabelas_offline.id_correios_transp = '.(int)$reg_correios_transp['id'];

                    $tabelas_off = Db::getInstance()->executeS($sql_tabelas_off);

                    foreach ($tabelas_off as $reg_tabelas_off) {
                    ?>

                    <br>

                    <div class="fkcarrier_form_group">
                        <label><?php echo $reg_tabelas_off['estado'].':'?></label>
                        <input class="fkcarrier_button" type="button" name="button" value="<?php echo $this->l('Processar esta tabela');?>" onclick="fkcarrierProcTabOffEspecifica('<?php echo $this->_url_funcoes;?>', '<?php echo $reg_correios_transp['id'];?>', '<?php echo $reg_tabelas_off['id'];?>', 'capital')">
                        <?php echo $reg_tabelas_off['capital'].' / CEP base: '.$reg_tabelas_off['cep_base_capital'];?>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <textarea style="width: 492px;" rows="4" class="fkcarrier_tabelas_off_capital_<?php echo $reg_correios_transp['id'];?>" id="fkcarrier_tabelas_off_capital_<?php echo $reg_tabelas_off['id'];?>" name="fkcarrier_tabelas_off_capital_<?php echo $reg_tabelas_off['id'];?>"><?php echo (!Tools::getValue('fkcarrier_tabelas_off_capital_'.$reg_tabelas_off['id']) ? $reg_tabelas_off['tabela_capital'] : Tools::getValue('fkcarrier_tabelas_off_capital_'.$reg_tabelas_off['id']));?></textarea>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <span id="fkcarrier_tabelas_off_alert_capital_<?php echo $reg_tabelas_off['id'];?>" name="fkcarrier_tabelas_off_alert_capital_<?php echo $reg_tabelas_off['id'];?>"></span>
                    </div>

                    <br>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <input class="fkcarrier_button" type="button" name="button" value="<?php echo $this->l('Processar esta tabela');?>" onclick="fkcarrierProcTabOffEspecifica('<?php echo $this->_url_funcoes;?>', '<?php echo $reg_correios_transp['id'];?>', '<?php echo $reg_tabelas_off['id'];?>', 'interior')">
                        <?php echo $this->l('Interior').' / CEP base: '.$reg_tabelas_off['cep_base_interior'];?>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <textarea style="width: 492px;" rows="4" class="fkcarrier_tabelas_off_interior_<?php echo $reg_correios_transp['id'];?>" id="fkcarrier_tabelas_off_interior_<?php echo $reg_tabelas_off['id'];?>" name="fkcarrier_tabelas_off_interior_<?php echo $reg_tabelas_off['id'];?>"><?php echo (!Tools::getValue('fkcarrier_tabelas_off_interior_'.$reg_tabelas_off['id']) ? $reg_tabelas_off['tabela_interior'] : Tools::getValue('fkcarrier_tabelas_off_interior_'.$reg_tabelas_off['id']));?></textarea>
                    </div>

                    <div class="fkcarrier_form_group">
                        <label></label>
                        <span id="fkcarrier_tabelas_off_alert_interior_<?php echo $reg_tabelas_off['id'];?>" name="fkcarrier_tabelas_off_alert_interior_<?php echo $reg_tabelas_off['id'];?>"></span>
                    </div>

                    <?php
                    }
                    ?>

                    <div class="fkcarrier_div_button">
                        <input class="fkcarrier_button" name="submitSave" type="submit" value="<?php echo $this->l('Salvar');?>">

                        <div>
                            <input class="fkcarrier_button_warning" name="submitDel" type="submit" value="<?php echo $this->l('Limpar tabelas');?>" onclick="return fkcarrierExcluir('<?php echo $this->l('Confirma limpeza das tabelas?')?>');">
                        </div>
                    </div>
                </div>
            </div>

        </form>

    <?php
    }
    ?>

</html>