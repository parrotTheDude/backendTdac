<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'template_name',
        'variables',
        'recipient_lists',
        'emails_sent'
    ];

    protected $casts = [
        'variables' => 'json',
        'recipient_lists' => 'json'
    ];
}