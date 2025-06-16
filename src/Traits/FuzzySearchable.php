<?php

namespace Soliyer\LaravelFuzzySearch\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Log::info('🔍 FuzzySearch started', ['term' => $term, 'columns' => $columns]);

        if (empty($columns)) {
            // Log::warning('⚠️ No searchable_attributes defined.');
            return $query;
        }

        $search_term = '%' . $term . '%';

        return $query->where(function (Builder $query) use ($columns, $search_term) {
            foreach ($columns as $column) {
                if (is_array($column)) {
                    // Build CONCAT string manually without DB::raw in reduce
                    $concat_fields = collect($column)
                        ->map(fn($field) => "IFNULL($field, '')")
                        ->reduce(fn($carry, $field) => $carry === null ? $field : "CONCAT($carry, ' ', $field)", null);

                    // Log::info('🔗 Searching CONCAT fields', ['fields' => $column, 'sql' => $concat_fields]);

                    // Use orWhereRaw to inject the raw SQL condition
                    $query->orWhereRaw("$concat_fields LIKE ?", [$search_term]);
                } elseif (str_contains($column, '.')) {
                    [$relation, $field] = explode('.', $column);
                    // Log::info('📎 Searching nested relation', ['relation' => $relation, 'field' => $field]);

                    $query->orWhereHas($relation, function ($q) use ($field, $search_term) {
                        // Log::info('➡️ orWhereHas called inside relation', ['field' => $field]);
                        $q->where($field, 'LIKE', $search_term);
                    });
                } else {
                    // Log::info('📄 Searching direct column', ['column' => $column]);
                    $query->orWhere($column, 'LIKE', $search_term);
                }
            }
        });
    }
    // public function scopeFuzzySearch(Builder $query, string $term): Builder
    // {
    //     $columns = property_exists($this, 'searchable_attributes') ? $this->searchable_attributes : [];

    //     if (empty($columns)) {
    //         return $query;
    //     }

    //     $search_term = '%' . $term . '%';

    //     return $query->where(function (Builder $query) use ($columns, $search_term) {
    //         foreach ($columns as $column) {
    //             if (!is_array($column)) {
    //                 $query->orWhere($column, 'LIKE', $search_term);
    //             } else {
    //                 $concat_fields = collect($column)->map(function ($field) {
    //                     return "IFNULL($field, '')";
    //                 })->reduce(function ($carry, $field) {
    //                     return $carry === null ? $field : DB::raw("CONCAT($carry, ' ', $field)");
    //                 }, null);

    //                 $query->orWhere($concat_fields, 'LIKE', $search_term);
    //             }
    //         }
    //     });
    // }
}
