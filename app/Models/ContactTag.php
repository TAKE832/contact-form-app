<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ContactTag extends Pivot
{
    protected $table = 'contact_tag';
    /**
     * 複数代入可能な属性
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'tag_id',
    ];

    /**
     * キャストする属性
     * 
     * @var array<int, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
