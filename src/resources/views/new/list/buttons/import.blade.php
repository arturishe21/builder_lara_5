
<div class="widget-toolbar" role="menu">
	<div class="btn-group">
		<button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
			<i class="fa fa-download"></i>
			{{__cms('Импорт')}}
		</button>
		<ul class="dropdown-menu pull-right" style="min-width: {{ $def['width'] ?? '260' }}px; padding-bottom: 0;">
			<form id="tb-import-form" class="smart-form">
				<fieldset style="padding: 12px 12px 0;">
					<section>
						<div class="row">
							<div class="col col-12" style="width: 100%;">
								<div class="input input-file">
                                <span class="button" style="top: 3px; right: 3px;">
                                    <input type="file" name="file"
										   accept=".xlsx, .xls"
										   onclick="this.value = null;"
										   onchange="TableBuilder.doImport(this, 'xlsx', '/admin/{{$nameDefinition}}/import');"
									>
                                    {{__cms('Выбрать')}}
                                </span>
									<input type="text" placeholder="{{__cms('Выбрать файл')}}" readonly="readonly">
								</div>
							</div>
						</div>
					</section>
				</fieldset>
			</form>
		</ul>
	</div>
</div>
<script type="text/javascript">
    jQuery('#tb-import-form').bind('click', function(e) {
        e.stopPropagation()
    });
</script>
