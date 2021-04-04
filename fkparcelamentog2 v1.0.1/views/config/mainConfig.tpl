
{assign var="class_tab_1" value=""}
{assign var="class_tab_2" value=""}
{assign var="class_tab_3" value=""}

{if $tabSelect == "2"}
    {assign var="class_tab_2" value="active"}
{elseif $tabSelect == "3"}
    {assign var="class_tab_3" value="active"}
{else}
    {assign var="class_tab_1" value="active"}
{/if}

<ul class="nav nav-tabs" data-tabs="tabs">
    <li class="{$class_tab_1}"><a href="#tab_1" data-toggle="tab">{l s="Configuração Geral" mod="fkparcelamentog2"}</a></li>
    <li class="{$class_tab_2}"><a href="#tab_2" data-toggle="tab">{l s="Parcelamento 1" mod="fkparcelamentog2"}</a></li>
    <li class="{$class_tab_3}"><a href="#tab_3" data-toggle="tab">{l s="Parcelamento 2" mod="fkparcelamentog2"}</a></li>
</ul>
<div class="tab-content">

    <div class="tab-pane {$class_tab_1}" id="tab_1">
        {include file="{$pathInclude}{$tab_1['nameTpl']}"}
    </div>
    <div class="tab-pane {$class_tab_2}" id="tab_2">
        {include file="{$pathInclude}{$tab_2['nameTpl']}"}
    </div>
    <div class="tab-pane {$class_tab_3}" id="tab_3">
        {include file="{$pathInclude}{$tab_3['nameTpl']}"}
    </div>

</div>


