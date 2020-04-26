<?php

namespace Revolta77\LaravelSqlLogger;

use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Config
{
	/**
	 * @var ConfigRepository
	 */
	protected $repository;

	/**
	 * Config constructor.
	 *
	 * @param ConfigRepository $repository
	 */
	public function __construct(ConfigRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Get directory where log files should be saved.
	 *
	 * @return string
	 */
	public function logDirectory()
	{
		return $this->repository->get('sql_logger.general.directory');
	}

	/**
	 * Whether query execution time should be converted to seconds.
	 *
	 * @return bool
	 */
	public function useSeconds()
	{
		return (bool) $this->repository->get('sql_logger.general.use_seconds');
	}

	/**
	 * Get suffix for console logs.
	 *
	 * @return string
	 */
	public function consoleSuffix()
	{
		return (string) $this->repository->get('sql_logger.general.console_log_suffix');
	}

	/**
	 * Whether all queries should be logged.
	 *
	 * @return bool
	 */
	public function databaseLog() {
		return $this->repository->get('sql_logger.database.database_log');
	}

	/**
	 * Days after clear selects from db.
	 *
	 * @return number
	 */
	public function selectRemoveDays() {
		return $this->repository->get('sql_logger.database.select_remove_days');
	}

	/**
	 * Days after clear all from db.
	 *
	 * @return number
	 */
	public function removeDays() {
		return $this->repository->get('sql_logger.database.remove_days');
	}

	/**
	 * Get file extension for logs.
	 *
	 * @return string
	 */
	public function fileExtension()
	{
		return $this->repository->get('sql_logger.general.extension');
	}

	/**
	 * Whether all queries should be logged.
	 *
	 * @return bool
	 */
	public function logAllQueries()
	{
		return (bool) $this->repository->get('sql_logger.all_queries.enabled');
	}

	/**
	 * Whether SQL log should be overridden for each request.
	 *
	 * @return bool
	 */
	public function overrideFile()
	{
		return (bool) $this->repository->get('sql_logger.all_queries.override_log');
	}

	/**
	 * Get pattern for all queries.
	 *
	 * @return string
	 */
	public function allQueriesPattern()
	{
		return $this->repository->get('sql_logger.all_queries.pattern');
	}

	/**
	 * Get file name (without extension) for all queries.
	 *
	 * @return string
	 */
	public function allQueriesFileName()
	{
		return $this->repository->get('sql_logger.all_queries.file_name');
	}

	/**
	 * Whether slow queries should be logged.
	 *
	 * @return bool
	 */
	public function logSlowQueries()
	{
		return (bool) $this->repository->get('sql_logger.slow_queries.enabled');
	}

	/**
	 * Minimum execution time (in milliseconds) to consider query as slow.
	 *
	 * @return float
	 */
	public function slowLogTime()
	{
		return $this->repository->get('sql_logger.slow_queries.min_exec_time');
	}

	/**
	 * Get pattern for slow queries.
	 *
	 * @return string
	 */
	public function slowQueriesPattern()
	{
		return $this->repository->get('sql_logger.slow_queries.pattern');
	}

	/**
	 * Get file name (without extension) for slow queries.
	 *
	 * @return string
	 */
	public function slowQueriesFileName()
	{
		return $this->repository->get('sql_logger.slow_queries.file_name');
	}

	/**
	 * Whether new lines should be converted to spaces.
	 *
	 * @return string
	 */
	public function newLinesToSpaces()
	{
		return $this->repository->get('sql_logger.formatting.new_lines_to_spaces');
	}

	/**
	 * Get query format that should be used to save query.
	 *
	 * @return string
	 */
	public function entryFormat()
	{
		return $this->repository->get('sql_logger.formatting.entry_format');
	}
}
