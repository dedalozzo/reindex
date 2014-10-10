{% set favoriteTags = currentUser.getFavoriteTags() %}
{% if !(favoriteTags is empty) %}
<div class="title">Tags preferiti</div>
<ul class="list gutter item-tags">
  {% for tag in favoriteTags %}
  <li><a class="tag" href="//{{ serverName }}/{{ tag['value'] }}/">{{ tag['value'] }}</a></li>
  {% endfor %}
</ul>
{% endif %}