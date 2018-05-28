<?php
namespace SecTheater\Jarvis\Model;
use Illuminate\Database\Eloquent\Model;

class EloquentModel extends Model {
	protected $guarded = [];
	public function __get($name) {
		if (!property_exists(static::class, $name) && str_contains($name, 'Model')) {
			$model = str_replace('Model', '', $name);
			if (model_exists($model)) {
				$key = 'jarvis.models.user.' . $model;
				$value = '\\' . config('jarvis.models.namespace') . ucfirst($model);
				config([$key => $value]);
				$this->{$name} = $value;
			} elseif (jarvis_model_exists($model)) {
				$this->{$name} = config('jarvis.models.package.' . $model);
			}
			return $this->{$name};
		}
		return parent::__get($name);
	}
	public function __set($key, $value) {
		if ($this->getAttribute($key) || \Schema::hasColumn($this->table,$key)) {
			parent::__set($key, $value);
			return;
		}
		$this->{$key} = $value;
	}
}
