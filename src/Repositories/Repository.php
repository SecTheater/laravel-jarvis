<?php

namespace SecTheater\Jarvis\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    /**
     * [fetches all records]
     * @param  array  $columns [retrieve speicfic columns]
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }
    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $attribute
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public function find($attribute, array $columns = ['*'])
    {
        return $this->model->find($attribute, $columns);
    }
    /**
     * retrieves records conditionally.
     * @param  [mixed] $attribute
     * @param  string $operator
     * @param  [mixed] $value
     * @return [Illuminate\Support\Collection]
     */
    public function findBy($attribute, $operator = '=', $value = null)
    {
        if (is_array($attribute)) {
            return $this->model->where($attribute)->get();
        }
        if (func_num_args() === 2) {
            list($attribute, $value) = func_get_args();
        }

        return $this->model->where($attribute, $operator, $value)->get();
    }
    /**
     * creates a record
     * @param  array  $attributes
     * @return  Illuminate\Database\Eloquent
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }
    /**
     * Updates a specific record
     * @param  [mixed] $identifier
     * @param  array  $attributes
     * @return  Illuminate\Database\Eloquent|false
     */
    public function update($identifier, array $attributes)
    {
        if (!is_object($identifier)) {
            $identifier = $this->model->find($identifier);
        }

        return ($identifier->update($attributes)) ? $identifier : false;
    }
    /**
     * deletes a record
     * @param  [mixed] $identifier
     * @return [boolean]
     */
    public function delete($identifier)
    {
        if ($identifier instanceof Model) {
            return (bool) $identifier->delete();
        }

        return (bool) $this->model->find($identifier)->delete();
    }
    /**
     * check if record exists under condition.
     * @param  [mixed] $attribute
     * @param  string $operator
     * @param  [mixed] $value
     * @return [boolean]
    */
    public function exists($attribute, $operator = '=', $value = null)
    {
        if (is_array($attribute)) {
            return $this->model->where($attribute)->exists();
        }
        if (func_num_args() === 2) {
            list($attribute, $value) = func_get_args();
        }

        return $this->model->where($attribute, $operator, $value)->exists();
    }

    public function ordered($attribute, $order = 'desc')
    {
        return $this->model->orderBy($attribute, $order)->get();
    }

    /**
     * retrieves recent sorted by a designated column.
     * @param  array|null $attributes
     * @param  string     $column
     * @return [Illuminate\Support\Collection]
     */
    public function recent(array $attributes = null, $column = 'created_at')
    {
        if (!Schema::hasColumn($this->model->getTable(), $column)) {
            throw new ConfigException("{$this->model->getTable()} Doesn't have $column");
        }
        if (!$attributes) {
            return $this->model->all()->sortByDesc($column);
        }
        return $this->findBy($attributes)->sortByDesc($column);
    }
    /**
     * retrieves Eloquent Collection Associated with model optionally queried.
     * @param  string $relation  [description]
     * @param  string $operator  [description]
     * @param  [type] $condition [description]
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function getEloquentHave(string $relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getEloquentWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        }
        if (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }
    /**
     * retrieves a queried relational Eloquent Collection Associated with model.
     * @param  [string]     $relation
     * @param  array        $condition
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function getEloquentWhereHave(string $relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }
    /**
     * retrieves Eager Loading Eloquent Collection Associated with model.
     * @param  [string]          $relation
     * @param  [mixed]|null      $count
     * @param  [mixed]|null      $condition
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function getEloquentWith(string $relation, $count = null, $condition = null)
    {
        if (!isset($count,$condition)) {
            return $this->model->with($relation)->get();
        }
        if ($condition && $count) {
            return $this->model->where($condition)->with($relation)->withCount($count)->get();
        }
        if ($condition) {
            return $this->model->where($condition)->with($relation)->get();
        }
        if ($count) {
            return $this->model->with($relation)->withCount($count)->get();
        }

    }
    /**
     * fetches approved/unapproved eloquent collection associated with model.
     * @param  [string]       $relation
     * @param  array        $condition
     * @param  bool|boolean $approved
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function fetch(string $relation = null,array $condition = null ,bool $approved = false)
    {
        $process = strtolower($this->getModelNamePlural());
        if (!config('jarvis.'.$process.'.approve')) {
            throw new ConfigException('Approval Is not enabled for ' . $process);
        }
        if (!$condition && !$relation) {
            return $this->model->whereApproved($approved)->get();
        }
        $condition = array_merge($condition ?? [], [ $process .'.approved' => $approved]);
        if ($relation) {
            return $this->{'get' . ucfirst($process) . 'Have'}($relation, $condition);
        }
        return $this->model->where($condition)->get();
    }
    /**
     * retrieves relational eloquent collection associated with model
     * @param  [string]     $relation
     * @param  array|null $condition
     * @return [Illuminate\Database\Eloquent\Collection]
     */
    public function getEloquentWhereDoesntHave(string $relation, array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->whereDoesntHave($relation)->get();
    }
    /**
     * retrieves the plural of the model name.
     * @return string
     */
    private function getModelNamePlural()
    {
        return str_plural(str_replace('Eloquent', '', class_basename($this->model)));
    }
    /**
     * Handle dynamic method calls
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $callingMethod = str_replace(ucfirst($this->getModelNamePlural()), 'Eloquent', $method);
        if (method_exists($this,$callingMethod)) {
            return $this->{$callingMethod}(... $arguments);
        }
        if (str_contains($method, 'fetch')) {
            return $this->fetch(...$arguments);
        }
        return $this->model->$method(...$arguments);
    }

}
