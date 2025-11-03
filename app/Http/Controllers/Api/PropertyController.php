<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends BaseController
{
    protected ?string $modelClass = Property::class;

    protected array $rules = [
        'title' => 'required',
        'price' => 'required|numeric',
        'area' => 'required|integer',
        'address' => 'required',
        'description' => 'nullable',
    ];

    protected array $messages = [
        'title.required' => 'Tiêu đề không được để trống.',
        'price.required' => 'Giá tiền không được để trống.',
        'price.numeric' => 'Giá tiền không đúng định dạng.',
        'area.required' => 'Area không được để trống.',
        'area.integer' => 'Area không đúng định dạng.',
        'address.required' => 'Địa chỉ không được để trống.'
    ];

    protected array $attributes = [
    ];

}
