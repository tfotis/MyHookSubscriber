{gt text='Update item' assign='templatetitle'}

{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='edit.gif' set='icons/large' alt=$templatetitle}</div>

    <h2>{$templatetitle}</h2>

    <form id="myhooksubscriber_admin_modifyform" class="z-form" action="{modurl modname='MyHookSubscriber' type='admin' func='update'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="url" value="{$item.returnurl|safetext}" />
            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='MyHookSubscriber'}" />
            <input type="hidden" name="data[id]" value="{$item.id|safetext}" />

            <fieldset>
                <legend>{gt text='Content'}</legend>
                <div class="z-formrow">
                    <label for="myhooksubscriber_title">{gt text='Title'}</label>
                    <input id="myhooksubscriber_title" class="z-form-text" name="data[title]" type="text" size="32" maxlength="255" value="{$item.title|safehtml}" />
                </div>
            </fieldset>

            {notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.edit' subject=$item id=$item.id}

            <fieldset class="z-formrow">
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
            
            <div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Update' __title='Update'}
                <a href="{modurl modname='MyHookSubscriber' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>

