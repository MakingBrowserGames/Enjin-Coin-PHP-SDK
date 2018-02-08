@extends('layouts.app')
@section('headTitle', 'PHP SDK Testing Panel')
@section('pageTitle', 'PHP SDK Testing Panel')

@section('content')
	<test-panel inline-template>
		<div class="container">
			<div class="panel panel-default" :class="sectionKey" v-for="(section, sectionKey) in sections">
				<div class="panel-heading" v-on:click="toggleSection(sectionKey)">@{{ section.title }}</div>
				<div class="panel-body" v-show="section.open">
					<textarea rows="5" class="form-control console-textarea" v-model="section.value"
							  v-if="sectionKey == 'console'"></textarea>
					<div class="methods">
						<div v-for="(method, methodKey) in section.methods" class="method">
							<div class="method-title" v-on:click="toggleMethod(sectionKey, methodKey)">@{{ method.title
								}}
							</div>
							<div class="method-toggle" v-show="method.open">
								<div v-for="param in method.params" class="col-6">
									<div class="form-group">
										<label class="form-label">@{{ param.label }}</label>
										<input type="text" v-if="param.type == 'text'" v-model="param.value"
											   class="form-control"/>
										<input type="number" v-if="param.type == 'number'" v-model="param.value"
											   class="form-control"/>
										<select v-if="param.type == 'select'" v-model="param.value"
												class="form-control">
											<option v-for="(optionValue, optionKey) in param.options"
													:value="optionKey">@{{ optionValue }}
											</option>
										</select>
										<small class="form-text text-muted" v-if="param.help">@{{ param.help }}</small>
									</div>
								</div>
								<div class="btn btn-sm btn-primary" v-on:click="execute(sectionKey, methodKey)">
									Execute
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</test-panel>
@endsection
