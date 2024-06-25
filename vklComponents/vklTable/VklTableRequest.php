<?php

namespace VklComponents\VklTable;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Must be used to validate request data for vklComponents/vklTable/VklTableBuilder.php
 * @package vklComponents\vklTable
 */
class VklTableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * If overwrite, need to save the existing array keys.
     *
     * @return array
     */
    public function rules()
    {
        $additionalRules = $this->additionalRules();

        return array_merge($additionalRules, [
            'search' => 'nullable|string', // String with requested search value.
            'order.*' => 'nullable|regex:/^.+:.+$/i', // Array of stings with requested columns orders with directions => ['column:direction'].
            'filter.*' => 'nullable|regex:/^.+:.+$/i', // Array of stings with requested columns filters => ['column:value1,value2'].
            'select' => 'nullable|array', // Array of requested column names that should be returned.
            'visible_columns' => 'nullable|array', // Array of requested column names that should be in export.
            'export' => 'nullable|string', // String with requested export type => 'excel'.
            'export_column_titles.*' => 'nullable|regex:/^.+:.+$/i', // Array of stings with requested export column titles => ['full_name:Name'].
            'export_column_formats.*' => 'nullable|regex:/^.+:.+$/i', // Array of stings with requested export column formats => ['shift_date:datetime'].
            'limit' => 'nullable|numeric', // Requested maximum number of records per page.
            'page' => 'nullable|numeric', // Requested page number.
        ]);
    }

    /**
     * Overwrite to add additional request validation rules.
     * @return array
     */
    public function additionalRules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response($validator->errors(), 400));
    }
}
