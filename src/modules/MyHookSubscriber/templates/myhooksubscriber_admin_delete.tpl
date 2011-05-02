{gt text='Delete item' assign='templatetitle'}

{include file='myhooksubscriber_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='editdelete.png' set='icons/large' alt=$templatetitle}</div>

    <h2>{$templatetitle}</h2>

    <p class="z-warningmsg">{gt text='Do you really want to delete this item?'}</p>

    <form class="z-form" action="{modurl modname='MyHookSubscriber' type='admin' func='delete'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="authid" value="{insert name='generateauthkey' module='MyHookSubscriber'}" />
            <input type="hidden" name="confirmation" value="1" />
            <input type="hidden" name="id" value="{$item.id|safetext}" />

            {notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.delete' subject=$item id=$item.id}

            <div class="z-formbuttons">
                {button src='button_ok.png' set='icons/small' __alt='Confirm deletion?' __title='Confirm deletion?'}
                <a href="{modurl modname=MyHookSubscriber type=admin func=view}">{img modname='core' src='button_cancel.gif' set='icons/small'  __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
