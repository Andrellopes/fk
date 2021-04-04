
{assign var="class_tab_2" value=""}
{assign var="class_tab_3" value=""}
{assign var="class_tab_4" value=""}

{if $tabSelect == "2"}
    {assign var="class_tab_2" value="active"}
{elseif $tabSelect == "3"}
    {assign var="class_tab_3" value="active"}
{elseif $tabSelect == "4"}
    {assign var="class_tab_4" value="active"}
{else}
    {assign var="class_tab_2" value="active"}
{/if}

<ul class="nav nav-tabs" data-tabs="tabs">
    <li class="{$class_tab_2}"><a href="#tab_2" data-toggle="tab">{l s="Home" mod="fkmessenger"}</a></li>
    <li class="{$class_tab_3}"><a href="#tab_3" data-toggle="tab">{l s="Detalhes Produto" mod="fkmessenger"}</a></li>
    <li class="{$class_tab_4}"><a href="#tab_4" data-toggle="tab">{l s="Carrinho" mod="fkmessenger"}</a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane {$class_tab_2}" id="tab_2">
        {include file="{$pathInclude}{$tab_2['nameTpl']}"}
    </div>
    <div class="tab-pane {$class_tab_3}" id="tab_3">
        {include file="{$pathInclude}{$tab_3['nameTpl']}"}
    </div>
    <div class="tab-pane {$class_tab_4}" id="tab_4">
        {include file="{$pathInclude}{$tab_4['nameTpl']}"}
    </div>

</div>

<script type="text/javascript">
    var ad = "{$path_adm}";
    var pathCSS = "{$path_css}";
    var iso = "{$language}";
</script>


