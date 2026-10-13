<?php
/**
 * PHPUnit result printer without progress dots.
 *
 * Verdict lines are printed by {@see Sikora_Login_Test_Logger}.
 *
 * @package LoginPageCustomizer
 */

use PHPUnit\TextUI\DefaultResultPrinter;

/**
 * Suppresses `.` / `F` progress markers on stdout.
 */
class Sikora_Login_Result_Printer extends DefaultResultPrinter {

	/**
	 * @param string $progress Progress character.
	 * @return void
	 */
	protected function writeProgress( string $progress ): void {
		// Intentionally empty.
	}

	/**
	 * @param string $color  Color name.
	 * @param string $buffer Progress character.
	 * @return void
	 */
	protected function writeProgressWithColor( string $color, string $buffer ): void {
		// Intentionally empty.
	}
}
