
{assign var="class_tab_2" value=""}
{assign var="class_tab_3" value=""}

{if $fkcorreiosg2cp1['tabSelect'] == "2"}
    {assign var="class_tab_2" value="active"}
{elseif $fkcorreiosg2cp1['tabSelect'] == "3"}
    {assign var="class_tab_3" value="active"}
{else}
    {assign var="class_tab_2" value="active"}
{/if}

<ul class="nav nav-tabs" data-tabs="tabs">
    <li class="{$class_tab_2}"><a href="#tab_2" data-toggle="tab">{l s="Configuração geral" mod="fkcorreiosg2cp1"}</a></li>
    <li class="{$class_tab_3}"><a href="#tab_3" data-toggle="tab">{l s="Transportadoras" mod="fkcorreiosg2cp1"}</a></li>
</ul>
<div class="tab-content">

    <div class="tab-pane {$class_tab_2}" id="tab_2">
        {include file="{$fkcorreiosg2cp1['pathInclude']}{$tab_2['nameTpl']}"}
    </div>
    <div class="tab-pane {$class_tab_3}" id="tab_3">
        {include file="{$fkcorreiosg2cp1['pathInclude']}{$tab_3['nameTpl']}"}
    </div>

</div>


