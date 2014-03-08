<div class="column-left">
  <!-- <div class="alert alert-info">Le tue modifiche saranno poste in coda sino a che il processo di revisione paritaria (peer review) avrà luogo. Ogni modifica, purché costruttiva, è benvenuta. Grazie.</div> -->
  {{ flash.output() }}

  <div id="page-title"><span>{{ doc.title }}</span></div>

  <form class="frm frm-aligned" action="{{ baseUri }}/accedi/" id="signinform" name="signinform" method="post" role="form">
    <fieldset>
      <div class="pure-control-group">
        <label for="revision">Revisione: </label>
        {{ select_static("revision", ["a", "b"], "class": "pure-input-1") }}
      </div>
      <div class="pure-control-group">
        <label for="title">Titolo: </label>
        {{ text_field("title", "placeholder": "Titolo", "class": "pure-input-1") }}
      </div>
    </fieldset>
    <fieldset>
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
          {{ text_area("body", "class": "pure-input-1") }}
        </div>
        <div class="notebook-page" id="preview">
        </div>
      </div>

      <div class="pure-controls">
        <button type="submit" name="signin" class="btn large red">Salva le modifiche</button>
        <a href="{{ controllerPath~doc.getUrl() }}" class="btn large">Annulla</a>
      </div>
    </fieldset>
  </form>

  <script>
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

</div> <!-- /column-left -->

<div class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</div> <!-- /column-right -->