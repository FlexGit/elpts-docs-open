<?xml version="1.0" encoding="UTF-8"?><root>@if(count($xml_arr[0]))@foreach($xml_arr[0] as $k => $v)<{{ $v['alias'] }}>@if(count($xml_arr[$k]))@foreach($xml_arr[$k] as $k2 => $v2)<{{ $v2['alias'] }}>{{ $v2['value'] }}</{{ $v2['alias'] }}>@endforeach @else{{ $v['value'] }}@endif</{{ $v['alias'] }}>@endforeach @endif</root>