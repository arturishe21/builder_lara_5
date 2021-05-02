@if ($active)
	<li><a onclick="TableBuilder.getRevisions({{ $item->id }});" ><i class="fa fa-history"></i> {{__cms('История')}} </a></li>
@endif