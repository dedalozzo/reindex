<ul class="list vertical gutter padding3">
  <li class="title">Tags recenti</li>
  {% for tag in recentTags %}
  <li><a class="tag" href="//{{ serverName }}/{{ tag[0] }}/">{{ tag[0] }}</a><span class="popularity"> × {{ tag[1] }}</span></li>
  {% endfor %}
</ul>