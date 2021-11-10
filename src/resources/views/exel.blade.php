<table>
	<thead>
	<tr>
		@foreach($head as $field)
		<th>{{$field->getName()}}</th>
		@endforeach
	</tr>
	</thead>
	<tbody>
		@foreach($items as $item)
			<tr>
				@foreach($item->fields as $field)
				<td>{{$field->value}}</td>
				@endforeach
			</tr>
		@endforeach
	</tbody>
</table>