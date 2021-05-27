@if ($active && app('user')->hasAccessActionsForCms('update'))
	<li><a onclick="Tree.showEditForm('{{ $item->id }}');"><i class="fa fa-pencil"></i> {{__cms('Редактировать')}}</a></li>
@endif