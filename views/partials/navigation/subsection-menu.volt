{% if subsectionMenu is defined  %}
  {% set actionPath = arraycolumn(sectionMenu, 'path', 'name')[actionName] %}
<ul class="list pills small">
  {% for i, itemName in subsectionMenu %}
    <li{{ (i == subsectionIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ serverName~resourcePath~actionPath~itemName }}">{{ itemName|minustospace|upper }}</a></li>
  {% endfor %}
</ul>
{% endif %}