<?php
$result = $current::defaultOrder()->ancestorsOf($current);
?>

@foreach ($result as $ancestor)
    <a href="?node={{ $ancestor->id }}" class="node_link">{{ $ancestor->t('title')}}</a> /
@endforeach

{{$current->t('title')}}

<a href="javascript:void(0);" onclick="Tree.showEditForm('{{$current->id}}');" style="min-width: 70px; float: right">{{__cms('Редактировать')}}</a>

<div class="buttons-panel">

</div>