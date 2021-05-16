
   <style>
    .types {
        display: none;
    }
   </style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>

    @if(isset($info->id))
        <h4 class="modal-title" id="modal_form_label">{{__cms('Редактирование')}}</h4>
    @else
        <h4 class="modal-title" id="modal_form_label">{{__cms('Создание')}}</h4>
    @endif
</div>
<div class="modal-body">

        <form id="form_page" class="smart-form" enctype="multipart/form-data" method="post" novalidate="novalidate" >
          <fieldset style="padding:0">
                <div class="row">
                  <section class="col" style="float: none">
                    <label class="label">{{__cms('Фраза')}}</label>
                    <div style="position: relative;">
                      <label class="input">
                          <input type="text"

                            value="{{ $info->phrase ?? ''}}"
                            name="phrase"
                            placeholder=""
                            class="dblclick-edit-input form-control input-sm unselectable"
                          </input>
                      </label>
                    </div>
                  </section>

                </div>

                  <div class="row">

                     @foreach($languages as $languageSlug => $language)
                         <section class="col" style="float: none">
                           <label class="label" for="title">{{$language->getName()}}</label>
                           <div style="position: relative;">
                             <label class="input">
                             <input type="text"
                               value=""
                               name="translation[{{$language->language}}]"
                               data-lang="{{$language->language}}"
                               placeholder=""
                               class="dblclick-edit-input form-control input-sm unselectable langs_input"
                               id="langs_input_{{$language->language}}"
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

            <input type="hidden" name="id" value="{{$info->id ?? '0'}}">
    </form>
</div>



 <script>

@if(!isset($info->id))
 $("#form_page [name=phrase]").keyup(function(){
    phrase = $(this).val();
    Trans.getTranslate(phrase);
 });
@endif;


//validation form
var $checkoutForm = $('#form_page').validate({
     submitHandler: function(form) {
         Trans.AddRec();
     },
    rules : {
        phrase : {
            required : true
        }
    },

    // Messages for form validation
    messages : {
        phrase : {
            required : 'Нужно заполнить фразу для перевода'
        }
    },
    // Do not change code below
    errorPlacement : function(error, element) {
        error.insertAfter(element.parent());
    }
});


 </script>
