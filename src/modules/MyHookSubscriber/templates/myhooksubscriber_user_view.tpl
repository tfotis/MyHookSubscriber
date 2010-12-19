{gt text='Items list' assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

{insert name='getstatusmsg'}

{if isset($category)}
    {assign var="lang" value=$coredata.language}
    {if isset($category.display_name.$lang)}
        {assign var="cattitle" value=$category.display_name.$lang}
    {else}
        {assign var="cattitle" value=$category.name}
    {/if}
    {assign var="cattitle" value="`$cattitle` - "}
{else}
    {assign var="cattitle" value=""}
{/if}

<h1>{$cattitle}{$templatetitle}</h1>

<ul>
    {foreach from=$data item='dt'}
    <li><a href="{modurl modname='MyHookSubscriber' func='display' id=$dt.id}">{$dt.title|safehtml}</a></li>
    
    {foreachelse}
    <li>{gt text='No items found.'}</li>
    
    {/foreach}
</ul>

{pager rowcount=$pager.numitems limit=$pager.limit posvar='offset' maxpages='10' optimize=true}
