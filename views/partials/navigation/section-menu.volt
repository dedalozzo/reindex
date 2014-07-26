{% set titles = arraycolumn(sectionMenu, 'title', 'name') %}
{% if titles[actionName] is defined %}
  {% set title = titles[actionName] %}
{% endif %}

{% if resourceName is defined %}
  {% set resourcePath = '/'~resourceName %}
{% else %}
  {% set resourcePath = '' %}
{% endif %}

<ul class="list tabs">
{% if controllerName == 'index' %}
  {% set sectionName = controllerName %}
  <li>
    <ul class="list categories no-gutter">
      {% for item in categories %}
        <li><a class="tag {{ item['type'] }}" href="//{{ domainName~item['path'] }}">{{ item['label'] }}</a></li>
      {% endfor %}
    </ul>
  </li>
{% else %}
  <li><span><b>{{ sectionLabel }}</b></span></li>
{% endif %}
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% for item in sectionMenu %}
    <li{{ (item['name'] == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ serverName~resourcePath~item['path'] }}">{{ item['label'] }}</a></li>
  {% endfor %}
</ul>