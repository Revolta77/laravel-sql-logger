<?php

namespace Revolta77\LaravelSqlLogger\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Revolta77\LaravelSqlLogger\Config;
use Revolta77\LaravelSqlLogger\Models\SqlLog;

class ClearDbCommand extends Command {
	protected $signature = 'laravelsqllogger:clear';
    protected $description = 'Remove old logs from db.';

	/**
	 * Command handle.
	 *
	 * @param Config $config
	 */
    public function handle( Config $config ) {
		$report = [];
		$report['start'] = Carbon::now()->format('Y-m-d H:i:s');
		$selectRemoveDays = $config->selectRemoveDays();
		$removeDays = $config->removeDays();
		if( $selectRemoveDays > 0 || $removeDays > 0 ){
			if( $removeDays > 0 ){
				$date = Carbon::now()->subDays( $removeDays );
				$res = SqlLog::where( 'created_at','>=', $date )->delete();
			}
			if( $selectRemoveDays > 0 && $selectRemoveDays != $removeDays){
				$date = Carbon::now()->subDays( $removeDays );
				$res = SqlLog::where( [
					[ 'created_at','>=', $date ],
					[ 'method' => 'select' ],
				])->delete();
			}
			$report['output'] = 'Deleted: ' . $res . ' records' ;
			$report['end'] = Carbon::now()->format('Y-m-d H:i:s');
			$this->info(json_encode($report));
		}
    }
}
