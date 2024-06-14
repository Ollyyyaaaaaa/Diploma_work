<?php

namespace App\Http\Requests;

use App\Models\PaywayCurrency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends BaseRequest
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
        $pwc = PaywayCurrency::query()->find($this->get('payway_currency_id'));
        $currency = $pwc?->currency;
        $payway = $pwc?->payway;

        return [
            'is_out' => 'required|boolean',
            'sum' => [
                'required',
                'numeric',
                $pwc ? 'min:' . $pwc?->min : '',
                $pwc ? 'max:' . $pwc?->max : ''],
            'payway_currency_id' => [
                'required',
                'integer',
                Rule::exists('payway_currencies', 'id')->where('is_active', true),
                // function ($attribute, $value, $fail) use ($currency, $payway) {
                //     if ($payway?->name !== 'card') {
                //         return $fail($attribute, __('validation.exists', ['attribute' => $attribute]));
                //     }
                //     if ($currency?->name !== 'UAH') {
                //         return $fail($attribute, __('validation.exists', ['attribute' => $attribute]));
                //     }
                //     if (!$currency?->is_active) {
                //         return $fail($attribute, __('validation.exists', ['attribute' => $attribute]));
                //     }
                // }
            ]
        ];
    }
}
