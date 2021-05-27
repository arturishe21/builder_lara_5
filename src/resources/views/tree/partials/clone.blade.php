@if ($active && app('user')->hasAccessActionsForCms('clone'))
	<li><a onclick="Tree.getCloneForm({{ $item->id }}, {{request("node", 1)}});" ><i class="fa fa-copy"></i> {{__cms('Клонировать')}} </a></li>
@endif