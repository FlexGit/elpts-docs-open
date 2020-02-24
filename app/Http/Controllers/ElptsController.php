<?php

namespace App\Http\Controllers;

use Session;
use App\Doctypes;
use App\Okopfs;
use App\Countries;
use App\Prefixes;
use App\Templates;
use App\Docs;
use App\Logs;
use Illuminate\Support\Str;
use View;
use Storage;
use Illuminate\Http\Request;
use Validator;
use Mail;
use Illuminate\Support\Facades\DB;
use Log;

class ElptsController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		// Get Doctypes
		$doctypes = Doctypes::where('enable', '=', '1')->orderBy('id')->get();
		
		// Get Templates
		$templates = Templates::where('enable', '=', '1')->orderBy('id')->get();
		
		return view('elpts.index')
			->withDoctypes($doctypes)
			->withTemplates($templates);
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $templates_id
	 * @return \Illuminate\Http\Response
	 */
	public function create($templates_id) {
		// Get Docs Fields
		$docs_fields_obj = new Docs;
		
		// Get Doctypes
		$doctypes = Doctypes::where('enable', '=', '1')->orderBy('id')->get();
		
		// Get Okopfs
		$okopfs = Okopfs::where('enable', '=', '1')->orderBy('name')->get();
		
		// Get Countries
		$countries = Countries::where('enable', '=', '1')->orderBy('id')->get();
		
		// Get Template
		$template = Templates::where([['enable', '=', '1'], ['id', '=', $templates_id]])->get();
		
		if (!count($template)) {
			abort(404);
		}
		
		$template = $template[0];
		
		// Get Docs Fields With Templates Values
		$docs_fields = $docs_fields_obj->getDocsFields($template->doctypes_id, $templates_id);
		
		$docs_fields_header_count_arr = [];
		if (count($docs_fields) > 0) {
			foreach ($docs_fields->all() as $docs_field) {
				if (isset($docs_field->value) && $docs_field->value == '0') continue;
				
				$docs_fields_header_count_arr[ $docs_field->parent_id ][] = $docs_field->id;
			}
		}
		
		return view('elpts.create')
			->withDoctypes($doctypes)
			->withOkopfs($okopfs)
			->withCountries($countries)
			->withTemplate($template)
			->with('doc_fields', $docs_fields)
			->with('doctypes_id', $template->doctypes_id)
			->with('templates_id', $templates_id)
			->with('docs_fields_header_count_arr', $docs_fields_header_count_arr);
	}
	
	/**
	 * Doc Save Ajax Post Request
	 */
	public function ajaxDocSave() {
		$request = request()->all();
		
		$templates_id = $request['templates_id'];
		
		// Get Template
		$template = Templates::where([['enable', '=', '1'], ['id', '=', $templates_id]])->get();
		
		if (!count($template)) {
			return response()->json([
				'response' => [
					'error' => [0 => 'Шаблон Документа не найден.'],
				],
			]);
		}
		
		$template = $template[0];
		
		// Get Prefixes
		$prefixes = Prefixes::where([
			['enable', '=', '1'],
			['doctypes_id', '=', $template->doctypes_id],
		])
			->get();
		
		// Get Template Fields
		$template_fields_obj = new Templates;
		$template_fields = $template_fields_obj->getTemplateFields($template->doctypes_id);
		
		// Get Template Fields Values
		$template_values_arr = $template_fields_obj->getTemplateFieldsValues($templates_id);
		
		// Get Docs Fields
		$doc_fields_obj = new Docs;
		$doc_fields = $doc_fields_obj->getDocsFields($template->doctypes_id, $templates_id);
		
		// Prepare Validation Rules
		$rules = $recaptcha_rules = $customValidNames = [];
		if (count($doc_fields) > 0) {
			foreach ($doc_fields->all() as $doc_field) {
				// If There Is An Option Not To Show The Field In The User Interface Or The Field Type Is 'header'
				if ((isset($doc_field->value) && $doc_field->value == '0') || $doc_field->type == 'header') {
					continue;
				}
				
				$key = 'doc_field' . $doc_field->id;
				$file_key = 'file_doc_field' . $doc_field->id;
				
				if (!empty($doc_field->name_alias))
					$customValidNames[$key] = '<a href="#'.$key.'">'.$doc_field->name_alias.'</a>';
				else
					$customValidNames[$key] = '<a href="#'.$key.'">'.$doc_field->name.'</a>';
				
				// If There Was A Early Uploaded File
				if ($doc_field->type == 'file' && !empty($request[ $file_key ])) {
					continue;
				}
				
				if ($doc_field->type == 'captcha') {
					$recaptcha_rules['g-recaptcha-response'] = 'required|captcha';
				}
				else if ($doc_field->type == 'certificate') {
					$rules['certificates'] = 'certificate';
				}
				else {
					// If There Is An Option That The Field Is Required
					if (!empty($template_values_arr[ $doc_field->templates_fields_id ]) && !in_array($doc_field->type, ['text'])) {
						if ($template_values_arr[ $doc_field->templates_fields_id ]['required']) {
							$rules[ $key ] = 'required';
						}
						else {
							$rules[ $key ] = 'nullable';
						}
					}
					
					// If The Field Is One Of Theese: 'Согласие на акцепт', 'ОГРН', 'E-mail'
					if (in_array($doc_field->id, ['30', '5', '20'])) {
						$rules[ $key ] = 'required';
						
						if ($doc_field->id == '5') {
							$rules[ $key ] = '|check_ogrn_exists';
						}
					}
					
					// If The Field Is 'Код подтверждения' And E-mail Confirmation Code Is Not Hidden
					if ($request['email_confirm_code_hidden']) {
						if (in_array($doc_field->id, ['21'])) {
							$rules[ $key ] = 'required';
						}
					}
					
					// Add Rules From DB
					if ($doc_field->valid_rules) {
						if (!empty($rules[ $key ])) {
							$rules[ $key ] .= '|' . $doc_field->valid_rules;
						}
						else {
							$rules[ $key ] = $doc_field->valid_rules;
						}
					}
					
					// Exception For OKOPF == 'Индивидуальные предприниматели'
					if (!empty($request['doc_field3'])) {
						if ($request['doc_field3'] == '50102') {
							if (!empty($rules['doc_field6'])) {
								unset($rules['doc_field6']);
							}
						}
					}
				}
			}
		}
		
		// Custom Field Message
		$messages = [
			'doc_field30.required' => 'Поле <a href="#doc_field30">"' . $template_values_arr['201']['value'] . '"</a> обязательно для заполнения.',
		];
		
		// Request Validation
		$validator = Validator::make($request, $rules, $messages);
		// Doc's Fields Aliases Instead Basic Names
		$validator->setAttributeNames($customValidNames);
		if (!$validator->passes()) {
			return response()->json([
				'response' => [
					'error' => $validator->errors()->all(),
				],
			]);
		}
		
		// E-mail Unique Validation
		$response = $doc_fields_obj->emailUniqueValidate($request['doc_field20'], $request['doc_field5']);
		if ($response['error']) {
			return response()->json([
				'response' => [
					'error' => $response['error'],
				],
			]);
		}
		
		// E-mail Not In E-mail Registry
		$response = $doc_fields_obj->checkEmailRegistry($request['doc_field20']);
		if ($response['error']) {
			return response()->json([
				'response' => [
					'error' => $response['error'],
				],
			]);
		}
		
		// E-mail Confirmation Code Validation
		if (!empty($request['doc_field21'])) {
			$response = $doc_fields_obj->emailConfirmCodeValidate($request['doc_field20'], $request['doc_field21']);
			if ($response['error']) {
				return response()->json([
					'response' => [
						'error' => $response['error'],
					],
				]);
			}
			
			$email_confirm_code_id = $response['email_confirm_code_id'];
		}
		else {
			$email_confirm_code_id = 0;
		}
		
		// Recaptcha Validation
		if (!empty($recaptcha_rules)) {
			$validator = Validator::make($request, $recaptcha_rules);
			if (!$validator->passes()) {
				return response()->json([
					'response' => [
						'error' => $validator->errors()->all(),
					],
				]);
			}
		}
		
		$prefix_id = $template_values_arr[15]['value'];
		
		/*$cur_number = $doc_fields_obj->getCurrentNumber($template->doctypes_id, $prefix_id);
		$number = $cur_number + 1;
		
		$prefix = '';
		if (count($prefixes) > 0) {
			foreach ($prefixes->all() as $value) {
				if ($value->id == $prefix_id) {
					$prefix = $value->name;
					break;
				}
			}
		}
		
		// BEGIN Task "SITEELPTS-30"
		$zeros = '';
		if (strlen($number) < 7) {
			for ($i = 0; $i < (7 - strlen($number)); $i++) {
				$zeros .= '0';
			}
		}
		$postfix = 'рф';
		// END Task "SITEELPTS-30"
		
		$prefix_number = $prefix . '/' . $zeros . $number . $postfix;*/
		
		$prefix_number = 'Проект';
		
		// Save Doc
		$doc = new Docs;
		/*$doc->number = $number;*/
		$doc->prefix_number = $prefix_number;
		$doc->templates_id = $templates_id;
		$doc->doctypes_id = $template->doctypes_id;
		$doc->prefix_id = $prefix_id;
		
		$doc->save();
		$doc_id = $doc->id;
		
		// Write Log
		$log = new Logs;
		$log->operation_id = 30;
		$log->doc_id = $doc_id;
		$log->user_name = $request['doc_field5'];
		$log->save();
		
		// Save Doc Fields Values
		$doc_fields_obj->storeDocsFieldsValues($doc_id, $doc_fields, $prefix_number, $template, $template_values_arr, $request);
		
		// Get Doc Fields Values
		$doc_values_arr = $doc_fields_obj->getDocsFieldsValues([$doc_id]);
		
		// Prepare Array to XML
		$xml_arr = [
			'80' => [
				'0' => [
					'alias' => 'StatementNumber',
					'value' => $prefix_number,
				],
			],
		];
		
		if (!empty($doc_fields)) {
			foreach ($doc_fields->all() as $doc_field) {
				if (empty($doc_field->alias))
					continue;
				
				if (empty($doc_field->parent_id))
					$doc_field->parent_id = 0;
				
				$xml_arr[ $doc_field->parent_id ][ $doc_field->id ]['alias'] = $doc_field->alias;
				$xml_arr[ $doc_field->parent_id ][ $doc_field->id ]['value'] = '';
				if (!empty($doc_values_arr[ $doc_field->id ]['value'])) {
					$xml_arr[ $doc_field->parent_id ][ $doc_field->id ]['value'] = $doc_values_arr[ $doc_field->id ]['value'];
				}
			}
		}
		
		// Generate XML
		$xml = View::make('elpts.xml')
			->with('xml_arr', $xml_arr)
			->render();
		
		// Save encoded Base64 XML
		$xml_encode = base64_encode($xml);
		
		// Save Xml And Base64 Encoded Xml Files To DB
		$doc = Docs::find($doc_id);
		$doc->file = base64_encode($xml);
		$doc->file_base64 = base64_encode($xml_encode);
		$doc->save();
		
		$certificates = 0;
		if (isset($request['certificates']))
			$certificates = $request['certificates'];
		
		return response()->json([
			'response' => [
				'error' => [],
				'certificates' => $certificates,
				'file' => $xml_encode,
				'doc_id' => $doc_id,
				'email_confirm_code_id' => $email_confirm_code_id,
			],
		]);
	}
	
	/**
	 * Doc Sign Ajax Post Request
	 */
	public function ajaxDocSign() {
		$request = request()->all();
		
		// Create Docs Object
		$doc = new Docs;
		
		// Verify Signature by Signal-COM DSS Server
		$response = $doc->signatureVerify($request['file'], $request['signature']);
		
		if ($response['error']) {
			// Write Log
			$log = new Logs;
			$log->operation_id = 26;
			$log->doc_id = $request['doc_id'];
			$log->user_name = $request['ogrn'];
			$log->value = 'Ошибка: Подпись не прошла верификацию DSS-сервером. ' . $response['error'][0];
			$log->save();
			
			$remove_doc_response = $doc->removeDoc($request['doc_id']);
			
			return response()->json([
				'response' => [
					'error' => $response['error'],
					'msg' => 'Подпись не прошла верификацию DSS-сервером.',
					'remove_doc' => $remove_doc_response,
				],
			]);
		}
		
		// Get Doc Fields Values
		$doc_values_arr = $doc->getDocsFieldsValues([$request['doc_id']]);
		
		// Matching OGRN In Form and Certificate
		if ($request['ogrn'] !== $doc_values_arr['5']['value']) {
			// Write Log
			$log = new Logs;
			$log->operation_id = 28;
			$log->doc_id = $request['doc_id'];
			$log->user_name = $request['ogrn'];
			$log->value = 'Ошибка: ОГРН из сертификата "' . $request['ogrn'] . '" не совпадает с ОГРН из документа "' . $doc_values_arr['5']['value'] . '"';
			$log->save();
			
			$remove_doc_response = $doc->removeDoc($request['doc_id']);
			
			return response()->json([
				'response' => [
					'error' => [0 => 'OGRN Matching Error'],
					'msg' => 'ОГРН в документе и в сертификате ЭЦП должны совпадать.',
					'remove_doc' => $remove_doc_response,
				],
			]);
		}
		
		// Matching INN In Form and Certificate
		if (!empty($doc_values_arr['4']['value'])) {
			// BUG SITEELPTS-23
			if (substr($doc_values_arr['4']['value'], 0, 2) != '00' && strlen($doc_values_arr['4']['value']) == 10) {
				$doc_values_arr['4']['value'] = '00' . $doc_values_arr['4']['value'];
			}
			
			if ($request['inn'] !== $doc_values_arr['4']['value']) {
				// Write Log
				$log = new Logs;
				$log->operation_id = 28;
				$log->doc_id = $request['doc_id'];
				$log->user_name = $request['ogrn'];
				$log->value = 'Ошибка: ИНН из сертификата "' . $request['inn'] . '" не совпадает с ИНН из документа "' . $doc_values_arr['4']['value'] . '"';
				$log->save();
				
				$remove_doc_response = $doc->removeDoc($request['doc_id']);
				
				return response()->json([
					'response' => [
						'error' => [0 => 'INN Matching Error'],
						'msg' => 'ИНН в документе и в сертификате ЭЦП должны совпадать.',
						'remove_doc' => $remove_doc_response,
					],
				]);
			}
		}
		
		// Matching Fullname In Form and Certificate
		if (!empty($doc_values_arr['13']['value'])) {
			if ($request['fullname'] !== $doc_values_arr['13']['value']) {
				// Write Log
				$log = new Logs;
				$log->operation_id = 28;
				$log->doc_id = $request['doc_id'];
				$log->user_name = $request['ogrn'];
				$log->value = 'Ошибка: ФИО из сертификата "' . $request['fullname'] . '" не совпадает с ФИО из документа "' . $doc_values_arr['13']['value'] . '"';
				$log->save();
				
				$remove_doc_response = $doc->removeDoc($request['doc_id']);
				
				return response()->json([
					'response' => [
						'error' => [0 => 'Fullname Matching Error'],
						'msg' => 'ФИО в документе и в сертификате ЭЦП должны совпадать.',
						'remove_doc' => $remove_doc_response,
					],
				]);
			}
		}
		
		// Matching Position In Form and Certificate
		if (!empty($doc_values_arr['12']['value'])) {
			if ($request['position'] !== $doc_values_arr['12']['value']) {
				// Write Log
				$log = new Logs;
				$log->operation_id = 28;
				$log->doc_id = $request['doc_id'];
				$log->user_name = $request['ogrn'];
				$log->value = 'Ошибка: Должность из сертификата "' . $request['position'] . '" не совпадает с Должностью из документа "' . $doc_values_arr['12']['value'] . '"';
				$log->save();
				
				$remove_doc_response = $doc->removeDoc($request['doc_id']);
				
				return response()->json([
					'response' => [
						'error' => [0 => 'Position Matching Error'],
						'msg' => 'Должность в документе и в сертификате ЭЦП должны совпадать.',
						'remove_doc' => $remove_doc_response,
					],
				]);
			}
		}
		
		$doc = Docs::find($request['doc_id']);
		$doctypes_id = $doc->doctypes_id;
		$templates_id = $doc->templates_id;
		
		$template = Templates::find($templates_id);

		// Save Signature And Additional Data to DB
		$doc->file_sign = base64_encode($request['signature']);
		$doc->snils = $request['snils'];
		$doc->position = $request['position'];
		$doc->fullname = $request['fullname'];
		if (!$template->no_accept)
			$doc->status_id = 1;
		else
			$doc->status_id = 7;
		$doc->save();
		
		// Set 'Used' Attribute To Email Confirm Code
		if ($request['email_confirm_code_id']) {
			DB::table('elpts_email_confirmation')->where('id', '=', $request['email_confirm_code_id'])->update(['used' => '1']);
		}
		
		// Write Log
		$log = new Logs;
		$log->operation_id = 2;
		$log->doc_id = $request['doc_id'];
		$log->user_name = $request['ogrn'];
		$log->save();
		
		// Get Templates Object
		$template_fields_obj = new Templates;
		
		// Get Template Fields Values
		$template_values_arr = $template_fields_obj->getTemplateFieldsValues($templates_id);
		
		// Send E-mail With Accepted Answer
		if (!empty($template_values_arr['3']['value'])) {
			// Replace Vars with Data
			$template_values_arr['3']['value'] = str_replace('%NUMBER%', $doc->prefix_number, $template_values_arr['3']['value']);
			if (!empty($doc_values_arr['41']['value'])) {
				$template_values_arr['3']['value'] = str_replace('%ORG_NAME%', $doc_values_arr['41']['value'], $template_values_arr['3']['value']);
			}
			if (!empty($doc_values_arr['4']['value'])) {
				$template_values_arr['3']['value'] = str_replace('%INN%', $doc_values_arr['4']['value'], $template_values_arr['3']['value']);
			}
			$template_values_arr['3']['value'] = str_replace('%OGRN%', $doc_values_arr['5']['value'], $template_values_arr['3']['value']);
			if (!empty($doc_values_arr['36']['value'])) {
				$template_values_arr['3']['value'] = str_replace('%SNILS%', $doc_values_arr['36']['value'], $template_values_arr['3']['value']);
			}
			$template_values_arr['3']['value'] = str_replace('%DATE%', date('d.m.Y'), $template_values_arr['3']['value']);
			
			Mail::send('emails.email_accept_doc', ['body' => $template_values_arr['3']['value']], function ($message) use ($doc_values_arr) {
				$message->to($doc_values_arr['20']['value'])->subject('Уведомление о принятии документа на согласование');
			});
			
			if (count(Mail::failures()) > 0) {
				$i = 0;
				$response = [
					'msg' => 'error',
				];
				foreach (Mail::failures as $failure) {
					$response['error_code'] = [
						$i => $failure,
					];
					$i++;
				}
			}
			
			// Write Log
			$log = new Logs;
			$log->operation_id = 29;
			$log->doc_id = $request['doc_id'];
			$log->user_name = $request['ogrn'];
			if (!empty($response['error_code']))
				$log->value = json_encode($response['error_code']);
			else
				$log->value = 'Да';
			$log->save();
		}
		
		// Create Docs Object
		$docs_obj = new Docs;
		
		// Get Statuses
		$statuses = $docs_obj->getStatuses($doctypes_id);
		
		// Send E-mail To Operator With Status Change
		if (count($statuses) > 0) {
			foreach ($statuses->all() as $status) {
				if ($status->id == 1 && !empty($status->notification_email) && !empty($status->notification_text)) {
					Mail::send('emails.email_operator_status_change', ['body' => $status->notification_text], function ($message) use ($status) {
						$message->to($status->notification_email)->subject('Уведомление о назначении нового акцепта');
					});
					break;
				}
			}
		}

		return response()->json([
			'response' => [
				'error' => [],
				'status' => 'success',
				'msg' => 'Документ успешно создан и подписан!',
			],
		]);
	}
	
	/**
	 * Email Confirm Code Ajax Post Request
	 */
	public function ajaxEmailConfirmCode() {
		$request = request()->all();
		
		$rules = [
			'email' => 'required|email',
		];
		
		// Request Validation
		$validator = Validator::make($request, $rules);
		if (!$validator->passes()) {
			return response()->json([
				'response' => [
					'error' => $validator->errors()->all(),
				],
			]);
		}
		
		// Create Docs Object
		$doc_obj = new Docs;
		
		// Store Email Confirm Code
		$response = $doc_obj->storeEmailConfirmCode($request);
		
		return response()->json([
			'response' => [
				'error' => [],
				'status' => 'success',
				'msg' => $response['msg'],
				'code' => $response['code'],
			],
		]);
	}
	
	/**
	 * Check OGRN
	 */
	public function ajaxOgrnCheck() {
		$request = request()->all();
		
		$rules = [
			'ogrn' => 'required|is_ogrn',
		];
		
		if ($request['doctypes_id'] == 1) {
			$rules['ogrn'] .= '|check_ogrn_exists';
		}
		
		// Request Validation
		$validator = Validator::make($request, $rules);
		if (!$validator->passes()) {
			return response()->json([
				'response' => [
					'error' => $validator->errors()->all(),
					'doctypes_id' => $request['doctypes_id'],
				],
			]);
		}
		
		// Create Docs Object
		$doc = new Docs;
		
		// Check If OGRN Exists In Previous Docs
		$default_values = $doc->checkDocsForOgrn($request['ogrn'], $request['doctypes_id'], $request['templates_id']);
		
		return response()->json([
			'response' => [
				'error' => [],
				'status' => 'success',
				'msg' => 'Проверка поля "ОГРН/ОГРНИП" пройдена успешно! Подписание возможно.',
				'default_values' => $default_values,
				'doctypes_id' => $request['doctypes_id'],
			],
		]);
	}
	
	/**
	 * Output The File.
	 *
	 * @param  string $file
	 * @return \Illuminate\Http\Response
	 */
	public function file($file) {
		$filePath = base_path() . '/files/docs/' . $file;
		
		$headers = [
			'Content-Description: File Transfer',
			'Content-Type: application/octet-stream',
			'Content-Disposition: attachment; filename="' . $file . '"',
		];
		
		return response()->download($filePath, $file, $headers);
	}
}
