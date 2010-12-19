{gt text='View items list' assign='templatetitle'}

{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='windowlist.gif' set='icons/large' alt=$templatetitle}</div>

    <h2>{$templatetitle}</h2>

    {assign var='colspan' value=3}

    <table class="z-admintable">
        <thead>
            <tr>
                <th>{gt text='ID'}</th>
                <th>{gt text='Title'}</th>
                {if $modvars.MyHookSubscriber.enablecategorization}
                <th>{gt text='Category'}</th>
                {assign var='colspan' value=$colspan+1}
                {/if}
                <th>{gt text='Actions'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$data item='dt'}
            <tr class="{cycle values='z-odd,z-even'}">
                <td>{$dt.id|safehtml}</td>
                <td>{$dt.title|safehtml}</td>
                {if $modvars.MyHookSubscriber.enablecategorization}
                <td>{assignedcategorieslist item=$dt}</td>
                {/if}
                <td>
                    {assign var='options' value=$dt.options}
                    {section name='options' loop=$options}
                    <a href="{$options[options].url|safetext}">{img modname='core' set='icons/extrasmall' src=$options[options].image title=$options[options].title alt=$options[options].title}</a>
                    {/section}
                </td>
            </tr>
            {foreachelse}
            <tr class="z-admintableempty">
                <td colspan="{$colspan}">{gt text='No items found.'}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>

    {pager rowcount=$pager.numitems limit=$pager.limit posvar='offset' maxpages='10' optimize=true}
</div>
