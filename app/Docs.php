<?php

namespace App;

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use SoapClient;
use SoapVar;
//use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mail;
use App\Logs;

class Docs extends Model {
	protected $table = 'elpts_docs';
	protected $fillable = ['number', 'prefix_number', 'templates_id', 'doctypes_id', 'prefix_id', 'status_id', 'snils', 'position', 'fullname', 'file_sign'];
	
	/**
	 * Get Doctype's Doc Fields with their Template Values.
	 *
	 * @param  int $doctypes_id
	 * @param  int $templates_id
	 * @return object DB data
	 */
	public function getDocsFields($doctypes_id, $templates_id) {
		return DB::table('elpts_docs_fields')
			->select('elpts_docs_fields.id', 'elpts_docs_fields.name', 'elpts_docs_fields.alias', 'elpts_docs_fields.type', 'elpts_docs_fields.link', 'elpts_docs_fields.mask', 'elpts_docs_fields.valid_rules', 'elpts_templates_fields_values.value', 'elpts_templates_fields_values.required', 'elpts_docs_fields.templates_fields_id', 'elpts_docs_fields.parent_id', 'elpts_docs_fields.required as field_required', 'elpts_templates_fields.visible as template_field_visible')
			->leftJoin('elpts_templates_fields_values', function ($join) use ($templates_id) {
				$join->on('elpts_templates_fields_values.fields_id', '=', 'elpts_docs_fields.templates_fields_id');
				$join->on('elpts_templates_fields_values.templates_id', '=', DB::raw($templates_id));
			})
			->leftJoin('elpts_templates_fields', 'elpts_templates_fields.id', '=', 'elpts_docs_fields.templates_fields_id')
			->whereIn('elpts_docs_fields.doctypes_id', [0, DB::raw($doctypes_id)])
			->where([
				['elpts_docs_fields.enable', '=', '1'],
				['elpts_docs_fields.visible', '=', '1'],
			])
			->orderBy('elpts_docs_fields.sort')
			->get();
	}
	
	/**
	 * Get Doc Fields Values by IDs.
	 *
	 * @param  array $ids
	 * @return array $values_arr
	 */
	public function getDocsFieldsValues($ids = []) {
		$rows = DB::table('elpts_docs_fields_values')
			->select('elpts_docs_fields.name', 'elpts_docs_fields_values.fields_id', 'elpts_docs_fields.alias', 'elpts_docs_fields_values.value', 'elpts_docs_fields.type', 'elpts_docs_fields_values.status_id', 'elpts_docs_fields.parent_id')
			->join('elpts_docs_fields', 'elpts_docs_fields_values.fields_id', '=', 'elpts_docs_fields.id')
			->where('elpts_docs_fields.enable', '=', '1')
			->whereIn('elpts_docs_fields_values.docs_id', $ids)
			->orderBy('elpts_docs_fields.sort')
			->get();
		
		$values_arr = [];
		if (count($rows) > 0) {
			foreach ($rows->all() as $value) {
				if (!$value->parent_id) $value->parent_id = 0;
				
				$values_arr[ $value->fields_id ]['name'] = $value->name;
				$values_arr[ $value->fields_id ]['alias'] = $value->alias;
				$values_arr[ $value->fields_id ]['value'] = $value->value;
				$values_arr[ $value->fields_id ]['type'] = $value->type;
				$values_arr[ $value->fields_id ]['status_id'] = $value->status_id;
				$values_arr[ $value->fields_id ]['parent_id'] = $value->parent_id;
			}
		}
		
		return $values_arr;
	}
	
	/**
	 * Get Doctype's Max Number.
	 *
	 * @param  int $doctypes_id
	 * @param  int $prefix_id
	 * @return int $number
	 */
	public function getCurrentNumber($doctypes_id, $prefix_id) {
		return DB::table('elpts_docs')
			->where([
				['doctypes_id', '=', $doctypes_id],
				['prefix_id', '=', $prefix_id],
			])
			->max('number');
	}
	
