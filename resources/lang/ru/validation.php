<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

	'is_snils' 	   		   => 'Значение поля :attribute должно быть в формате 123-456-789-01.',
	'is_inn' 	   		   => 'Значение поля :attribute должно состоять только из цифр, а его длина должна быть 10 или 12 символов.',
	'is_ogrn' 	   		   => 'Значение поля :attribute должно состоять только из цифр, а его длина должна быть 13 или 15 символов.',
	'is_kpp' 	   		   => 'Значение поля :attribute должно состоять только из цифр, а его длина должна быть 9 символов.',
	'email_confirm_code'   => 'Значение поля :attribute указано некорректно.',
	'email_confirm_code_is_active' => 'Указанный код подтверждения уже был ранее использован.',
	'is_phone' 	   		   => 'Значение поля :attribute должно быть в формате 8-123-456-78-90.',
	'is_okved_exists'	   => 'Значение поля :attribute не совпадает ни с одним из заданных значений для данного шаблона Документа.',
	'check_ogrn_exists'    => 'Поле :attribute не прошло проверку. Акцепт данной Оферты уже существует. Подписание документа невозможно.',
	'ogrn_check' 		   => 'Перед подписанием значение поля :attribute должно пройти проверку. Нажмите кнопку "Проверить"',
	'certificate'   	   => 'Поле :attribute обязательно для заполнения.',
    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'Значение поля :attribute не является электронным адресом.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field must have a value.',
    'gt'                   => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'Значение поля :attribute должно быть целочисленным.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'lt'                   => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max'                  => [
        'numeric' => 'Значение поля :attribute не должно быть больше чем :max.',
        'file'    => 'Размер файла :attribute не должен превышать :max Кб.',
        'string'  => 'Значение поля :attribute не должно быть больше чем :max символов.',
        'array'   => 'Значение поля :attribute не должно быть больше чем :max элементов.',
    ],
    'mimes'                => 'Файл :attribute должен быть в формате: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'Значение поля :attribute должно быть не менее :min.',
        'file'    => 'Значение поля :attribute должно быть не менее :min Кб.',
        'string'  => 'Значение поля :attribute должно быть не менее :min символов.',
        'array'   => 'Значение поля :attribute должно содержать не менее :min элементов.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'Некорректный формат поля :attribute.',
    'required'             => 'Поле :attribute обязательно для заполнения.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'Размер файла :attribute должен быть равен :size Кб.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'Значение поля :attribute уже существует.',
    'uploaded'             => 'Файл :attribute не корректно загружен.',
    'url'                  => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
    	'name' => '"Наименование"',
    	'doctypes_id' => '"Тип документа"',
    	'id' => '"Код"',
    	'snils' => '<a href="#doc_field5">"СНИЛС"</a>',
    	'ogrn' => '<a href="#doc_field5">"ОГРН/ОГРНИП"</a>',
    	'ogrn_check' => '<a href="#doc_field5">"ОГРН/ОГРНИП"</a>',
    	'email' => '<a href="#doc_field20">"Адресс электронной почты"</a>',
		'certificates' => '<a href="#doc_field61">"Сертификат ЭЦП"</a>',
    	'g-recaptcha-response' => '<a href="#doc_field60">"Я не робот"</a>',
    ],

];
