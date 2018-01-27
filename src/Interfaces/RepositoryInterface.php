<?php
namespace SecTheater\Jarvis\Interfaces;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface {

function all();
	function find($attribute);
	function findBy($attribute, $operator = '=', $value = null);
	function create(array $attributes);
	function update(Model $model, array $attributes);
	function delete(Model $model);
	function ordered($attribute, $order = 'desc');
	function exists($condition, $operator = '=', $value = null);
	function __call($method, $arguments);

}