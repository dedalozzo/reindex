{# This is used when a resource is provided, like a username or a post ID. #}
{% if resourceName is defined %}
  {% set resourcePath = resourceName~'/' %}
{% else %}
  {% set resourcePath = '' %}
{% endif %}

{# Menu #}
<ul class="list tabs">
  <li><span><b>{{ section }}</b></span></li>
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% if button is defined %}
  <li class="pull-right icon"><a href="http://programmazione.it" class="icon-plus icon-large"> {{ button }}</a></li>
  {% endif %}
  {% for name, path in menu %}
  <li{{ (name == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~'/'~section~'/'~resourcePath~path }}/">{{ path|minustospace }}</a></li>
  {% endfor %}
</ul>