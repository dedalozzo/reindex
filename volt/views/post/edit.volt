{% extends "templates/base.volt" %}

{% block content %}
<div id="content">
  <div class="column-left">
    <!-- <div class="alert alert-info">Le tue modifiche saranno poste in coda sino a che il processo di revisione paritaria (peer review) avrà luogo. Ogni modifica, purché costruttiva, è benvenuta. Grazie.</div> -->
    {{ flash.output() }}

    <div id="page-title">{{ post.title }}</div>

    <form class="frm-stacked" action="//{{ domainName }}/accedi/" id="signinform" name="signinform" method="post" role="form">
      <fieldset>
        <label for="revision">Revisione: </label>
        <select name="revision" id="select-nation" placeholder="Nazione...">
          <option value="">Seleziona nazione...</option>
          <optgroup label="North America">
            <option value="1">USA</option>
            <option value="9">Canada</option>
          </optgroup>
          <optgroup label="Europe">
            <option value="2">France</option>
            <option value="3">Spain</option>
            <option value="6">Bulgaria</option>
            <option value="7" disabled="disabled">Greece</option>
            <option value="8">Italy</option>
          </optgroup>
          <optgroup label="Asia" disabled="disabled">
            <option value="5">Japan</option>
            <option value="11">China</option>
          </optgroup>
          <option value="4">Brazil</option>
          <option value="10">South Africa</option>
        </select>
        <script>
          $('#select-nation').selectize({
            sortField: {
              field: 'text',
              direction: 'asc'
            },
            dropdownParent: 'body'
          });
        </script>
        <label for="title">Titolo: </label>
        <input type="text" style="width: 100%;" placeholder="Titolo" autocomplete="on" id="keyword" name="keyword" value="{{ post.title }}">
      </fieldset>
      <fieldset>
        <ul class="list tabs">
          <li><span><b>Corpo dell'articolo</b></span></li>
          <li class="pull-right"><a href="#preview" data-toggle="tab">ANTEPRIMA</a></li>
          <li class="active pull-right"><a href="#markdown" data-toggle="tab">MARKDOWN</a></li>
        </ul>
        <div class="notebook">
          <div class="notebook-page active" id="markdown">
            <ul class="list toolbar">
              <li class="toolgroup break">
                <a href="#" title="Grassetto"><i class="icon-bold"></i></a>
                <a href="#" title="Corsivo"><i class="icon-italic"></i></a>
              </li>
              <li class="toolgroup break">
                <a href="#" title="Aggiungi un link"><i class="icon-link"></i></a>
                <a href="#" title="Aggiungi un'immagine"><i class="icon-picture"></i></a>
              </li>
              <li class="toolgroup break">
                <a href="#" title="Quota una parte di testo"><i class="icon-angle-right"></i></a>
                <a href="#" title="Aggiungi un blocco di codice"><i class="icon-code"></i></a>
              </li>
              <li class="toolgroup">
                <a href="#" title="Aggiungi ai preferiti"><i class="icon-ellipsis-horizontal"></i></a>
                <a href="#" title="Lista puntata"><i class="icon-list-ul"></i></a>
                <a href="#" title="Lista numerata"><i class="icon-list-ol"></i></a>
              </li>
            </ul>
            {{ text_area("body", "class": "pure-input-1") }}
            <script type="text/javascript">
              var editor = CodeMirror.fromTextArea(postument.getElementById("body"), {
                mode: 'gfm',
                lineNumbers: true,
                lineWrapping: true,
                theme: "default",
                viewportMargin: Infinity
              });

              var charWidth = editor.defaultCharWidth(), basePadding = 4;
              editor.on("renderLine", function(cm, line, elt) {
                var off = CodeMirror.countColumn(line.text, null, cm.getOption("tabSize")) * charWidth;
                elt.style.textIndent = "-" + off + "px";
                elt.style.paddingLeft = (basePadding + off) + "px";
              });

              editor.refresh();
            </script>
          </div>
          <div class="notebook-page" id="preview">
          </div>
        </div>
      </fieldset>
      <fieldset>
        <label for="tags">Tags: </label>
        <select id="tags" placeholder="Seleziona alcuni tags..."></select>
        <script>
          $('#tags').selectize({
            plugins: ['remove_button'],
            persist: false,
            create: true,
            //theme: 'links',
            maxItems: null,
            valueField: 'id',
            searchField: 'title',
            options: [
              {id: 1, title: 'php', url: 'https://diy.org'},
              {id: 2, title: 'java', url: 'http://google.com'},
              {id: 3, title: 'c#', url: 'http://yahoo.com'}
            ],
            render: {
              option: function(data, escape) {
                return '<div class="option">' +
                '<span class="title">' + escape(data.title) + '</span>' +
                '</div>';
              },
              item: function(data, escape) {
                return '<div class="item"><a class="tag" href="' + escape(data.url) + '">' + escape(data.title) + '</a></div>';
              }
            },
            create: function(input) {
              return {
                id: 0,
                title: input,
                url: '#'
              };
            }
          });
        </script>
      </fieldset>

      <div class="pure-controls">
        <button type="submit" name="signin" class="btn large red">Salva le modifiche</button>
        <a href="//{{ serverName~post.getHref() }}" class="btn large">Annulla</a>
      </div>
    </form>

  </div> <!-- /column-left -->

  <aside class="column-right">

  <div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

  </aside> <!-- /column-right -->
</div>
{% endblock %}