	/**
	 * Store Docs Fields Values.
	 *
	 * @param  int $id
	 * @param  array $fields
	 * @param  string $prefix_number
	 * @param  object $template
	 * @param  array $request
	 */
	public function storeDocsFieldsValues($id, $fields, $prefix_number, $template, $template_values_arr, $request) {
		// Get Okopfs
		$okopfs = Okopfs::get();
		
		// Get Countries
		$countries = Countries::get();
		
		// Prepare Doc Fields Values
		$values = [];
		$i = 0;
		if (count($fields) > 0) {
			foreach ($fields as $field) {
				if (empty($request[ 'doc_field' . $field->id ]) && empty($request[ 'file_doc_field' . $field->id ])) continue;
				
				$values[ $i ]['docs_id'] = $id;
				$values[ $i ]['fields_id'] = $field->id;
				
				// Storing Files
				if ($field->type == 'file') {
					if (!empty($request[ 'doc_field' . $field->id ])) // There Is An Uploaded File
					{
						$values[ $i ]['value'] = base64_encode(file_get_contents($request[ 'doc_field' . $field->id ]));
					}
					else if (!empty($request[ 'file_doc_field' . $field->id ])) // There Was An Early Uploaded File
					{
						$values[ $i ]['value'] = base64_encode(file_get_contents(base_path() . '/files/docs/' . $request[ 'file_doc_field' . $field->id ]));
					}
				}
				else {
					// Replace Value in User Agreement Field
					if ($field->alias == 'AgreementText' && !empty($request[ 'doc_field' . $field->id ])) {
						$values[ $i ]['value'] = $template_values_arr['201']['value'];
					}
					else {
						$values[ $i ]['value'] = $request[ 'doc_field' . $field->id ];
					}
				}
				
				// Replace Value in Okopf Field
				if ($field->alias == 'BusinessEntityTypeName') {
					if (count($okopfs) > 0) {
						foreach ($okopfs->all() as $okopf) {
							if ($okopf->id == $request[ 'doc_field' . $field->id ]) {
								$values[ $i ]['value'] = $okopf->name;
								break;
							}
						}
					}
				}
				
				// Replace Value in Country Field
				if ($field->alias == 'CountryName') {
					if (count($countries) > 0) {
						foreach ($countries->all() as $country) {
							if ($country->id == $request[ 'doc_field' . $field->id ]) {
								$values[ $i ]['value'] = $country->name;
								break;
							}
						}
					}
				}
				
				// Replace Vars with Data in Offer Text Field
				if ($field->alias == 'DogText') {
					$values[ $i ]['value'] = str_replace('%NUMBER%', $prefix_number, $values[ $i ]['value']);
					
					if (!empty($request['doc_field41']))
						$values[ $i ]['value'] = str_replace('%ORG_NAME%', $request['doc_field41'], $values[ $i ]['value']);
					
					if (!empty($request['doc_field4']))
						$values[ $i ]['value'] = str_replace('%INN%', $request['doc_field4'], $values[ $i ]['value']);
					
					if (!empty($request['doc_field5']))
						$values[ $i ]['value'] = str_replace('%OGRN%', $request['doc_field5'], $values[ $i ]['value']);
					
					if (!empty($request['doc_field36']))
						$values[ $i ]['value'] = str_replace('%SNILS%', $request['doc_field36'], $values[ $i ]['value']);
					
					$values[ $i ]['value'] = str_replace('%DATE%', date('d.m.Y'), $values[ $i ]['value']);
				}
				
				$i++;
			}
		}
		
		// Save Doc Fields Values
		DB::table('elpts_docs_fields_values')
			->insert($values);
	}
	
