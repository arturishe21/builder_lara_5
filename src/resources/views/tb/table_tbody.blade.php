@forelse ($rows as $row)
    @include('admin::tb.single_row')
@empty
    <tr>
        <td colspan="100%">{{ __cms('Пока пусто') }}</td>
    </tr>
@endforelse
