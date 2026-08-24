<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstructorCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $imageRules = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('parent_id', '>', 0)
                    ->whereNull('deleted_at')),
            ],
            'level' => ['required', 'integer', 'between:1,3'],
            'course_avatar' => $imageRules,
            'course_avatar_2' => $imageRules,
            'course_avatar_3' => $imageRules,
        ];
    }
}
