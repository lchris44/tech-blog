<?php

namespace App\Services\Datatable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FilterService
{
    // Match modes
    const STARTS_WITH = 'startsWith';

    const CONTAINS = 'contains';

    const NOT_CONTAINS = 'notContains';

    const ENDS_WITH = 'endsWith';

    const EQUALS = 'equals';

    const NOT_EQUALS = 'notEquals';

    const IN = 'in';

    const LESS_THAN = 'lt';

    const LESS_THAN_OR_EQUAL_TO = 'lte';

    const GREATER_THAN = 'gt';

    const GREATER_THAN_OR_EQUAL_TO = 'gte';

    const BETWEEN = 'between';

    const DATE_IS = 'dateIs';

    const DATE_IS_NOT = 'dateIsNot';

    const DATE_BEFORE = 'dateBefore';

    const DATE_AFTER = 'dateAfter';

    public function __construct(
        public string $field,
        public mixed $value = null,
        public ?string $matchMode = self::CONTAINS
    ) {}

    /**
     * Build the WHERE clause for the query.
     */
    public function buildWhere(Builder &$q, ?bool $or = false): void
    {
        // Remove `.en` suffix if present
        if (Str::endsWith($this->field, '.en')) {
            $this->field = str_replace('.', '->', $this->field);
        }

        $searchParts = explode('.', $this->field);

        if (count($searchParts) <= 4) {
            $this->applyNestedWhere($q, $searchParts, $or);
        }
    }

    /**
     * Apply nested WHERE clauses based on the number of search parts.
     */
    private function applyNestedWhere(Builder &$q, array $searchParts, bool $or): void
    {
        switch (count($searchParts)) {
            case 1:
                $this->applyWhere($q, $searchParts[0], $or);
                break;
            case 2:
                $this->applyWhereHas($q, $searchParts[0], $searchParts[1], $or);
                break;
            case 3:
                $this->applyNestedWhereHas($q, $searchParts[0], $searchParts[1], $searchParts[2], $or);
                break;
            case 4:
                $this->applyDeepNestedWhereHas($q, $searchParts[0], $searchParts[1], $searchParts[2], $searchParts[3], $or);
                break;
            default:
                break;
        }
    }

