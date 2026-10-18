<?php
/**
 * Prints each test name and writes filename + verdict to a log file.
 *
 * @package SikorixLoginPageCustomizer
 */

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

/**
 * PHPUnit listener for console names and a results log.
 */
class Sikorix_Login_Test_Logger implements TestListener {
	use TestListenerDefaultImplementation;

	/**
	 * Absolute path to the results log.
	 *
	 * @var string
	 */
	private $log_file;

	/**
	 * Relative path of the tests directory (for shorter log lines).
	 *
	 * @var string
	 */
	private $tests_dir;

	/**
	 * Latest non-pass verdict for the current test, if any.
	 *
	 * @var string|null
	 */
	private $current_verdict;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->tests_dir = dirname( __DIR__ );
		$log_dir         = $this->tests_dir . '/logs';

		if ( ! is_dir( $log_dir ) ) {
			mkdir( $log_dir, 0755, true );
		}

		$this->log_file = $log_dir . '/results.log';
		file_put_contents(
			$this->log_file,
			sprintf( "# Sikorix Login Page Customizer test results — %s\n", gmdate( 'c' ) )
		);
	}

	/**
	 * @param Test $test Test about to run.
	 * @return void
	 */
	public function startTest( Test $test ): void {
		$this->current_verdict = null;
	}

	/**
	 * @param Test      $test Test that finished.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function endTest( Test $test, float $time ): void {
		if ( ! $test instanceof TestCase ) {
			return;
		}

		$verdict = $this->current_verdict ? $this->current_verdict : 'PASS';
		fwrite( STDOUT, $verdict . ' ' . $test->toString() . PHP_EOL );
		$this->write_log_line( $test, $verdict );
	}

	/**
	 * @param Test      $test Test that failed an assertion.
	 * @param AssertionFailedError $e Failure.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function addFailure( Test $test, AssertionFailedError $e, float $time ): void {
		$this->current_verdict = 'FAIL';
	}

	/**
	 * @param Test      $test Test that errored.
	 * @param Throwable $t Error.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function addError( Test $test, Throwable $t, float $time ): void {
		$this->current_verdict = 'ERROR';
	}

	/**
	 * @param Test    $test Test that warned.
	 * @param Warning $e Warning.
	 * @param float   $time Duration in seconds.
	 * @return void
	 */
	public function addWarning( Test $test, Warning $e, float $time ): void {
		$this->current_verdict = 'WARNING';
	}

	/**
	 * @param Test      $test Test that was skipped.
	 * @param Throwable $t Reason.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function addSkippedTest( Test $test, Throwable $t, float $time ): void {
		$this->current_verdict = 'SKIP';
	}

	/**
	 * @param Test      $test Incomplete test.
	 * @param Throwable $t Reason.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function addIncompleteTest( Test $test, Throwable $t, float $time ): void {
		$this->current_verdict = 'INCOMPLETE';
	}

	/**
	 * @param Test      $test Risky test.
	 * @param Throwable $t Reason.
	 * @param float     $time Duration in seconds.
	 * @return void
	 */
	public function addRiskyTest( Test $test, Throwable $t, float $time ): void {
		$this->current_verdict = 'RISKY';
	}

	/**
	 * Appends one log line: relative file path, test name, verdict.
	 *
	 * @param TestCase $test    Finished test.
	 * @param string   $verdict Verdict label.
	 * @return void
	 */
	private function write_log_line( TestCase $test, $verdict ) {
		$reflection = new ReflectionClass( $test );
		$file       = $reflection->getFileName();
		$relative   = $file;

		if ( 0 === strpos( $file, $this->tests_dir . DIRECTORY_SEPARATOR ) ) {
			$relative = substr( $file, strlen( $this->tests_dir ) + 1 );
		}

		$line = sprintf(
			"%s\t%s\t%s\n",
			$verdict,
			$relative,
			$test->getName( false )
		);

		file_put_contents( $this->log_file, $line, FILE_APPEND );
	}
}
