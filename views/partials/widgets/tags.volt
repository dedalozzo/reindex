<ul class="list vertical recent-tags">
  <li>Tags recenti</li>
  {% for tag in recentTags %}
  <li><a class="tag" href="#">{{ tag[0] }}</a> × {{ tag[1] }}</li>
  {% endfor %}
</ul>