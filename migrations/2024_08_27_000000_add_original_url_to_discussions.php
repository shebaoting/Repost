<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (! $schema->hasColumn('discussions', 'original_url')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->string('original_url', 255)->nullable();
            });
        }
    },
    'down' => function (Builder $schema) {
        if ($schema->hasColumn('discussions', 'original_url')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->dropColumn('original_url');
            });
        }
    },
];
