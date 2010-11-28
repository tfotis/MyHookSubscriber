{gt text='Create item' assign='templatetitle'}

{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='filenew.gif' set='icons/large' alt=$templatetitle}</div>

    <h2>{$templatetitle}</h2>

    <form id="myhooksubscriber_admin_newform" class="z-form" action="{modurl modname='MyHookSubscriber' type='admin' func='create'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='MyHookSubscriber'}" />

            <fieldset>
                <legend>{gt text='Content'}</legend>
                <div class="z-formrow">
                    <label for="myhooksubscriber_title">{gt text='Title'}</label>
                    <input id="myhooksubscriber_title" class="z-form-text" name="data[title]" type="text" size="32" maxlength="255" />
                </div>
            </fieldset>

            {notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.edit' subject=null id=null}

            <div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Create' __title='Create'}
                <a href="{modurl modname='MyHookSubscriber' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
