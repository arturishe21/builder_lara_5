<tr data-id="{{ $item->id }}" id-row="{{ $item->id }}">
    <td class="tb-sort-me-gently" style="cursor:s-resize;">
        <i class="fa fa-sort"></i>
    </td>
    <td>
        <i class="{{$item->isHasChildren() ? 'fa fa-folder' : 'fal fa-file'}}"></i>&nbsp;
        <a href="?node={{ $item->id }}" class="node_link">{{ $item->t('title') }}</a>
    </td>
        <td>
            <a class="tpl-editable" href="javascript:void(0);"
               data-type="select"
               data-name="template"
               data-pk="{{ $item->id }}"
               data-value="{{ $item->template }}"
               data-original-title="{{__cms("Выберите шаблон")}}">
                {{ $definition->getTitleDefinition($item->template)}}
            </a>
        </td>
        <td style="white-space: nowrap;">{{ $item->slug }}</td>
        <td style="position: relative;">
            <span class="onoffswitch">
                <input onchange="Tree.activeToggle('{{$item->id}}', this.checked);" type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" @if ($item->is_active) checked="checked" @endif id="myonoffswitch{{$item->id}}">
                <label class="onoffswitch-label" for="myonoffswitch{{$item->id}}">
                    <span class="onoffswitch-inner" data-swchon-text="{{__cms('ДА')}}" data-swchoff-text="{{__cms("НЕТ")}}"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </span>
        </td>


    <td style="text-align: center">
        <div style="display: inline-block">
            <div class="btn-group hidden-phone pull-right">
                <a class="btn dropdown-toggle btn-default" data-toggle="dropdown"><i class="fa fa-cog"></i> <i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    @include('admin::tree.partials.update', ['type' => 'update'])
                    @include('admin::tree.partials.preview', ['type' => 'preview'])
                    @include('admin::tree.partials.clone', ['type' => 'clone'])
                    @include('admin::tree.partials.revisions', ['type' => 'revisions'])
                    @include('admin::tree.partials.delete', ['type' => 'delete'])
                </ul>
            </div>
        </div>
    </td>
</tr>
