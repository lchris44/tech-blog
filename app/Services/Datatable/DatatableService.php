<?php

namespace App\Services\Datatable;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ReflectionClass;
use Throwable;

class DatatableService
{
    private Builder $query; // The query builder instance

    private ?int $currentPage; // Current page for pagination

    private ?string $sort; // Field to sort by

    private ?string $sortDirection; // Sort direction (asc/desc)

    private array $searchableColumns; // List of searchable columns

    private int $perPage; // Number of items per page

    private array $filters; // Filters to apply

    private array $dtParams; // DataTable parameters from the request

    /**
     * Constructor.
     * Initializes the DataTable parameters and searchable columns from the request.
     */
    public function __construct()
    {
        $this->dtParams = request()->get('dt_params', []);
        $this->searchableColumns = array_map(function ($column) {
            return Str::endsWith($column, '.en') ? Str::beforeLast($column, '.en') : $column;
        }, request()->get('searchable_columns', []));
    }

    /**
     * Set custom DataTable parameters.
     *
     * @return $this
     */
    public function dtParams(array $params): self
    {
        $this->dtParams = $params;

        return $this;
    }

    /**
     * Set searchable columns.
     *
     * @return $this
     */
    public function searchableColumns(array $searchableColumns): self
    {
        $this->searchableColumns = $searchableColumns;

        return $this;
    }

    /**
     * Set the query builder instance.
     *
     * @return $this
     */
    public function query(Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Create a new instance with the given query.
     */
    public static function of(Builder $query): self
    {
        return (new self)->query($query);
    }

    /**
     * Build and execute the DataTable query.
     */
    public function make(): LengthAwarePaginator
    {
        // Extract pagination and sorting parameters
        $this->currentPage = collect($this->dtParams)->get('page', 0) + 1;
        $this->perPage = collect($this->dtParams)->get('rows', 10);
        $this->sort = collect($this->dtParams)->get('sortField');
        $this->sortDirection = collect($this->dtParams)->get('sortOrder') == 1 ? 'asc' : 'desc';

        // Extract filters
        $filters = collect($this->dtParams)->get('filters', []);
        $globalFilter = collect($filters)->get('global');
        $localFilters = collect($filters)->except('global');

        // Apply global and local filters
        $this->applyGlobalFilter($globalFilter);
        $this->applyLocalFilters($localFilters);

        // Load relationships for searchable columns
        $this->loadRelationships();

        // Apply sorting
        $this->applySort();

        // Execute the query and return paginated results
        return $this->query->paginate($this->perPage, page: $this->currentPage);
    }

    /**
     * Apply global filter to the query.
     */
    private function applyGlobalFilter(?array $globalFilter): void
    {
        if (! $globalFilter || ! isset($globalFilter['value'])) {
            return;
        }

        $value = $globalFilter['value'];
        $matchMode = $globalFilter['matchMode'] ?? FilterService::CONTAINS;

        // Apply the global filter to all searchable columns
        $this->query->where(function (Builder $q) use ($value, $matchMode) {
            foreach ($this->searchableColumns as $index => $column) {
                $filter = new FilterService($column, $value, $matchMode);
                $this->applyFilter($filter, $q, $index > 0); // Use OR for additional columns
            }
        });
    }

    /**
     * Apply local filters to the query.
     *
     * @param  mixed  $localFilters
     */
    private function applyLocalFilters($localFilters): void
    {
        $this->query->where(function (Builder $q) use ($localFilters) {
            foreach ($localFilters as $field => $filter) {
                if (isset($filter['constraints'])) {
                    foreach ($filter['constraints'] as $constraint) {
                        if (isset($constraint['value']) && $constraint['value']) {
                            $filterInstance = new FilterService($field, $constraint['value'], $constraint['matchMode']);
                            $this->applyFilter($filterInstance, $q, $filter['operator'] == 'or');
                        }
                    }
                } else {
                    $value = $filter['value'] ?? null;
                    if ($value !== null) {
                        $filterInstance = new FilterService($field, $value, $filter['matchMode'] ?? FilterService::CONTAINS);
                        $this->applyFilter($filterInstance, $q);
                    }
                }
            }
        });
    }

    /**
     * Load relationships for searchable columns.
     */
    private function loadRelationships(): void
    {
        $with = collect([]);
        foreach ($this->searchableColumns as $columnName) {
            $exploded = explode('.', $columnName);
            if (count($exploded) == 2) {
                $with->push($exploded[0]); // Single-level relationship
            } elseif (count($exploded) == 3) {
                $with->push($exploded[0].'.'.$exploded[1]); // Nested relationship
            }
        }
        $this->query->with($with->toArray());
    }

    /**
     * Apply sorting to the query.
     */
    private function applySort(): void
    {
        if ($this->sort !== null) {
            $key = explode('.', $this->sort);
            if (count($key) === 1) {
                // Sort by a direct column
                $this->query->orderBy($this->sort, $this->sortDirection ?? 'asc');
            } elseif (count($key) === 2) {
                // Sort by a related column
                $relationship = $this->getRelatedFromMethodName($key[0], get_class($this->query->getModel()));
                if ($relationship) {
                    $parentTable = $relationship->getParent()->getTable();
                    $relatedTable = $relationship->getRelated()->getTable();
                    if ($relationship instanceof HasOne) {
                        $parentKey = explode('.', $relationship->getQualifiedParentKeyName())[1];
                        $relatedKey = $relationship->getForeignKeyName();
                    } else {
                        $parentKey = $relationship->getForeignKeyName();
                        $relatedKey = $relationship->getOwnerKeyName();
                    }

                    $this->query->orderBy(
                        get_class($relationship->getRelated())::query()
                            ->select($key[1])
                            ->whereColumn("$parentTable.$parentKey", "$relatedTable.$relatedKey"),
                        $this->sortDirection ?? 'asc'
                    );
                }
            }
        }
    }

    /**
     * Get a relationship instance from a method name.
     *
     * @return HasOne|BelongsTo|null
     */
    private function getRelatedFromMethodName(string $methodName, string $className): ?object
    {
        try {
            $method = (new ReflectionClass($className))->getMethod($methodName);

            return $method->invoke(new $className);
        } catch (Throwable $exception) {
            Log::error('Failed to get relationship: '.$exception->getMessage());

            return null;
        }
    }

    /**
     * Apply a filter to the query.
     */
    private function applyFilter(FilterService $filter, Builder &$q, bool $or = false): void
    {
        $filter->buildWhere($q, $or);
    }
}
