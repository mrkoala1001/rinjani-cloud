<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait HasDynamicTable
{
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = parent::getTable();
        $suffix = Config::get('app.table_suffix', '');

        if ($suffix && !str_ends_with($table, $suffix)) {
            return $table . $suffix;
        }

        return $table;
    }
}
