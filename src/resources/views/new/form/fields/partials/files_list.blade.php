@if (count($list))
    @foreach($list as $file)
        <tr>
            <td>
                <label class="checkbox">
                    <input data-basename = "{{basename($file)}}" value="/storage/files/{{basename($file)}}" type="checkbox">
                    <i></i>
                </label>
            </td>
            <td style="text-align: left" class="name"><a href="/storage/files/{{basename($file)}}" target="_blank">{{basename($file)}}</a></td>
            <td class="type">{{File::extension($file)}}</td>
            <td class="size">{{filesize_format(File::size($file))}}</td>
            <td class="date">{{date("Y-m-d G:i:s", File::lastModified($file))}}</td>
        </tr>
    @endforeach
    <script>
        $('.files_uploaded_table input').change(function () {
            var type = $(this).parents('tbody').attr('data-type');

            if (type == 'one_file') {
                $(this).parents('tbody').find('input').prop( "checked", false );
                $(this).prop( "checked", true );
            }
        });
    </script>
@else
    <tr>
        <td colspan="5" style="text-align: center; padding: 30px 0; width: 680px">Пока нет файлов</td>
    </tr>
@endif
