<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ProfileEditRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'personalemail' => 'email|required',
            'new_emergencyemail' => 'email',
        ];
    }

    public function messages()
    {
        return [
            'personalemail.email' => 'The Preferred Email must be a valid email address.',
            'personalemail.required' => 'The Preferred Email field is required.',
            'new_emergencyemail.email' => 'The Emergency Contact Email must be a valid email address.',
        ];
    }
}
