@extends('layouts.elpts')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel">
				<div id="ajaxResponse"></div>
				<div class="panel-body">
					<form id="sign_form" action="/ajaxDocSave" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}
						<input type="hidden" name="templates_id" value="{{ $templates_id }}">
						<input type="hidden" name="doctypes_id" value="{{ $doctypes_id }}">
						<input type="hidden" name="ogrn_check" value="0">
						<input type="hidden" name="email_confirm_code_hidden" value="0">
						@if (count($doc_fields) > 0)
						@foreach ($doc_fields->all() as $doc_field)
						@if (in_array($doc_field->type, array('header')) && empty($docs_fields_header_count_arr[$doc_field->id]))
							@continue
						@endif

						@if (isset($doc_field->value) && $doc_field->value == '0')
							@continue
						@endif

						@if ($doc_field->type == 'header')
						</fieldset>
						<fieldset class="well the-fieldset">
							<legend class="the-legend bold">
								<h3>
									@if (!empty($doc_field->name_alias))
										{{ $doc_field->name_alias }}
									@else
										{{ $doc_field->name }}
									@endif
								</h3>
							</legend>
							@if ($doc_field->name == 'Реквизиты документа')
								{{--<div class="form-group">
									<span>Тип документа</span>
									<div class="form-control" style="height:auto;">
										@if (count($doctypes) > 0)
											@foreach ($doctypes->all() as $doctype)
												@if ($doctype->id == $template->doctypes_id)
													{{ $doctype->name }}
													@break
												@endif
											@endforeach
										@endif
									</div>
								</div>--}}
								<div class="form-group">
									<span>Наименование документа</span>
									<div class="form-control" style="height:auto;">
										{{ $template->name }}
									</div>
								</div>
							@endif
							@continue
							@endif

							<div class="form-group">
								<a name="doc_field{{ $doc_field->id }}"></a>

								@if (!in_array($doc_field->type, array('header', 'captcha')) && !in_array($doc_field->id, array(30)))
									<span class="doc_field doc_field{{ $doc_field->id }}">
										@if (!empty($doc_field->name_alias))
											{{ $doc_field->name_alias }}
										@else
											{{ $doc_field->name }}
										@endif
									</span>
								@endif

								@switch($doc_field->type)
									@case('text')
									@if (!empty($doc_field->value))
										<div class="form-control" style="height:auto;">
											<input type="hidden" name="doc_field{{ $doc_field->id }}"
												   value="{{ $doc_field->value }}">
											{!! $doc_field->value !!}
										</div>
									@endif
									@break
									@case('textarea')
									<textarea name="doc_field{{ $doc_field->id }}" class="form-control"
											  style="height:300px;background-color:#fff;">@if(old('doc_field'.$doc_field->id)){{old('doc_field'.$doc_field->id)}}@endif</textarea>
									@break
									@case('input')
									@if ($doc_field->required || !$doc_field->template_field_visible)<span
											class="red required_sign{{ $doc_field->id }}">*</span>@endif
									<input type="text" name="doc_field{{ $doc_field->id }}"
										   value="@if (old('doc_field'.$doc_field->id)){{ old('doc_field'.$doc_field->id) }}@endif"
										   class="form-control @if (!empty($doc_field->mask)) {{ $doc_field->mask }} @endif">
									@if ($doc_field->name == 'Адрес электронной почты')
										<button type="button" id="email_confirm_code_btn" class="btn btn-info">Отправить
											код подтверждения
										</button>
									@elseif ($doc_field->name == 'ОГРН/ОГРНИП')
										<button type="button" id="ogrn_check_btn" class="btn btn-info">Проверить наличие
											данных
										</button>
									@endif
									@break
									@case('file')
									@if ($doc_field->required)<span class="red">*</span>@endif
									<input type="file" name="doc_field{{ $doc_field->id }}" class="form-control">
									<input type="hidden" name="file_doc_field{{ $doc_field->id }}" class="file_hidden">
									@break
									@case('checkbox')
									<label class="fancy-checkbox">
										<input type="hidden" name="doc_field{{ $doc_field->id }}" value="">
										<input type="checkbox" name="doc_field{{ $doc_field->id }}" value="1"
											   @if (old('doc_field'.$doc_field->id)) checked @endif>
										<span class="doc_field doc_field{{ $doc_field->id }}">{{ $doc_field->value }}</span>
										<span class="red">*</span>
									</label>
									@break
									@case('select')
									@if ($doc_field->required)<span class="red">*</span>@endif
									<select name="doc_field{{ $doc_field->id }}" class="form-control">
										<option value="">---</option>
										@switch($doc_field->link)
											@case('okopfs')
											@if (count($okopfs) > 0)
												@foreach ($okopfs->all() as $okopf)
													<option value="{{ $okopf->id }}"
															@if (old('doc_field'.$doc_field->id) == $okopf->id) selected @endif>{{ $okopf->name }}</option>
												@endforeach
											@endif
											@break
											@case('countries')
											@if (count($countries) > 0)
												@foreach ($countries->all() as $country)
													<option value="{{ $country->id }}"
															@if (old('doc_field'.$doc_field->id) == $country->id) selected @endif>{{ $country->name }}</option>
												@endforeach
											@endif
											@break
										@endswitch
									</select>
									@break
									@case('captcha')
									<div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
									<small class="text-danger">{{ $errors->first('g-recaptcha-response') }}</small>
									@break
									@case('certificate')
									<select id="certificates" name="certificates" class="form-control">
										<option value="-1" selected>Загрузка...</option>
									</select>
									@break
								@endswitch
							</div>
							@endforeach
						</fieldset>
						@endif
						<div class="text-center">
							<button class="btn btn-primary">Подписать</button>
							<button type="button" class="btn btn-primary"
									onClick="if(confirm('Внимание! Все внесенные данные будут потеряны.\nВы действительно хотите выйти?')){window.location.href='/';}">
								Выйти
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection
