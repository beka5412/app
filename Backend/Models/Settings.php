<?php 

namespace Backend\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings'; // Nome da tabela no banco de dados
    protected $fillable = ['name', 'value', 'description']; // Colunas que podem ser editadas
}
