{if $item.id eq null}
{gt text='Create item' assign='templatetitle'}
{else}
{gt text='Update item' assign='templatetitle'}
{/if}

{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">

    <div class="z-adminpageicon">
    {if $item.id eq null}
        {img modname='core' src='edit.png' set='icons/large' alt=$templatetitle}
    {else}
        {img modname='core' src='xedit.png' set='icons/large' alt=$templatetitle}
    {/if}
    </div>

    <h2>{$templatetitle}</h2>

    <form class="z-form" id="myhooksubscriber_admin_editform" action="{modurl modname='MyHookSubscriber' type='admin' func='edit'}" method="post" enctype="application/x-www-form-urlencoded">

        <div>

            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='MyHookSubscriber'}" />
            <input type="hidden" name="data[id]" value="{$item.id|safetext}" />

            <fieldset>
                <legend>{gt text='Content'}</legend>
                <div class="z-formrow">
                    <label for="myhooksubscriber_title">{gt text='Title'}</label>
                    <input id="myhooksubscriber_title" class="z-form-text" name="data[title]" type="text" size="32" maxlength="255" value="{$item.title|safehtml}" />
                </div>
            </fieldset>

            {if $modvars.MyHookSubscriber.enablecategorization}
            <fieldset>
                <legend>{gt text='Categories'}</legend>
                {foreach from=$catregistry key="property" item="category_id"}
                <div class="z-formrow">
                    <label for="myhooksubscriber_category_{$property}">{gt text='Category'}</label>
                    {selector_category category=$category_id name="data[Categories][$property][category_id]" selectedValue=$item.Categories.$property.category_id defaultValue=0 __defaultText="Choose category" editLink=false}
                    <input type="hidden" name="data[Categories][{$property}][reg_property]" value="{$property}" />
                </div>
                {/foreach}
            </fieldset>
            {/if}

            {notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.edit' area='modulehook_area.myhooksubscriber.mhs' subject=$item id=$item.id}

            {if $item.id neq null}
            <fieldset>
                <legend>{gt text='Meta data'}</legend>
                <ul>
                    {usergetvar name='uname' uid=$item.cr_uid assign='username'}
                    <li>{gt text='Created by %s' tag1=$username}</li>
                    <li>{gt text='Created on %s' tag1=$item.cr_date|dateformat}</li>
                    {usergetvar name='uname' uid=$item.lu_uid assign='username'}
                    <li>{gt text='Last update by %s' tag1=$username}</li>
                    <li>{gt text='Updated on %s' tag1=$item.lu_date|dateformat}</li>
                </ul>
            </fieldset>
            {/if}

            <div class="z-formbuttons z-buttons">
                {button src='button_ok.png' set='icons/extrasmall' __alt='Save' __title='Save' __text="Save"}
                <a href="{modurl modname='MyHookSubscriber' type='admin' func='view'}" title="{gt text='Cancel'}">{img modname='core' src='button_cancel.gif' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
            </div>

        </div>

    </form>

</div>
