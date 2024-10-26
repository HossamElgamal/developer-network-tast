<?php

namespace App\common;


use App\Common\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

abstract class ApiRequest extends FormRequest
{

    abstract  public function authorize();
    abstract  public function rules();

    protected function failedValidation(Validator $validator){
        if ($this->is('api/*')){
            $response = ApiResponse::sendResponse(JsonResponse::HTTP_UNPROCESSABLE_ENTITY,'Validation Error', $validator->errors());
            throw new ValidationException($validator , $response);
        }
    }




}

