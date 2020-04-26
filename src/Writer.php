<?php

namespace Revolta77\LaravelSqlLogger;

use Revolta77\LaravelSqlLogger\Models\SqlLog;
use Carbon\Carbon;
use Revolta77\LaravelSqlLogger\Objects\SqlQuery;
use Illuminate\Support\Str;

class Writer
{
	/**
	 * @var Formatter
	 */
	private $formatter;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var FileName
	 */
	private $fileName;

	private $is_log = false;

	/**
	 * Writer constructor.
	 *
	 * @param Formatter $formatter
	 * @param Config $config
	 * @param FileName $fileName
	 */
	public function __construct(Formatter $formatter, Config $config, FileName $fileName)
	{
		$this->formatter = $formatter;
		$this->config = $config;
		$this->fileName = $fileName;
	}

	/**
	 * Save queries to log.
	 *
	 * @param array $array
	 */
	public function saveToDb( $array ){
		$file = $line = '';
		$backtracks = (object) debug_backtrace();
		$lastBuilder = false;
		if( !empty( $backtracks ) ) foreach ( $backtracks as $key => $trace ){
			$trace = (object) $trace;
			$base = base_path();
			$builder = '/vendor/laravel/framework/src/Illuminate/Database';
			$isBuilder = strpos( $trace->file, $builder ) !== false;
			if( $lastBuilder && !$isBuilder ){
				$file = str_replace( $base, '', $trace->file );
				$line = $trace->line;
				break;
			}
			if( $isBuilder ){
				$lastBuilder = true;
			}
		}
		SqlLog::create([
			'url' => $array['[origin]'],
			'sql' => $array['[query]'],
			'execution_time' => str_replace('ms', '', $array['[query_time]']),
			'method' => strtok($array['[query]'], " "),
			'function' => $file,
			'line' => $line,
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		]);
	}

	/**
	 * Save queries to log.
	 *
	 * @param SqlQuery $query
	 */
	public function save(SqlQuery $query) {

		if( !$this->is_log ){
			$array = $this->formatter->getReplace($query);

			if( $this->config->databaseLog() ){
				$this->is_log = true;
				$this->saveToDb($array);
			} else {
				$this->createDirectoryIfNotExists($query->number());

				$line = $this->formatter->getLine($array);

				if ($this->shouldLogQuery($query)) {
					$this->saveLine($line, $this->fileName->getForAllQueries(), $this->shouldOverrideFile($query));
				}

				if ($this->shouldLogSlowQuery($query)) {
					$this->saveLine($line, $this->fileName->getForSlowQueries());
				}
			}

		} else {
			$this->is_log = false;
		}

	}

	/**
	 * Create directory if it does not exist yet.
	 *
	 * @param int $queryNumber
	 */
	protected function createDirectoryIfNotExists($queryNumber)
	{
		if ($queryNumber == 1 && ! file_exists($directory = $this->directory())) {
			mkdir($directory, 0777, true);
		}
	}

	/**
	 * Get directory where file should be located.
	 *
	 * @return string
	 */
	protected function directory()
	{
		return rtrim($this->config->logDirectory(), '\\/');
	}

	/**
	 * Verify whether query should be logged.
	 *
	 * @param SqlQuery $query
	 *
	 * @return bool
	 */
	protected function shouldLogQuery(SqlQuery $query)
	{
		return $this->config->logAllQueries() &&
			preg_match($this->config->allQueriesPattern(), $query->raw());
	}

	/**
	 * Verify whether slow query should be logged.
	 *
	 * @param SqlQuery $query
	 *
	 * @return bool
	 */
	protected function shouldLogSlowQuery(SqlQuery $query)
	{
		return $this->config->logSlowQueries() && $query->time() >= $this->config->slowLogTime() &&
			preg_match($this->config->slowQueriesPattern(), $query->raw());
	}

	/**
	 * Save data to log file.
	 *
	 * @param string $line
	 * @param string $fileName
	 * @param bool $override
	 */
	protected function saveLine($line, $fileName, $override = false)
	{
		file_put_contents(
			$this->directory() . DIRECTORY_SEPARATOR . $fileName,
			$line,
			$override ? 0 : FILE_APPEND
		);
	}

	/**
	 * Verify whether file should be overridden.
	 *
	 * @param SqlQuery $query
	 *
	 * @return bool
	 */
	private function shouldOverrideFile(SqlQuery $query)
	{
		return ($query->number() == 1 && $this->config->overrideFile());
	}

	public function is_log(){
		return $this->is_log;
	}
}
