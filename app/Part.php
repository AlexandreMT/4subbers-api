<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $table = 'part';

    public function project() {
        return $this->belongsTo(Project::class);
    }
}
