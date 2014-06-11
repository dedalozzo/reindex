<section class="notebook" id="updates">
  <ul class="list tabs no-gutter">
    <li class="active"><a href="#articles" data-toggle="tab">ARTICOLI</a></li>
    <li><a href="#books" data-toggle="tab">LIBRI</a></li>
  </ul>
  {% if articles is defined %}
  <div class="notebook-page active" id="articles">
    <ul class="items">
      {% for article in articles %}
        <li>
          <a href="{{ article.url }}">
            <div><i class="icon-thumbs-up"></i>&nbsp;{{ article.score }}&nbsp;&nbsp;<i class="icon-comments"></i>&nbsp;{{ article.repliesCount }}</div>
            <div>{{ article.whenHasBeenPublished }}</div>
            {{ article.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  {% endif %}
  {% if books is defined %}
  <div class="notebook-page" id="books">
    <ul class="items">
      {% for book in books %}
        <li>
          <a href="{{ book.url }}">
            <div><i class="icon-thumbs-up"></i>&nbsp;{{ book.score }}&nbsp;&nbsp;<i class="icon-comments"></i>&nbsp;{{ book.repliesCount }}</div>
            <div>{{ book.whenHasBeenPublished }}</div>
            {{ book.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  {% endif %}
</section>