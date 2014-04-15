{% set titles = arraycolumn(sectionMenu, 'title', 'name') %}
{% if titles[actionName] is defined %}
  {% set title = titles[actionName] %}
{% endif %}
<ul class="list tabs">
  <li><span><b>{{ sectionLabel }}</b></span></li>
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% for item in sectionMenu %}
    <li{{ (item['name'] == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ serverName~item['path'] }}">{{ item['label'] }}</a></li>
  {% endfor %}
</ul>