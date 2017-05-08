<vue-table :vue-table="{{ $table->toJson() }}" inline-template>
	<div>
		<h2 v-html="title"></h2><button @click.prevent="reload">Reload</button>

		<table class="table" v-if="hasResult">
			<thead>
			<tr>
				<th v-for="column in labels"><abbr :title="column">@{{ column }}</abbr></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th v-for="column in labels"><abbr :title="column">@{{ column }}</abbr></th>
			</tr>
			</tfoot>
			<tbody>
			<tr v-for="row in rows">
				<td v-for="column in row">@{{ column }}</td>
			</tr>
			</tbody>
		</table>

		<p v-else>No results found.</p>

		<ul class="pagination" v-if="hasPages">
			<li class="page-item disabled" v-if="onFirstPage"><span class="page-link">&laquo;</span></li>

			<li class="page-item" v-else>
				<a class="page-link" :href="previousUrl" rel="prev" @click.prevent="loadPage(previousUrl)">&laquo;</a>
			</li>

			<li v-for="element in pagination" class="page-item disabled" v-if="typeof element == 'string'">
				<span class="page-link">@{{ element }}</span>
			</li>

			<li v-else class="page-item" v-for="(url, page) in element" :class="{ active: isCurrentPage(page) }">
				<span class="page-link" v-if="isCurrentPage(page)">@{{ page }}</span>
				<a v-else class="page-link" :href="url" @click.prevent="loadPage(url)">@{{ page }}</a>
			</li>

			<li class="page-item" v-if="hasMorePages">
				<a class="page-link" :href="nextUrl" rel="next" @click.prevent="loadPage(nextUrl)">&raquo;</a>
			</li>

			<li class="page-item disabled" v-else><span class="page-link">&raquo;</span></li>
		</ul>

		{{--<div>
			<button type="button" @click="download">Export</button>

			<ul>
				<li v-for="download in downloads">
					<a v-if="download.completed" :href="download.path" :download="download.id">Download</a>

					<span>
						<progress max="100" :value="download.progress"></progress>@{{ download.progress }}
			</span>

					<button type="button" @click="deleteDownload(download.id)" v-if="download.completed">Remove</button>
				</li>
			</ul>
		</div>--}}

		<div>
			<form @submit.prevent="upload">
				<input type="file" name="import"/>

				<button type="submit">Import</button>
			</form>

			<ul>
				<li v-for="upload in uploads">
					<span>
						<progress max="100" :value="upload.progress"></progress>@{{ upload.progress }}
					</span>
				</li>
			</ul>
		</div>
	</div>
</vue-table>