    /**
     * Apply WHERE clause for a single field.
     */
    private function applyWhere(Builder &$q, string $field, bool $or): void
    {
        $method = $or ? 'orWhere' : 'where';

        // Check if the field is a JSON field (e.g., `data->key` or `data->>'key'`)
        $isJsonField = str_contains($field, '->');

        switch ($this->matchMode) {
            case self::STARTS_WITH:
                if ($isJsonField) {
                    $q->$method($field, 'LIKE', $this->value.'%');
                } else {
                    $q->$method($field, 'LIKE', $this->value.'%');
                }
                break;

            case self::NOT_CONTAINS:
                if ($isJsonField) {
                    $q->$method($field, 'NOT LIKE', '%'.$this->value.'%');
                } else {
                    $q->$method($field, 'NOT LIKE', '%'.$this->value.'%');
                }
                break;

            case self::ENDS_WITH:
                if ($isJsonField) {
                    $q->$method($field, 'LIKE', '%'.$this->value);
                } else {
                    $q->$method($field, 'LIKE', '%'.$this->value);
                }
                break;

            case self::EQUALS:
                if ($isJsonField) {
                    $q->$method($field, $this->value);
                } else {
                    $q->$method($field, '=', $this->value);
                }
                break;

            case self::NOT_EQUALS:
                if ($isJsonField) {
                    $q->$method($field, '!=', $this->value);
                } else {
                    $q->$method($field, '!=', $this->value);
                }
                break;

            case self::IN:
                // TODO: Implement IN logic
                if ($isJsonField) {
                    $q->$method(function ($query) use ($field) {
                        foreach ($this->value as $value) {
                            $query->orWhereJsonContains($field, $value);
                        }
                    });
                } else {
                    $q->$method($field, 'IN', $this->value);
                }
                break;

            case self::LESS_THAN:
                if ($isJsonField) {
                    $q->$method($field, '<', $this->value);
                } else {
                    $q->$method($field, '<', $this->value);
                }
                break;

            case self::LESS_THAN_OR_EQUAL_TO:
                if ($isJsonField) {
                    $q->$method($field, '<=', $this->value);
                } else {
                    $q->$method($field, '<=', $this->value);
                }
                break;

            case self::GREATER_THAN:
                if ($isJsonField) {
                    $q->$method($field, '>', $this->value);
                } else {
                    $q->$method($field, '>', $this->value);
                }
                break;

            case self::GREATER_THAN_OR_EQUAL_TO:
                if ($isJsonField) {
                    $q->$method($field, '>=', $this->value);
                } else {
                    $q->$method($field, '>=', $this->value);
                }
                break;

            case self::BETWEEN:
                // TODO: Implement BETWEEN logic
                if ($isJsonField) {
                    $q->$method(function ($query) use ($field) {
                        $query->whereBetween($field, $this->value);
                    });
                } else {
                    $q->$method($field, 'BETWEEN', $this->value);
                }
                break;

            case self::DATE_IS:
                if ($isJsonField) {
                    $q->$method.'Date'($field, '=', Carbon::parse($this->value)->addDay());
                } else {
                    $q->$method.'Date'($field, '=', Carbon::parse($this->value)->addDay());
                }
                break;

            case self::DATE_IS_NOT:
                if ($isJsonField) {
                    $q->$method.'Date'($field, '!=', Carbon::parse($this->value)->addDay());
                } else {
                    $q->$method.'Date'($field, '!=', Carbon::parse($this->value)->addDay());
                }
                break;

            case self::DATE_BEFORE:
                if ($isJsonField) {
                    $q->$method.'Date'($field, '<=', Carbon::parse($this->value)->addDay());
                } else {
                    $q->$method.'Date'($field, '<=', Carbon::parse($this->value)->addDay());
                }
                break;

            case self::DATE_AFTER:
                if ($isJsonField) {
                    $q->$method.'Date'($field, '>', Carbon::parse($this->value)->addDay());
                } else {
                    $q->$method.'Date'($field, '>', Carbon::parse($this->value)->addDay());
                }
                break;

            case self::CONTAINS:
            default:
                if ($isJsonField) {
                    $q->$method($field, 'LIKE', '%'.$this->value.'%');
                } else {
                    $q->$method($field, 'LIKE', '%'.$this->value.'%');
                }
                break;
        }
    }

    /**
     * Apply WHERE HAS clause for a single nested relationship.
     */
    private function applyWhereHas(Builder &$q, string $relation, string $field, bool $or): void
    {
        $method = $or ? 'orWhereHas' : 'whereHas';
        $q->$method($relation, function ($q1) use ($field) {
            $this->applyWhere($q1, $field);
        });
    }

    /**
     * Apply WHERE HAS clause for a double-nested relationship.
     */
    private function applyNestedWhereHas(Builder &$q, string $relation1, string $relation2, string $field, bool $or): void
    {
        $method = $or ? 'orWhereHas' : 'whereHas';
        $q->$method($relation1, function ($q1) use ($relation2, $field) {
            $q1->whereHas($relation2, function ($q2) use ($field) {
                $this->applyWhere($q2, $field);
            });
        });
    }

    /**
     * Apply WHERE HAS clause for a triple-nested relationship.
     */
    private function applyDeepNestedWhereHas(Builder &$q, string $relation1, string $relation2, string $relation3, string $field, bool $or): void
    {
        $method = $or ? 'orWhereHas' : 'whereHas';
        $q->$method($relation1, function ($q1) use ($relation2, $relation3, $field) {
            $q1->whereHas($relation2, function ($q2) use ($relation3, $field) {
                $q2->whereHas($relation3, function ($q3) use ($field) {
                    $this->applyWhere($q3, $field);
                });
            });
        });
    }
}
