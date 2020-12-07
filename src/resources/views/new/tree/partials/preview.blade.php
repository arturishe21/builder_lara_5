@if ($active)
	<li><a href="{{ $item->getUrl() }}?show=1" target="_blank"><i class="fa fa-eye"></i> {{__cms('Предпросмотр')}} </a></li>
@endif