{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='configure.gif' set='icons/large' alt=''}</div>

    <h2>{gt text='Settings'}</h2>

    <form class="z-form" action="{modurl modname='MyHookSubscriber' type='admin' func='settings'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='MyHookSubscriber'}" />

            <fieldset>
                <legend>{gt text='General settings'}</legend>
                <div class="z-formrow">
                    <label for="myhooksubscriber_itemsperpage">{gt text='Items per page'}</label>
                    <input id="myhooksubscriber_itemsperpage" type="text" name="settings[itemsperpage]" size="3" value="{$modvars.MyHookSubscriber.itemsperpage|safetext}" />
                </div>
            </fieldset>

            <div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Save' __title='Save'}
                <a href="{modurl modname='MyHookSubscriber' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
