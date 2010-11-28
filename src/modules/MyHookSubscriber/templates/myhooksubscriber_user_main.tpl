{gt text='Items' assign=templatetitle}
{pagesetvar name='title' value=$templatetitle}

{insert name='getstatusmsg'}

{modfunc modname='MyHookSubscriber' func='view'}
