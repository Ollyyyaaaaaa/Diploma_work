<?php

namespace App\Http\Requests;

use App\Models\PaywayCurrency;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CallbackTransactionRequest extends BaseRequest
{
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
        $trx = Transaction::query()->find($this->get('id'));

        return [
            'id' => [
                'required',
                'integer',
                'exists:transactions,id',
                function ($attribute, $value, $fail) use ($trx) {
                    if ($trx->status !== Transaction::STATUS_PENDING) {
                        return $fail($attribute, __('validation.exists', ['attribute' => $attribute]));
                    }
                }
            ],
            'status' => [
                'required',
                'string',
                'in:'. Transaction::STATUS_FAIL . ',' . Transaction::STATUS_SUCCESS
            ]
        ];
    }
}
