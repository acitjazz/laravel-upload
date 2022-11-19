<?php

return [
    //namespace
    'namespace' => 'App\UploadEndpoint',

    //database connection
    'database' => 'mongodb',

    // Bucket Name
    // Change config filesystems Symbol links if you want to change this bucket name
    // 'links' => [
    //      public_path('storage') => storage_path('app/yourbucketname'),
    //  ],

    'bucket_name' => 'public',

    //upload path
    'upload_path' => storage_path('app'),

    //validations
    'validation_rules' => [
      //  'file' => 'required|max:2048|mimes:jpeg,jpg,png,gif',
        'name' => '',
        'folder'=> ''
    ],

    //validation messages
    'validation_messages' =>  [
        'file.required'  => 'File yang akan diupload harus dipilih',
        'file.mimes'       => 'Jenis file yang diupload harus berupa jpeg,jpg,png,gif',
        'file.max'       => 'Maximal file 2Mb',
    ],
    
];