{pagesetvar name='title' value=$item.title}
{insert name='getstatusmsg'}

<h2>{$item.title|safehtml}</h2>

{notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.view' area='modulehook_area.myhooksubscriber.mhs' subject=$item id=$item.id}
