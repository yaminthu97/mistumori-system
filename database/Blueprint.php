<?php

namespace database;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

class Blueprint extends BaseBlueprint
{
    /**
     * Add automatic creation and update and delete dateTimes to the table.
     *
     * @param  int  $precision
     */
    public function dateTimes($precision = 0): void
    {
        $this->datetime('created_at');
        $this->datetime('updated_at')->nullable();
    }

    public function softDeletesDateTime($precision = 0): void
    {
        $this->datetime('deleted_at')->nullable();
    }
}
