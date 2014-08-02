{# This is used when a resource is provided, like a username or a post id. #}
{% if resourceName is defined %}
  {% set resourcePath = resourceName~'/' %}
{% else %}
  {% set resourcePath = '' %}
{% endif %}

{# Menu #}
<ul class="list tabs">
  {% if sectionName == 'home' %}
    {% set controllerPath = '/'~types[controllerName]~'/' %}
  <li>
    <ul class="list types no-gutter">
    {% for name, path in types %}
      <li><a class="tag {{ name }}" href="//{{ domainName~'/'~path }}/">{{ path }}</a></li>
    {% endfor %}
    </ul>
  </li>
  {% else %}
    {% set controllerPath = sections[sectionName]['path'] %}
  <li><span><b>{{ sectionLabel }}</b></span></li>
  {% endif %}
  <li class="pull-right icon"><a href="http://programmazione.it/rss" class="icon-rss icon-large"></a></li>
  {% for name, path in menu %}
  <li{{ (name == actionName) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~controllerPath~resourcePath~path }}/">{{ path|minustospace }}</a></li>
  {% endfor %}
</ul>

{# Submenu #}
{% if submenu is defined  %}
  {% set actionPath = menu[actionName] %}
<ul class="list pills small">
  {% for path, value in submenu %}
  <li{{ (value == submenuIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~controllerPath~resourcePath~actionPath~'/'~path }}/">{{ path|minustospace }}</a></li>
  {% endfor %}
</ul>
{% endif %}