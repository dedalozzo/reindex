<div class="tab-content" id="updates">
  <ul class="list tabs no-gutter">
    <li class="active"><a href="#articles" data-toggle="tab">ARTICOLI</a></li>
    <li><a href="#tutorials" data-toggle="tab">GUIDE</a></li>
    <li><a href="#books" data-toggle="tab">LIBRI</a></li>
  </ul>
  <div class="tab-pane active" id="articles">
    <ul class="items">
      {% for article in articles %}
        <li>
          <a href="#">
            <div><span class="icon-thumbs-up" />&nbsp;{{ article.score }}&nbsp;&nbsp;<span class="icon-comments" />&nbsp;5</div>
            <div>{{ article.whenHasBeenPublished }}</div>
            {{ article.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  <div class="tab-pane" id="tutorials">
    <ul class="items">
      {% for tutorial in tutorials %}
        <li>
          <a href="#">
            <div><span class="icon-thumbs-up" />&nbsp;{{ tutorial.score }}&nbsp;&nbsp;<span class="icon-comments" />&nbsp;5</div>
            <div>{{ tutorial.whenHasBeenPublished }}</div>
            {{ tutorial.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
  <div class="tab-pane" id="books">
    <ul class="items">
      {% for book in books %}
        <li>
          <a href="#">
            <div><span class="icon-thumbs-up" />&nbsp;{{ book.score }}&nbsp;&nbsp;<span class="icon-comments" />&nbsp;5</div>
            <div>{{ book.whenHasBeenPublished }}</div>
            {{ book.title }}
          </a>
        </li>
      {% endfor %}
    </ul>
  </div>
</div>