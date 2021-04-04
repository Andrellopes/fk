
<script type="text/javascript" src="{$UrlJs}fkcustomers_front.js"></script>

<div class="account_creation">
    <h3 class="page-subheading">{l s='Informações Fiscais' mod='fkcustomers'}</h3>

    <div class="clearfix">
        <label>{l s='Tipo Pessoa' mod='fkcustomers'}</label>
        <br>
        <div class="radio-inline">
            <label for="tipo_1">
                <input type="radio" name="tipo" id="tipo_1" value="pf" onclick="procRadioTipo(this);" {if !isset($smarty.post.tipo) or isset($smarty.post.tipo) and $smarty.post.tipo == 'pf'} checked="checked"{/if}/>
                {l s='Física' mod='fkcustomers'}
            </label>
        </div>
        <div class="radio-inline">
            <label for="tipo_2">
                <input type="radio" name="tipo" id="tipo_2" value="pj" onclick="procRadioTipo(this);" {if isset($smarty.post.tipo) and $smarty.post.tipo == 'pj'} checked="checked"{/if}/>
                {l s='Jurídica' mod='fkcustomers'}
            </label>
        </div>

        <div class="required form-group" id="fkcustomers_pf" {if !isset($smarty.post.tipo) or isset($smarty.post.tipo) and $smarty.post.tipo == 'pf'} style="display:block" {else} style="display:none"{/if}>
            <div class="required form-group">
                <label for="fkcustomers_cpf">{l s='CPF' mod='fkcustomers'} <sup>*</sup></label>
                <input type="text" class="is_required form-control" name="fkcustomers_cpf" id="fkcustomers_cpf" value="{if isset($smarty.post.fkcustomers_cpf)}{$smarty.post.fkcustomers_cpf}{/if}"/>
            </div>
            <div class="required form-group">
                <label for="fkcustomers_rg">{l s='RG' mod='fkcustomers'}</label>
                <input type="text" class="is_required form-control" name="fkcustomers_rg" id="fkcustomers_rg" value="{if isset($smarty.post.fkcustomers_rg)}{$smarty.post.fkcustomers_rg}{/if}"/>
            </div>
        </div>

        <div class="required form-group" id="fkcustomers_pj" {if isset($smarty.post.tipo) and $smarty.post.tipo == 'pj'} style="display:block" {else} style="display:none"{/if}>
            <div class="required form-group">
                <label for="fkcustomers_cnpj">{l s='CNPJ' mod='fkcustomers'} <sup>*</sup></label>
                <input type="text" class="is_required form-control" name="fkcustomers_cnpj" id="fkcustomers_cnpj" value="{if isset($smarty.post.fkcustomers_cnpj)}{$smarty.post.fkcustomers_cnpj}{/if}"/>
            </div>
            <div class="required form-group">
                <label for="fkcustomers_ie">{l s='IE' mod='fkcustomers'}</label>
                <input type="text" class="is_required form-control" name="fkcustomers_ie" id="fkcustomers_ie" value="{if isset($smarty.post.fkcustomers_ie)}{$smarty.post.fkcustomers_ie}{/if}"/>
            </div>
        </div>

        {**** Campos hidden ***}
        <input type="hidden" class="text" name="cpf_cnpj" id="cpf_cnpj" value="{if isset($smarty.post.cpf_cnpj)}{$smarty.post.cpf_cnpj}{/if}"/>
        <input type="hidden" class="text" name="rg_ie" id="rg_ie" value="{if isset($smarty.post.rg_ie)}{$smarty.post.rg_ie}{/if}"/>

    </div>

    <div id="fkcustomers_page_subheading">
        <h3 class="page-subheading">{l s='Suas Informações Pessoais' mod='fkcustomers'}</h3>
    </div>

</div>
