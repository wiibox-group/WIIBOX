{{#if died.BTC}}
	<tr class="danger">
		<th colspan="3">{{../basei18n.stopped}}</th>
	</tr>
	<tr>
		<th>{{../basei18n.number}}</th>
		<th>{{../basei18n.equipment}}</th>
		<th>{{../basei18n.algorithm}}</th>
	</tr>
	{{#each died.BTC}}
		<tr>
			<td>{{math @index "+" 1}}</td>
			<td>B:{{this}}</td>
			<td>SHA</td>
		</tr>
	{{/each}}
{{/if}}

{{#if died.LTC}}
	<tr class="danger">
		<th colspan="3">{{../basei18n.stopped}}</th>
	</tr>
	<tr>
		<th>{{../basei18n.number}}</th>
		<th>{{../basei18n.equipment}}</th>
		<th>{{../basei18n.algorithm}}</th>
	</tr>
	{{#each died.LTC}}
		<tr>
			<td>{{math @index "+" 1}}</td>
			<td>L:{{this}}</td>
			<td>SCRYPT</td>
		</tr>
	{{/each}}
{{/if}}

{{#if alived.BTC}}
	<tr class="success">
		<th colspan="3">{{../basei18n.running}}</th>
	</tr>
	<tr>
		<th>{{../basei18n.number}}</th>
		<th>{{../basei18n.equipment}}</th>
		<th>{{../basei18n.algorithm}}</th>
	</tr>
	{{#each alived.BTC}}
		<tr>
			<td>{{math @index "+" 1}}</td>
			<td>B:{{this}}</td>
			<td>SHA</td>
		</tr>
	{{/each}}
{{/if}}

{{#if alived.LTC}}
	<tr class="success">
		<th colspan="3">{{../basei18n.running}}</th>
	</tr>
	<tr>
		<th>{{../basei18n.number}}</th>
		<th>{{../basei18n.equipment}}</th>
		<th>{{../basei18n.algorithm}}</th>
	</tr>
	{{#each alived.LTC}}
		<tr>
			<td>{{math @index "+" 1}}</td>
			<td>L:{{this}}</td>
			<td>SCRYPT</td>
		</tr>
	{{/each}}
{{/if}}

