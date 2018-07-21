<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class BaseObserver
{
    protected $grammar = [
        'slug' => 'str_slug',
        'title' => 'title_case',
        'studly' => 'studly_case',
        'firstUpper' => 'ucfirst',
        'firstLower' => 'lcfirst',
        'allUpper' => 'strtoupper',
        'allLower' => 'strtolower',
        'startsWithUpper' => 'ucwords'
    ];
    protected function fireObserversListeners(Model $model){
        foreach ($model->observers as $field => $listener) {
                if (!$this->hasListenerOption($listener) && !$this->isExistingFunction($listener)) {
                    throw new \Exception("$listener Action neither an available option nor exist as a function.");
                }
                $this->fireListener($field,$listener,$model);
            }
    }
    protected function fireListener($field,$listener,$model){
        if ($this->isExistingFunction($listener)) {
            $model->{$field} = snake_case($listener)($model->{$field});
        }
        if ($this->hasListenerOption($listener)) {
            $model->{$field} = $this->grammar[$listener]($model->{$field});
        }
    }
    protected function isExistingFunction($listener){
        return function_exists(snake_case($listener));
    }
    protected function hasListenerOption($listener)
    {
        return array_key_exists($listener,$this->grammar);
    }
    protected function fireApprovalListeners($model){
        $needle = "jarvis." . str_replace('Eloquent','',class_basename($model)) . ".approve";
        if (config($needle) && user()->hasAnyRole(['approve-' .  str_replace('Eloquent','',class_basename($model))])) {
            $reply->approved = true;
            $reply->approved_by = user()->id;
            $reply->approved_at = date('Y-m-d H:i:s');
            $reply->updated_at = date('Y-m-d H:i:s');
        } elseif (config($needle) && !user()->hasAnyRole(['approve-'.  str_replace('Eloquent','',class_basename($model))])) {
            $reply->approved = false;
            $reply->approved_by = null;
            $reply->approved_at = null;
            $reply->updated_at = date('Y-m-d H:i:s');
        }
    }
    public function deleting(Model $model)
    {
        foreach (config('jarvis.models.package') as $key => $value) {
            if (model_exists($key) && config('jarvis.'.str_plural($key).'.register') && \Schema::hasTable(str_plural($key)) && method_exists($model, str_plural($key))) {
                if ($key == 'tag' || $key == 'role') {
                    $model->{str_plural($key)}()->detach();
                } else {
                    $model->{str_plural($key)}()->delete();
                }
            }
        }
    }
}
