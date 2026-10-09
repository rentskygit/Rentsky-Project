<?php
namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id','image_path','is_primary','order'];
    protected $casts    = ['is_primary' => 'boolean', 'order' => 'integer'];

    public function property() { return $this->belongsTo(Property::class); }

    public function getImageUrlAttribute(): ?string {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}