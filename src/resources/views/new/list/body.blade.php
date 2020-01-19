
@forelse ($listingRecords as $record)
    @include('admin::new.list.single_row')
@empty
    <tr>
        <td colspan="100%">{{__cms('Пока пусто')}}</td>
    </tr>
@endforelse
