{gt text='Items list' assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

{insert name='getstatusmsg'}

<ul>
    {foreach from=$data item='dt'}
    <li><a href="{modurl modname='MyHookSubscriber' func='display' id=$dt.id}">{$dt.title|safehtml}</a></li>
    {/foreach}
</ul>

{pager rowcount=$pager.numitems limit=$pager.limit posvar='offset' maxpages='10' optimize=true}
