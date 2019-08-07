<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;
use GuzzleHttp\Client;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // SNILS
		Validator::extend('is_snils', function($attribute, $value, $parameters, $validator)
		{
			$pattern = '/^\d{3}-\d{3}-\d{3}-\d{2}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

		// INN
		Validator::extend('is_inn', function($attribute, $value, $parameters, $validator) {
			$pattern1 = '/^\d{10}$/';
			$pattern2 = '/^\d{12}$/';

			if(preg_match($pattern1, $value) || preg_match($pattern2, $value))
                return true;

            return false;
        });

		// OGRN
		Validator::extend('is_ogrn', function($attribute, $value, $parameters, $validator) {
			$pattern1 = '/^\d{13}$/';
			$pattern2 = '/^\d{15}$/';

			if(preg_match($pattern1, $value) || preg_match($pattern2, $value))
                return true;

            return false;
        });

		// KPP
		Validator::extend('is_kpp', function($attribute, $value, $parameters, $validator) {
			$pattern = '/^\d{9}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

		// Phone
		Validator::extend('is_phone', function($attribute, $value, $parameters, $validator) {
			$pattern = '/^8-\d{3}-\d{3}-\d{2}-\d{2}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

        // Check if OKVED exists in Document Template
    	Validator::extend('is_okved_exists', function($attribute, $value, $parameters, $validator)
		{
			$inputs = $validator->getData();

			$rows = DB::table('elpts_templates_fields_values')
				->where([
					['fields_id', '=', '7'],
					['templates_id', '=', $inputs['templates_id']],
				])
				->get();

			$template_values_arr = [];
			if (count($rows) > 0)
			{
	        	foreach ($rows->all() as $row)
	        	{
	       			$template_values_arr = explode(';', $row->value);
	        	}
	        }

	        if (count($template_values_arr) == 1 && !$template_values_arr[0])
	        	return true;

			$values_arr = explode(';', $value);
			if (count($values_arr) > 0)
			{
				foreach ($values_arr as $v)
				{
			        if(in_array($v, $template_values_arr, true))
			        	return true;
				}
			}

			return false;
        });

        // Check For OGRN
    	Validator::extend('check_ogrn_exists', function($attribute, $value, $parameters, $validator)
		{
			$inputs = $validator->getData();

			// If OGRN Exists in Docs of Current Template In Status Other Than 'Отказано'
			$rows = DB::table('elpts_docs_fields_values')
				->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
				->where([
					['elpts_docs_fields_values.fields_id', '=', '5'],
					['elpts_docs_fields_values.value', '=', $value],
					['elpts_docs.templates_id', '=', $inputs['templates_id']]
				])
				->whereNotIn('elpts_docs.status_id', [0,4])
				->get();

				if (count($rows) > 0)
		        	return false;

			return true;
        });

        // Check if OGRN was Checked :)
    	Validator::extend('ogrn_check', function($attribute, $value, $parameters, $validator)
		{
			if(!$value)
	        	return false;

			return true;
        });

        // Сertificate
		Validator::extend('certificate', function($attribute, $value, $parameters, $validator)
		{
			if($value > -1)return true;

            return false;
        });
    }
}
