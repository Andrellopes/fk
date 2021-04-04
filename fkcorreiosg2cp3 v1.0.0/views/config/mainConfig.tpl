
{assign var="class_tab_2" value=""}

{if $fkcorreiosg2cp3['tabSelect'] == "2"}
    {assign var="class_tab_2" value="active"}
{else}
    {assign var="class_tab_2" value="active"}
{/if}

<ul class="nav nav-tabs" data-tabs="tabs">
    <li class="{$class_tab_2}"><a href="#tab_2" data-toggle="tab">{l s="Transportadora" mod="fkcorreiosg2cp3"}</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane {$class_tab_2}" id="tab_2">
        {include file="{$fkcorreiosg2cp3['pathInclude']}{$tab_2['nameTpl']}"}
    </div>
</div>


