<?php

namespace App\Http\Requests\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
        return [
            'email' => ['required', 'email',function ($attribute, $value, $fail){
                // Check the guard input to determine the table to query
                $table = $this->header('guard') === 'admin' ? 'admins' : 'users';

                if (!DB::table($table)->where('email', $value)->exists()) {
                    $fail("The {$attribute} does not exist in the {$table} table.");
                }
            }],
            'password' => 'required | string',
        ];
    }

    public function authenticate()
    {
        if($this->header('guard') == 'admin'){
           return  $this->checkPassword(Admin::class);
        }
        elseif($this->header('guard') == 'user')
        {
           return  $this->checkPassword(User::class);
        }
    }

    private function checkPassword($model)
    {
        $data = $model::where('email',$this->input('email'))->first();
        if(!Hash::check($this->input('password'), $data['password'])){
            throw ValidationException::withMessages([
                'password' => trans('password is wrong'),
            ]);
        }
        return $data;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
