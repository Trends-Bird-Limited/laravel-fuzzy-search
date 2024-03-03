<?php

namespace Soliyer\LaravelFuzzySearch\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait FuzzySearchable
{
    /**
     * Scope a query to search for a term in the model's attributes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @param string $term
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFuzzySearch(Builder $query, string $term): Builder
    {
        $columns = property_exists($this, 'searchable_attributes') ? $this->searchable_attributes : [];

        if (empty($columns)) {
            return $query;
        }

        $search_term = '%' . $term . '%';

        return $query->where(function (Builder $query) use ($columns, $search_term) {
            foreach ($columns as $column) {
                if (!is_array($column)) {
                    $query->orWhere($column, 'LIKE', $search_term);
                } else {
                    $concat_fields = collect($column)->map(function ($field) {
                        return "IFNULL($field, '')";
                    })->reduce(function ($carry, $field) {
                        return $carry === null ? $field : DB::raw("CONCAT($carry, ' ', $field)");
                    }, null);

                    $query->orWhere($concat_fields, 'LIKE', $search_term);
                }
            }
        });
    }
}
