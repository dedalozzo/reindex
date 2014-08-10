{# Submenu #}
{% if submenu is defined  %}
  {% set actionPath = menu[actionName] %}
  <ul class="list pills small">
    {% for path, value in submenu %}
      <li{{ (value == submenuIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ domainName~'/'~section~'/'~resourcePath~actionPath~'/'~path }}/">{{ path|minustospace }}</a></li>
    {% endfor %}
  </ul>
{% endif %}