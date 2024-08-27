<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::addColumns('discussions', [
    'original_url' => ['string', 'length' => 255, 'nullable' => true],
]);
