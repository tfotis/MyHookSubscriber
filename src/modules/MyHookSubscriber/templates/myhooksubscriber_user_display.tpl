{pagesetvar name='title' value=$item.title}
{insert name='getstatusmsg'}

<h2>{$item.title|safehtml}</h2>

{notifydisplayhooks eventname='myhooksubscriber.ui_hooks.mhs.display_view' id=$item.id}
