<div class="loader_definition"><i class="fa fa-gear fa-4x fa-spin"></i></div>
<table class="table table-hover table-bordered">
    <thead>
    <tr>
        <td class="col_sort"></td>
        @foreach($fieldsDefinition as $field)
            <th>{{$field->getName()}}</th>
        @endforeach
        <th style="width: 10%"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($list as $record)
        <tr data-id="{{$record->id}}">
            <td class="handle col_sort"><i class="fa fa-sort"></i></td>
            @foreach($record->fields as $field)
                <td>{!! $field->value !!}</td>
            @endforeach
            <td>
                <div class="btn-group hidden-phone pull-right">
                    <a class="btn dropdown-toggle btn-default"  data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a onclick="ForeignDefinition.edit({{$record->id}}, '{{request('id')}}', '{{$attributes}}', 'actions')"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a></li>
                        <li><a onclick="ForeignDefinition.delete({{$record->id}}, '{{request('id')}}', '{{$attributes}}', '{{$urlAction}}')"><i class="fa red fa-times"></i> {{__cms('Удалить')}}</a></li>
                    </ul>
                </div>

            </td>
        </tr>
    @empty
        <tr><td colspan="{{count ($fieldsDefinition) + 1 }}"> {{__cms('Пока пусто')}} </td></tr>
    @endforelse
    </tbody>
</table>
