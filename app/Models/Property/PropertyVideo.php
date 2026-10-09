<?php
namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyVideo extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','video_path','title','order'];
    protected $casts    = ['order' => 'integer'];

    public function property() { return $this->belongsTo(Property::class); }

    public function getVideoUrlAttribute(): ?string {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }
}