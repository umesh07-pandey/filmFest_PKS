<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table="event";

    protected $fillable=[
        "name",
        "description",
        "starting_date",
        "ending_date",
        "image",
        "status",
        "isActive",
        "venue_name",
        "address_line",
        "city",
        "state",
        "country",
        "pin_code",
       "event_capicity", 
        "admin_id",
        "category_id"
    ];
    public function AuthModel(){
        return $this->belongsTo(AuthModel::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }

}
