{pagesetvar name='title' value=$item.title}
{insert name='getstatusmsg'}

<h2>{$item.title|safehtml}</h2>

{notifydisplayhooks eventname='myhooksubscriber.hook.mhs.ui.view' subject=$item id=$item.id}
