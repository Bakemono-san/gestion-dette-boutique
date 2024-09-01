<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateArticleRequest extends FormRequest
{
    use RestResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'libelle' => ['sometimes', 'string', 'max:255','unique:articles,libelle'],
            'user_id' => ['sometimes', 'string', 'max:255','exists:users,id'],
            'prix' => ['sometimes', 'numeric','min:1'],
            'quantite' => ['sometimes', 'numeric','min:1'],
        ];


        return $rules;
    }

    function messages()
    {
        return [
            'libelle.unique' => 'Le libelle doit etre unique',
            'user_id.exists' => 'L\'utilisateur n\'existe pas.',
            'prix.numeric' => 'Le prix doit etre un nombre.',
            'prix.min' => 'Le prix doit etre un nombre supérieur ou égale à 1.',
            'quantite.numeric' => 'La quantité doit etre un nombre.',
            'quantite.min' => 'La quantité doit être supérieure ou égale à 1.'
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}
