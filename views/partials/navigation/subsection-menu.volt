{% if subsectionMenu is defined  %}
<ul class="list pills small">
  {% for i, item in subsectionMenu %}
    <li{{ (i == subsectionIndex) ? ' class="active pull-right"' : ' class="pull-right"' }}><a href="{{ controllerPath~actionPath~item }}">{{ item|minustospace|upper }}</a></li>
  {% endfor %}
</ul>
{% endif %}