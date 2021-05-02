@if (count($list))
    @foreach($list as $img)
        <?php
        $baseImg =  basename($img);
        $imgParam = getimagesize(public_path('storage/editor/fotos/' . basename($img)));
        ?>

        <div class="one_img_uploaded is_wrapper" onclick="TableBuilder.selectImgInStorage($(this))">
            <div class="one_img_uploaded_content">
                <img src="{{glide('/storage/editor/fotos/'. basename($img), ['w'=>100, 'h' => 100])}}" data-path = '/storage/editor/fotos/{{basename($img)}}'>
            </div>
            <div class="one_img_uploaded_label">
                @if (isset($imgParam[0]) && $imgParam[1])
                    {{$imgParam[0].'x'.$imgParam[1]}}
                @endif
            </div>
        </div>

    @endforeach

    <div style="text-align: center" class="paginator_pictures">
        {{ $list->appends(request()->all())->render() }}
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

    </script>

@endif
