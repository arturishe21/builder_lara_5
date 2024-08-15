<div class="smart-form" style="padding: 0 20px">
    <div class="row filter_gallary_images" style="padding-top: 10px">
        <section  class="col col-4">
            <label class="input">
                <input type="text" value="{{request('q')}}" name="q" placeholder="Введите название картинки">
            </label>
        </section>
        <section class="col col-4">
            <label class="select">
                <select name="id_gallery" onchange="TableBuilder.changeGalleryAndTags($(this))">
                    <option value="">Выбрать галерею</option>
                     @foreach($galleries as $gallery)
                        <option value="{{$gallery->id}}" {{request('gallary') == $gallery->id ? 'selected' : ''}}>{{$gallery->title}}</option>
                     @endforeach
                </select>
                <i></i>
            </label>
        </section>

        <section class="col col-4">
            <label class="select">
                <select name="id_tag" onchange="TableBuilder.changeGalleryAndTags($(this))">
                    <option value="">Выбрать тег</option>
                    @foreach($tags as $tag)
                        <option value="{{$tag->id}}" {{request('tag') == $tag->id ? 'selected' : ''}}>{{$tag->title}}</option>
                    @endforeach
                </select>
                <i></i>
            </label>
        </section>
        <input type="hidden" value="{{request('ident')}}" name="ident">

    </div>
</div>

    @forelse($list as $img)

            <?php
            try {
                $imgParam = getimagesize(public_path($img->file_folder . $img->file_source));
            } catch (Exception $e) {
                $imgParam = [];
            }

            ?>

            <div class="one_img_uploaded is_wrapper <?=strpos($img->file_source, '.svg') ? 'transparent-image' : '' ?>" onclick="TableBuilder.selectImgInStorage($(this))">
                <div class="one_img_uploaded_content">
                    <img src="{{glide($img->file_folder . $img->file_source, ['w'=>100, 'h' => 100])}}"
                         data-path = '/{{trim($img->file_folder . $img->file_source, '/')}}'
                      >
                </div>
                <div class="one_img_uploaded_label">
                    @if (isset($imgParam[0]) && $imgParam[1])
                        {{$imgParam[0].'x'.$imgParam[1]}}
                    @endif
                </div>
            </div>

    @empty
        <div style="text-align: center; padding: 50px">Нет изображений</div>
    @endforelse
    <div style="text-align: center" class="paginator_pictures">
        {{ $list->appends(request()->all())->links() }}
    </div>


    <style>

        .one_img_uploaded img {
            width: auto;
            height: auto;
        }
        .one_img_uploaded.is_wrapper {
            width: 100px;
            height: 125px;
            flex-flow: column;
            align-items: center;
            justify-content: center;
        }
        .one_img_uploaded_content {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .one_img_uploaded_label {
            padding: 5px 0px;
            font-size: 14px;
            line-height: 1;
        }
    </style>

    <script>

        $(".paginator_pictures a").click(function(e) {
            var href = $(this).attr('href');
            e.preventDefault();
            var section = $(this).parents('tbody');
            $.post(href,
                function(response){
                    section.html(response.data);
            });
        });

        $('[name=q]').keyup(function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);

            if (code==13) {
                e.preventDefault();
                TableBuilder.changeGalleryAndTags($('.filter_gallary_images [name=q]'));
            }
        });
    </script>
