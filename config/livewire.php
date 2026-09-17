<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Temporary file upload rules
    |--------------------------------------------------------------------------
    |
    | GLB files are uploaded through Livewire before Filament stores them.
    | Keep this limit aligned with ItemResource and the PHP upload limits.
    |
    */
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:102400'],
        'max_upload_time' => 30,
    ],
];
