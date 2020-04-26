<?php

namespace Revolta77\LaravelSqlLogger\Models;

use Illuminate\Database\Eloquent\Model;

class SqlLog extends Model {

	protected $table = 'sql_logs';
	public $timestamps = true;

	protected $fillable = [
		'id', 'url', 'sql', 'execution_time', 'method', 'function', 'line', 'created_at', 'updated_at'
	];
}
