<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Base Language
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of language is your base language.
    | The base language select will be created as json file when scanning.
    | It will also be the file it reads and uploads to POEditor.
    |
    */
    'base_language' => 'en',
    'languages' => ['en'],

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    |
    | Here you define an array describing all the function names to scan files for.
    |
    */

    'tags' => ['be'],

    /*
    |--------------------------------------------------------------------------
    | Excluded directories
    |--------------------------------------------------------------------------
    |
    | Here you define which directories are excluded from scan.
    |
    */

    'excluded_directories' => ['vendor', 'storage', 'public', 'node_modules'],

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | Here you define an array describing all the file extensions to scan through.
    |
    */

    'extensions' => ['*.php'],

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Here you define your API Key for POEditor.
    |
    | More info: https://poeditor.com/account/api
    |
    */

    'api_key' => env('POEDITOR_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Project Id
    |--------------------------------------------------------------------------
    |
    | Here you define the project Id to upload / download from.
    |
    */

    'project_id' => env('POEDITOR_PROJECT_ID'),
];
