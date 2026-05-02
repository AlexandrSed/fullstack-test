<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentsModel extends Model
{
    protected $table = 'comments';

    protected $allowedFields = ['name', 'text', 'date'];
}