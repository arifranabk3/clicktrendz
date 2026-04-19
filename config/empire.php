<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Regional Mapping
    |--------------------------------------------------------------------------
    |
    | Strictly mapping URL segments (pk, ae, sa, qa) to their respective 
    | integer IDs in the countries table.
    |
    */
    'regions' => [
        'pk' => 1,
        'ae' => 2,
        'sa' => 3,
        'qa' => 4,
    ],

    'default_region_id' => 1, // Pakistan
];
