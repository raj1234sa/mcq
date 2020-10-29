<?php

use Illuminate\Http\Request;

function extract_search_field(Request $request)
{
    $array = '';
    if ($request->input('data')) {
        parse_str($request->input('data'), $array);
    }
    return $array;
}
?>