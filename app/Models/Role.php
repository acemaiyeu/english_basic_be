<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    use HasFactory;
    protected $table = "roles";

    protected $fillable = [
        'id', 
        'code', 
        'name', 
        'created_at',  
        'updated_at', 
        'deleted_at']; // tuỳ bạn

     public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
    
}