	/**
	 * Verify Signature by Signal-COM DSS Server
	 *
	 * @param  string $file
	 * @param  string $signature
	 * @return array
	 */
	public function signatureVerify($file, $signature) {
		$xw = new XMLWriter();
		$xw->openMemory();
		$xw->setIndent(true);
		$xw->startElementNS('ns1', 'verifyRequest', null);
		$xw->startAttribute('xmlns');
		$xw->text('urn:oasis:names:tc:dss:1.0:core:schema');
		$xw->endAttribute();
		$xw->writeAttributeNS('xmlns', 'ns6', null, 'http://uri.etsi.org/02231/v2#');
		$xw->writeAttributeNS('xmlns', 'ns5', null, 'urn:oasis:names:tc:dss-x:1.0:profiles:verificationreport:schema#');
		$xw->writeAttributeNS('xmlns', 'ns7', null, 'http://signalcom.ru/2018/01/oasis/dss/extension');
		$xw->writeAttributeNS('xmlns', 'ns2', null, 'http://www.w3.org/2000/09/xmldsig#');
		$xw->writeAttributeNS('xmlns', 'ns4', null, 'urn:oasis:names:tc:dss:1.0:profiles:AdES:schema#');
		$xw->writeAttributeNS('xmlns', 'ns3', null, 'http://uri.etsi.org/01903/v1.3.2#');
		$xw->writeAttributeNS('xmlns', 'dss', null, 'http://dss.oasis.signalcom.ru/');
		$xw->writeAttributeNS('xmlns', 'urn', null, 'urn:oasis:names:tc:dss:1.0:core:schema');
		$xw->writeAttributeNS('xmlns', 'xd', null, 'http://www.w3.org/2000/09/xmldsig#');
		/*$xw->startElementNS('urn', "OptionalInputs", NULL);
			$xw->startElementNS('ns5', 'ReturnVerificationReport', NULL);
				$xw->startElementNS('ns5', 'IncludeVerifier', NULL);
					$xw->text("false");
				$xw->endElement();
				$xw->startElementNS('ns5', 'IncludeCertificateValues', NULL);
					$xw->text("true");
				$xw->endElement();
				$xw->startElementNS('ns5', 'ReportDetailLevel', NULL);
					$xw->text("urn:oasis:names:tc:dss-x:1.0:profiles:verificationreport:reportdetail:allDetails");
				$xw->endElement();
			$xw->endElement();
		$xw->endElement();*/
		$xw->startElementNS('urn', 'InputDocuments', null);
		$xw->startElement("Document");
		$xw->startElement("Base64Data");
		$xw->text(base64_encode($file));
		$xw->endElement();
		$xw->endElement();
		$xw->endElement();
		$xw->startElementNS('urn', 'SignatureObject', null);
		$xw->startElement("Base64Signature");
		$xw->startAttribute('Type');
		$xw->text('urn:ietf:rfc:3369');
		$xw->endAttribute();
		$xw->text($signature);
		$xw->endElement();
		$xw->endElement();
		$xw->endElement();
		
		try {
			$client = new SoapClient(config('constants.dss_uri'), [
				"soap_version" => SOAP_1_1,
				"trace" => 1,
				"style" => SOAP_DOCUMENT,
				"use" => SOAP_LITERAL,
				"cache_wsdl" => WSDL_CACHE_NONE,
			]);
			
			$verifyResponse = json_decode(json_encode($client->__soapCall('verify', [new SoapVar($xw->outputMemory(), XSD_ANYXML)])), true);
			
			$xw = null;
			
			if (($verifyResponse["Result"]["ResultMajor"] != 'urn:oasis:names:tc:dss:1.0:resultmajor:Success') || ($verifyResponse["Result"]["ResultMajor"] == 'urn:oasis:names:tc:dss:1.0:resultmajor:Success' && $verifyResponse["Result"]["ResultMinor"] == 'urn:oasis:names:tc:dss:1.0:resultminor:invalid:IncorrectSignature')) {
				return [
					'error' => [0 => $verifyResponse["Result"]["ResultMinor"]],
				];
			}
		} catch (SoapFault $fault) {
			return [
				'error' => [0 => $fault->getMessage()],
			];
		}
		
		return [
			'error' => [],
		];
	}
	
