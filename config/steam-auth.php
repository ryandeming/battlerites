<?php

return [

    /*
     * Redirect URL after login
     */
    'redirect_url' => '/auth/steam/handle',
    /*
     * API Key (set in .env file) [http://steamcommunity.com/dev/apikey]
     */
    'api_key' => env('A77646AE40FD3F40862993344AC8B92C', 'A77646AE40FD3F40862993344AC8B92C'),
    /*
     * Is using https ?
     */
    'https' => false

];
