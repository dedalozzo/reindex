{% if subsectionMenu is defined  %}
  {% set path = arraycolumn(sectionMenu, 'path', 'name')[actionName] %}
<ul class="list pills small">
  {% for i, item in subsectionMenu %}
    <li{{ (i == subsectionIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="//{{ serverName~displayName~path~item }}">{{ item|minustospace|upper }}</a></li>
  {% endfor %}
</ul>
{% endif %}