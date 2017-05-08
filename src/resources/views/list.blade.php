<p>Showing {{ $list->count() }} of {{ $list->total() }}</p>
@if($list->count())
<table class="table">
	<thead>
	<tr>
		@foreach($list->columns() as $column)
		<th><abbr title="{{ $column }}">{{ $column }}</abbr></th>
		@endforeach
	</tr>
	</thead>
	<tfoot>
	<tr>
		@foreach($list->columns() as $column)
			<th><abbr title="{{ $column }}">{{ $column }}</abbr></th>
		@endforeach
	</tr>
	</tfoot>
	<tbody>
	@foreach($list->rows as $row)
		<tr>
			@foreach($list->columns() as $column)
				<td>{{ $list->getRowValue($column, $row) }}</td>
			@endforeach
		</tr>
	@endforeach
	</tbody>
</table>
@else
	<p>No results found.</p>
@endif
{{ $list->links('vue-list::pagination') }}
