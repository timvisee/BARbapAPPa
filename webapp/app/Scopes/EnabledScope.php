<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EnabledScope implements Scope {

    protected $field;

    /**
     * Enabled scope.
     *
     * @param string [$field='enabled'] Enabled field name.
     */
    public function __construct($field = 'enabled') {
        $this->field = $field;
    }

    /**
     * Apply the enabled scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) {
        $builder->where($this->field, true);
    }
}
