@if ($active)
	<li><a onclick="TableBuilder.getRevisions({{ $item->id }}, this);" ><i class="fa fa-history"></i> {{__cms('Версии')}} </a></li>
@endif