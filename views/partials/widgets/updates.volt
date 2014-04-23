<section class="notebook" id="updates">
  <ul class="list tabs no-gutter">
    <li class="active"><a href="#articles" data-toggle="tab">ARTICOLI</a></li>
    <li><a href="#tutorials" data-toggle="tab">GUIDE</a></li>
    <li><a href="#books" data-toggle="tab">LIBRI</a></li>
  </ul>
  {% if articles is defined %}
  <div class="notebook-page active" id="articles">
    <ul class="items">
      {% for article in articles %}
        <li>
          <a href="{{ article.url }}">
            <div><i class="icon-thumbs-up"></i>&nbsp;{{ article.score }}&nbsp;&nbsp;<i class="icon-comments"></i>&nbsp;{{ article.replaysCount }}</div>
            <div>{{ article.whenHasBeenPublished }}</div>
            {{ article.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  {% endif %}
  {% if tutorials is defined %}
  <div class="notebook-page" id="tutorials">
    <ul class="items">
      {% for tutorial in tutorials %}
        <li>
          <a href="{{ tutorial.url }}">
            <div><i class="icon-thumbs-up"></i>&nbsp;{{ tutorial.score }}&nbsp;&nbsp;<i class="icon-comments"></i>&nbsp;{{ tutorial.replaysCount }}</div>
            <div>{{ tutorial.whenHasBeenPublished }}</div>
            {{ tutorial.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  {% endif %}
  {% if tutorials is defined %}
  <div class="notebook-page" id="books">
    <ul class="items">
      {% for book in books %}
        <li>
          <a href="{{ book.url }}">
            <div><i class="icon-thumbs-up"></i>&nbsp;{{ book.score }}&nbsp;&nbsp;<i class="icon-comments"></i>&nbsp;{{ book.replaysCount }}</div>
            <div>{{ book.whenHasBeenPublished }}</div>
            {{ book.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  {% endif %}
</section>