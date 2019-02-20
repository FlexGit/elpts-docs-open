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
	'is_kpp' 	   		   => 'Значение поля :attribute должно состоять только из цифр, а его длина долюна быть 9 символов.',
	'email_confirm_code'   => 'Значение поля :attribute указано некорректно.',
	'email_confirm_code_is_active' => 'Указанный код подтверждения уже был ранее использован.',
	'is_phone' 	   		   => 'Значение поля :attribute должно быть в формате 8-123-456-78-90.',
	'is_okved_exists'	   => 'Значение поля :attribute не совпадает ни с одним из заданных значений для данного шаблона Документа.',
	'check_ogrn_exists'    => 'Поле :attribute не прошло проверку. Подписание документа невозможно.',
	'ogrn_check' 		   => 'Перед подписанием поле :attribute должно пройти успешную проверку. Нажмите кнопку "Проверить наличие данных"',
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
    	'template_field1' => '"Текст договора"',
    	'template_field2' => '"Текст акцепта договора"',
    	'template_field3' => '"Текст ответа о принятии в работу акцепта договора"',
    	'template_field4' => '"Текст ответа в случае принятия акцепта договора"',
    	'template_field5' => '"Текст ответа в случае непринятия акцепта договора"',
    	'template_field7' => '"Список номеров ОКВЭД, при вводе которых позволяется акцептовать данный договор"',
    	'template_field15' => '"Префикс к номеру акцепта"',
    	'template_field16' => '"Текст заявления"',
    	'template_field17' => '"Текст ответа о принятии в работу заявления"',
    	'template_field18' => '"Текст ответа в случае принятия заявления"',
    	'template_field19' => '"Текст ответа в случае непринятия заявления"',
    	'template_field20' => '"Текст заявления"',
    	'template_field21' => '"Текст ответа о принятии в работу заявления"',
    	'template_field22' => '"Текст ответа в случае принятия заявления"',
    	'template_field23' => '"Текст ответа в случае непринятия заявления"',
    	'doc_field1' => '<a href="#doc_field1">"Текст оферты"</a>',
    	'doc_field74' => '<a href="#doc_field74">"Полное наименование организации"</a>',
    	'doc_field3' => '<a href="#doc_field3">"Организационно-правовая форма"</a>',
    	'doc_field4' => '<a href="#doc_field4">"ИНН"</a>',
    	'doc_field5' => '<a href="#doc_field5">"ОГРН/ОРГНИП"</a>',
    	'doc_field6' => '<a href="#doc_field6">"КПП"</a>',
    	'doc_field7' => '<a href="#doc_field7">"ОКВЭД"</a>',
    	'doc_field8' => '<a href="#doc_field8">"WMI"</a>',
    	'doc_field9' => '<a href="#doc_field9">"БИК"</a>',
    	'doc_field10' => '<a href="#doc_field10">"№ в едином реестре"</a>',
    	'doc_field11' => '<a href="#doc_field11">"№ в гос. реестре ломбардов"</a>',
    	'doc_field12' => '<a href="#doc_field12">"Должность"</a>',
    	'doc_field13' => '<a href="#doc_field13">"Фамилия, Имя, Отчество"</a>',
    	'doc_field14' => '<a href="#doc_field14">"Страна"</a>',
    	'doc_field15' => '<a href="#doc_field15">"Субъект"</a>',
    	'doc_field16' => '<a href="#doc_field16">"Наименование банка"</a>',
    	'doc_field17' => '<a href="#doc_field17">"БИК банка"</a>',
    	'doc_field18' => '<a href="#doc_field18">"Корреспонденский счет"</a>',
    	'doc_field19' => '<a href="#doc_field19">"Расчетный счет"</a>',
    	'doc_field20' => '<a href="#doc_field20">"Адресс электронной почты"</a>',
    	'doc_field21' => '<a href="#doc_field21">"Код подтверждения"</a>',
    	'doc_field22' => '<a href="#doc_field22">"Телефон"</a>',
    	'doc_field23' => '<a href="#doc_field23">"Устав"</a>',
    	'doc_field24' => '<a href="#doc_field24">"Свидетельство о гос регистрации ЮЛ или лист записи ЕГРЮЛ"</a>',
    	'doc_field25' => '<a href="#doc_field25">"Свидетельство о постановке на учет в налоговом органе"</a>',
    	'doc_field26' => '<a href="#doc_field26">"Документ, подтверждающий полномочия лица на осуществление действий от имени юр лица без доверенности (копия решения или протокола о назначении или избрании руководителя организации, действующее на момент заключения договора)"</a>',
    	'doc_field27' => '<a href="#doc_field27">"WMI для изготовителей"</a>',
    	'doc_field28' => '<a href="#doc_field28">"Аттестат аккредитации для Оператора ТО"</a>',
    	'doc_field29' => '<a href="#doc_field29">"Аттестат аккредитации для испытательных лабораторий"</a>',
    	'doc_field30' => '<a href="#doc_field30">"Согласие на акцепт документа"</a>',
    	'doc_field41' => '<a href="#doc_field41">"Краткое наименование организации"</a>',
    	'doc_field42' => '<a href="#doc_field42">"Корреспондентский счет"</a>',
    	'doc_field43' => '<a href="#doc_field43">"№ регистрации в реестре ОТО"</a>',
    	'doc_field44' => '<a href="#doc_field44">"Номер аттестата"</a>',
    	'doc_field45' => '<a href="#doc_field45">"Полное наименование органа по сертификации/испытательной лаборатории"</a>',
    	'doc_field46' => '<a href="#doc_field46">"Краткое наименование органа по сертификации/испытательной лаборатории"</a>',
    	'doc_field48' => '<a href="#doc_field48">"Район"</a>',
    	'doc_field49' => '<a href="#doc_field49">"Город"</a>',
    	'doc_field50' => '<a href="#doc_field50">"Населенный пункт"</a>',
    	'doc_field51' => '<a href="#doc_field51">"Улица"</a>',
    	'doc_field52' => '<a href="#doc_field52">"Дом"</a>',
    	'doc_field53' => '<a href="#doc_field53">"Номер помещения"</a>',
    	'doc_field54' => '<a href="#doc_field54">"Индекс"</a>',
    	'doc_field56' => '<a href="#doc_field56">"Местонахождение банка"</a>',
    	'certificates' => '<a href="#doc_field61">"Сертификат ЭЦП"</a>',
    	'doc_field31' => '<a href="#doc_field31">"Текст акцепта договора"</a>',
    	'doc_field32' => '<a href="#doc_field32">"Текст заявления"</a>',
    	'doc_field33' => '<a href="#doc_field33">"Организационно-правовая форма"</a>',
    	'doc_field34' => '<a href="#doc_field34">"ОГРН/ОРГНИП"</a>',
    	'doc_field35' => '<a href="#doc_field35">"Регистрационный номер заявления - основания"</a>',
    	'doc_field36' => '<a href="#doc_field36">"СНИЛС"</a>',
    	'doc_field37' => '<a href="#doc_field37">"Текст заявления"</a>',
    	'doc_field38' => '<a href="#doc_field38">"Организационно-правовая форма"</a>',
    	'doc_field39' => '<a href="#doc_field39">"ОГРН/ОРГНИП"</a>',
    	'doc_field40' => '<a href="#doc_field40">"Регистрационный номер заявления - основания"</a>',
    	'doc_field75' => '<a href="#doc_field75">"Текст заявления в свободной форме"</a>',
    	'doc_field76' => '<a href="#doc_field76">"ОКВЭД"</a>',
    	'doc_field81' => '<a href="#doc_field81">"Номер акцепта Оферты"</a>',
    	'doc_field82' => '<a href="#doc_field82">"Марка организации-изготовителя, чья продукция реализуется"</a>',
    	'doc_field83' => '<a href="#doc_field83">"Объем выпущенных/проданных/переданных в лизинг автомобилей в месяц/год"</a>',
    	'g-recaptcha-response' => '<a href="#doc_field60">"Я не робот"</a>',
    ],

];