	/**
	 * Store Email Confirm Code.
	 *
	 * @param  array $request
	 * return object json
	 */
	public function storeEmailConfirmCode($request) {
		$code = Str::random(8);
		
		Mail::send('emails.email_confirm_code', ['code' => $code], function ($message) use ($request) {
			$message->to($request['email'])->subject('Код подтверждения');
		});
		
		if (count(Mail::failures()) > 0) {
			$i = 0;
			foreach (Mail::failures as $failure) {
				$response['error_code'] = [$i => $failure];
				$i++;
			}
			
			// Write Log
			$log = new Logs;
			$log->operation_id = 24;
			$log->user_name = $request['email'];
			$log->value = json_encode($response['error_code']);
			$log->save();
			
			return [
				'error' => $response['error_code'],
				'msg' => 'Ошибка при отправке письма.',
				'code' => '',
			];
		}
		
		$values = [
			0 => [
				'email' => $request['email'],
				'code' => $code,
			],
		];
		
		// Save Email Confirm Code To DB
		DB::table('elpts_email_confirmation')
			->insert($values);
		
		// Write Log
		$log = new Logs;
		$log->operation_id = 24;
		$log->user_name = $request['email'];
		$log->value = 'Да';
		$log->save();
		
		return [
			'error' => [],
			'msg' => 'Код успешно отправлен на указанный E-mail.',
			'code' => $code,
		];
	}
	
	/**
	 * Validate Email Unique.
	 *
	 * @param  string $email
	 * @param  string $ogrn
	 * @param  int $doctypes_id
	 * return object json
	 */
	public function emailUniqueValidate($email, $ogrn) {
		$rows = DB::table('elpts_docs')
			->select(['elpts_docs.*', 'val1.value as email', 'val2.value as ogrn'])
			->join('elpts_docs_fields_values as val1', function ($join) use ($email) {
				$join->on('val1.docs_id', '=', 'elpts_docs.id')
					->where('val1.fields_id', '=', '20')
					->where('val1.value', '=', $email);
			})
			->join('elpts_docs_fields_values as val2', function ($join) use ($ogrn) {
				$join->on('val2.docs_id', '=', 'elpts_docs.id')
					->where('val2.fields_id', '=', '5')
					->where('val2.value', '<>', $ogrn);
			})
			->where([
				['elpts_docs.status_id', '>', '0'],
			])
			->get();
		
		if (count($rows)) {
			foreach ($rows->all() as $value) {
				$prefix_number = $value->prefix_number;
				$email1 = $value->email;
				$ogrn1 = $value->ogrn;
				break;
			}
			return [
				'error' => [
					0 => 'Указанный E-mail уже существует.',
				],
				'prefix_number' => [
					0 => $prefix_number
				],
				'email' => [
					0 => $email1
				],
				'ogrn' => [
					0 => $ogrn1
				]
			];
		}
	}
	
	/**
	 * Check Email Registry.
	 *
	 * @param  string $email
	 * return object json
	 */
	public function checkEmailRegistry($email) {
		$rows = DB::table('elpts_email_registry')
			->where([
				['elpts_email_registry.email', '=', $email],
			])
			->get();
		
		if (count($rows)) {
			return [
				'error' => [
					0 => 'Указанный адрес электронной почты уже используется для другого участника.',
				],
			];
		}
	}
	
	/**
	 * Validate Email Confirm Code.
	 *
	 * @param  string $email
	 * @param  string $code
	 * return object json
	 */
	public function emailConfirmCodeValidate($email, $code) {
		$rows = DB::table('elpts_email_confirmation')
			->where([
				['email', '=', $email],
				['code', '=', $code],
			])
			->orderBy('id', 'desc')
			->limit(1)
			->get();
		
		if (!count($rows)) {
			return [
				'error' => [
					0 => 'Указанный код подтверждения не существует.',
				],
			];
		}
		
		$id = '';
		foreach ($rows->all() as $row) {
			if ($row->used) {
				return [
					'error' => [
						0 => 'Указанный код подтверждения уже был ранее использован.',
					],
				];
			}
			else {
				$id = $row->id;
			}
		}
		
		return [
			'error' => [],
			'email_confirm_code_id' => $id,
		];
	}
	
