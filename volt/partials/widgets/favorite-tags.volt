{% if currentUser is defined %}
  {% set favoriteTags = currentUser.getFavoriteTags() %}
  {% if !(favoriteTags is empty) %}
  <ul class="list item-tags gutter-minus">
    <li class="title">Tags preferiti</li>
    {% for tag in favoriteTags %}
    <li><a class="tag" href="//{{ serverName }}/{{ tag['value'] }}/">{{ tag['value'] }}</a></li>
    {% endfor %}
  </ul>
  {% endif %}
{% endif %}