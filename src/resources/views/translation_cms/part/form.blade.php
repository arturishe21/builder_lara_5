<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>

    @if(isset($info->id))
        <h4 class="modal-title" id="modal_form_label">{{__cms("Редактирование")}}</h4>
    @else
        <h4 class="modal-title" id="modal_form_label">{{__cms("Создание")}}</h4>
    @endif
</div>
<div class="modal-body">
    <form id="form_page" class="smart-form" enctype="multipart/form-data" method="post" novalidate="novalidate" >
      <fieldset style="padding:0">
            <div class="row">
              <section class="col" style="float: none">
                <label class="label">Фраза</label>
                <div style="position: relative;">
                  <label class="input">
                  <input type="text" value="{{{ $info->phrase ?? "" }}}" onkeyup="Trans.getTranslate(this.value)" name="phrase"  class="dblclick-edit-input form-control input-sm unselectable">
                  </label>
                </div>
              </section>
            </div>
            <div class="row">

                @foreach($langs as $languageSlug => $language)
                    <section class="col" style="float: none">
                        <label class="label" for="title">{{$language}}</label>
                        <div style="position: relative;">
                            <label class="input">
                                <input type="text"
                                       value=""
                                       name="translation[{{$languageSlug}}]"
                                       data-lang="{{$languageSlug}}"
                                       placeholder=""
                                       class="dblclick-edit-input form-control input-sm unselectable langs_input"
                                       id="langs_input_{{$languageSlug}}"
                                >
                            </label>
                        </div>
                    </section>
                @endforeach
            </div>

      </fieldset>
        <div class="modal-footer">
          <i class="fa fa-gear fa-41x fa-spin" style="display: none"></i>
          <button  type="submit" class="btn btn-success btn-sm"> <span class="glyphicon glyphicon-floppy-disk"></span> {{__cms('Сохранить')}} </button>
          <button type="button" class="btn btn-default" data-dismiss="modal"> {{__cms('Отмена')}} </button>
        </div>

        <input type="hidden" name="id" value="{{$info->id ?? "0"}}">
    </form>
</div>

