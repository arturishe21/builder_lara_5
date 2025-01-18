<div class="modal fade tb-modal" id="modal_form_edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog " style="width: 1100px" >
        <div class="form-preloader smoke_lol"><i class="fa fa-gear fa-4x fa-spin"></i></div>

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="modal_form_edit_label">{{__cms('История изменений')}}</h4>
            </div>
            <div class="content_revisions">

                <table class="revisions table  table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{__cms('Пользователь')}}</th>
                        <th>{{__cms('Поле')}}</th>
                        <th>{{__cms('Старое значение')}}</th>
                        <th>{{__cms('Новое значение')}}</th>
                        <th>{{__cms('Дата/Время')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($history as $k => $historyRecord)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>
                                @if ($historyRecord->userResponsible())
                                    <a href="/admin/tb/users/{{ $historyRecord->userResponsible()->id }}?type=revisions" target="_blank">{{ $historyRecord->userResponsible()->first_name }}</a>
                                @endif
                            </td>
                            <td>{{ $historyRecord->fieldName()}}</td>
                            <td>
                                <div class="value_old_new">
                                    @if (is_array(json_decode($historyRecord->old_value, true)))
                                        @foreach(json_decode($historyRecord->old_value, true) as $k => $value)
                                            <p>{{$k}}: {{{$value}}}</p>
                                        @endforeach
                                    @else
                                        {{{ $historyRecord->old_value}}}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="value_old_new">
                                    @if (is_array(json_decode($historyRecord->new_value, true)))
                                        @foreach(json_decode($historyRecord->new_value) as $k => $value)
                                            <p>{{$k}}: {{{$value}}}</p>
                                        @endforeach
                                    @else
                                        {{{ $historyRecord->new_value}}}
                                    @endif
                                </div>
                            </td>
                            <td>{{{ $historyRecord->created_at}}}</td>
                            <td>
                                <a onclick="TableBuilder.getReturnHistory({{$historyRecord->id}});">{{__cms('Вернуть изменения')}}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px 0"><p>{{__cms('Изменений не было')}}</p></td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

