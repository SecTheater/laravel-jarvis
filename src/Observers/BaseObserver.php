<?php
namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class BaseObserver
{
    public function deleting(Model $model)
    {
        foreach (config('jarvis.models.package') as $key => $value) {
            if (model_exists($key) && config("jarvis.".str_plural($key).".register") && \Schema::hasTable(str_plural($key)) && method_Exists($model,str_plural($key))) {
                if ($key == 'tag' || $key == 'role') {
                    $model->{str_plural($key)}()->detach();
                }else {
                    $model->{str_plural($key)}()->delete();
                }
            }
        }
    }
}