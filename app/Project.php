<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    public function parts()
    {
        return $this->hasMany(Part::class, 'id', 'id');
    }
}
