{# Submenu #}
{% if submenu is defined  %}
  {% set actionPath = menu[actionName] %}
  <ul class="list pills small">
    {% for filterPath, value in submenu %}
      <li{{ (value == submenuIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~'/'~controllerPath~actionPath~'/'~filterPath }}/">{{ filterPath|minustospace }}</a></li>
    {% endfor %}
  </ul>
{% endif %}