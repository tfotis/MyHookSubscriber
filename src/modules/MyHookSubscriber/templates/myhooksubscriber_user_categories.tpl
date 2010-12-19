{gt text='Categories' assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

{insert name='getstatusmsg'}

{assign var="lang" value=$coredata.language}

<h1>{$templatetitle}</h1>

<ul>
    {foreach from=$categories item='category'}
    
    {if isset($category.display_name.$lang)}
    {assign var="cattitle" value=$category.display_name.$lang}
    {else}
    {assign var="cattitle" value=$category.name}
    {/if}

    <li><a href="{modurl modname='MyHookSubscriber' func='view' cid=$category.id}">{$cattitle|safetext}</a></li>
    {/foreach}
</ul>
