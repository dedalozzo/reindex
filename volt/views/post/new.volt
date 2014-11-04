{% extends "templates/base.volt" %}

{% block content %}
  <div id="content">
    <div id="page-title">Nuovo articolo</div>
    <hr class="fade-long">

    <div class="column-left expanded">
      <!-- <div class="alert alert-info">Le tue modifiche saranno poste in coda sino a che il processo di revisione paritaria (peer review) avrà luogo. Ogni modifica, purché costruttiva, è benvenuta. Grazie.</div> -->
      {{ flash.output() }}

      <form action="//{{ domainName }}/nuovo/articolo/" id="articlefrm" name="articlefrm" method="post" role="form">

        <div class="gutter">
          {{ text_field("title", "placeholder": "Titolo") }}
          <span class="error">{{ validation.first("title") }}</span>
        </div>

        <div class="gutter">
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
              {{ text_area("body") }}
              <script type="text/javascript">
                var minLines = 19;
                var startingValue = '';
                for (var i = 0; i < minLines; i++) {
                  startingValue += '\n';
                }

                var editor = CodeMirror.fromTextArea(document.getElementById("body"), {
                  mode: 'gfm',
                  lineNumbers: true,
                  lineWrapping: true,
                  theme: "default",
                  value: startingValue,
                  viewportMargin: Infinity
                });

                editor.setValue(startingValue);

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
          <span class="error">{{ validation.first("body") }}</span>
        </div>

        <div class="gutter">
          <select id="tags" name="tags[]" placeholder="Seleziona alcuni tags..."></select>
          <span class="error">{{ validation.first("tags[]") }}</span>
          <script>
            $('#tags').selectize({
              plugins: ['remove_button'],
              persist: false,
              //create: true,
              create: false,
              //theme: 'links',
              maxItems: null,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              options: [],
              render: {
                option: function(tag, escape) {
                  return '<ul class="list vertical mini-gutter">' +
                           '<li>' +
                             '<span class="tag hoverless">' + escape(tag.name) + '</span>' +
                             '<span class="popularity"> × ' + tag.postsCount + '</span>' +
                           '</li>' +
                           '<li class="excerpt">' + escape(tag.excerpt || 'Nessuna descrizione disponibile.') + '</li>' +
                         '</ul>';
                },
                item: function(tag, escape) {
                  return '<div class="item"><a class="tag" href="' + '//{{ domainName }}/' + escape(tag.name) + '/">' + escape(tag.name) + '</a></div>';
                }
              },
              /*
              create: function(input) {
                return {
                  id: 0,
                  title: input,
                  url: '#'
                };
              },
              */
              load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                  url: 'http://programmazione.me/tags/filtra/',
                  type: 'POST',
                  dataType: 'json',
                  data: {
                    filter: query,
                    page_limit: 10
                  },
                  error: function() {
                    callback();
                  },
                  success: function(res) {
                    callback(res);
                  }
                });
              }
            });
          </script>
        </div>

        <ul class="list btn-list gutter">
          <li class="pull-right"><a class="btn" onclick="window.history.back();">ANNULLA</a></li>
          <li class="pull-right"><button type="submit" name="signin" class="btn red">SALVA LE MODIFICHE</button></li>
        </ul>

      </form>

    </div> <!-- column-left -->

    <aside class="column-right compressed">
      {% include "partials/notes/formatting-rules.volt" %}
      {% include "partials/notes/tags-usage.volt" %}
    </aside> <!-- column-right -->

  </div>
{% endblock %}