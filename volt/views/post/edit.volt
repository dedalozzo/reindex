{% extends "templates/base.volt" %}

{% block content %}
<div id="content">
  <div id="page-title">{{ post.title }}</div>
  <hr class="fade-long">

  <div class="column-left">
    <!-- <div class="alert alert-info">Le tue modifiche saranno poste in coda sino a che il processo di revisione paritaria (peer review) avrà luogo. Ogni modifica, purché costruttiva, è benvenuta. Grazie.</div> -->
    {{ flash.output() }}

    <form action="//{{ domainName }}/accedi/" id="signinform" name="signinform" method="post" role="form">

      <ul class="list vertical mbottom10 gutter">
        <li>
          <select name="version" id="select-version">
            <option value="">Seleziona la versione...</option>
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
            $('#select-version').selectize({
              sortField: {
                field: 'text',
                direction: 'asc'
              },
              dropdownParent: 'body'
            });
          </script>
          <label>{{ validation.first("email") }}</label>
        </li>
        <li>
          {{ text_field("title", "placeholder": "Titolo") }}
          <label>{{ validation.first("title") }}</label>
        </li>
      </ul>

      <ul class="list tabs">
        <li><span><b>Corpo dell'articolo</b></span></li>
        <li class="pull-right"><a href="#preview" data-toggle="tab">ANTEPRIMA</a></li>
        <li class="active pull-right"><a href="#markdown" data-toggle="tab">MARKDOWN</a></li>
      </ul>
      <div class="notebook gutter">
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
            var editor = CodeMirror.fromTextArea(document.getElementById("body"), {
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

      <ul class="list vertical mbottom10 gutter">
        <li>
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
        </li>
        <li>
          {{ text_field("summary", "placeholder": "Breve descrizione delle modifiche") }}
          <label>{{ validation.first("summary") }}</label>
        </li>
      </ul>

      <ul class="list btn-list gutter">
        <li class="pull-right"><a href="//{{ serverName~post.getHref() }}" class="btn">ANNULLA</a></li>
        <li class="pull-right"><button type="submit" name="signin" class="btn red">SALVA LE MODIFICHE</button></li>
      </ul>

    </form>

  </div> <!-- column-left -->

  <aside class="column-right">
  </aside> <!-- column-right -->

</div>
{% endblock %}