	/**
	 * If OGRN Exists in Docs of Any Templates In Status Other Than 'Отказано'.
	 *
	 * @param  string $ogrn
	 * return array $values_arr
	 */
	public function checkDocsForOgrn($ogrn, $doctypes_id, $templates_id) {
		$values_arr = [];
		
		switch ($doctypes_id) {
			case '1':
				$doc_id = DB::table('elpts_docs_fields_values')
					->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
					->where([
						['elpts_docs_fields_values.fields_id', '=', '5'],
						['elpts_docs_fields_values.value', '=', $ogrn],
					])
					->whereNotIn('elpts_docs.status_id', [0, 4])
					->max('elpts_docs.id');
				
				if (!$doc_id)
					return $values_arr;
				
				$doc = new Docs;
				
				// Get Doc Fields Values
				$doc_values_arr = $doc->getDocsFieldsValues([$doc_id]);
				
				if (count($doc_values_arr) > 0) {
					foreach ($doc_values_arr as $key => $value) {
						if ($value['type'] == 'file') {
							$fileName = md5(time() . $doc_id . $key) . '.pdf';
							$filePath = base_path() . '/files/docs/' . $fileName;
							file_put_contents($filePath, base64_decode($value['value']));
							$values_arr[ $key ]['value'] = $fileName;
						}
						else {
							$values_arr[ $key ]['value'] = $value['value'];
						}
					}
				}
			break;
			case '2':
				$values_arr['81']['value'] = [];
				
				/*$do_check = true;

				$rows = DB::table('elpts_templates_fields_values')
					->select('value')
					->where([
						['fields_id', '=', '204'],
						['required', '=', '1'],
						['templates_id', '=', $templates_id],
					])
					->get();

				if (!count($rows))
       				$do_check = false;*/
				
				$rows = DB::table('elpts_templates_fields_values')
					->select('value')
					->where([
						['fields_id', '=', '205'],
						['templates_id', '=', $templates_id],
					])
					->get();
				
				$prefix_arr = [];
				
				$do_check = false;
				
				if (count($rows) > 0) {
					foreach ($rows->all() as $value) {
						if (!empty($value->value)) {
							$do_check = true;
						}
						$prefix_arr = explode(';', $value->value);
					}
				}
				
				/*if (!count($prefix_arr))
				 return $values_arr;*/
				
				$rows = DB::table('elpts_docs_fields_values')
					->select('elpts_prefixes.name as prefix', 'elpts_docs.prefix_number')
					->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
					->leftJoin('elpts_prefixes', 'elpts_prefixes.id', '=', 'elpts_docs.prefix_id')
					->where([
						['elpts_docs_fields_values.fields_id', '=', '5'],
						['elpts_docs_fields_values.value', '=', $ogrn],
					])
					->whereIn('elpts_docs.status_id', [3, 8, 9])
					->distinct()
					->get();
				
				if (count($rows) > 0) {
					foreach ($rows->all() as $value) {
						if ($do_check) {
							if (in_array($value->prefix, $prefix_arr)) {
								$values_arr['81']['value'][] = $value->prefix_number;
							}
						}
						else {
							$values_arr['81']['value'][] = $value->prefix_number;
						}
					}
				}
			
			break;
		}
		
		return $values_arr;
	}
	
	/**
	 * Remove Doc With All Values
	 *
	 * @param  int $doc_id
	 * return object DB
	 */
	public function removeDoc($doc_id) {
		return DB::transaction(function () use ($doc_id) {
			DB::table('elpts_docs')->where('id', '=', $doc_id)->delete();
			DB::table('elpts_docs_fields_values')->where('docs_id', '=', $doc_id)->delete();
		});
	}
}
