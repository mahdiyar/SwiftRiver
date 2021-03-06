<?php defined('SYSPATH') or die('No direct access');

/**
 * Overrides the default exception handler
 *
 * PHP version 5
 * LICENSE: This source file is subject to the AGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/agpl.html
 * @author      Ushahidi Team <team@ushahidi.com> 
 * @package     Swiftriver - http://github.com/ushahidi/SwiftRiver
 * @subpackage  Libraries
 * @copyright   Ushahidi - http://www.ushahidi.com
 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
 */
class Kohana_Exception extends Kohana_Kohana_Exception {
 
	/**
	 * Exception handler, logs the exception and generates a 
	 * Response object for display
	 *
	 * @param   Exception $e
	 * @return  boolean
	 */
	public static function _handler(Exception $e)
	{
		// Log the error
		Kohana::$log->add(Log::ERROR, $e->getMessage());

		// Generate the response
		$response = Response::factory();
		
		$view = Swiftriver::get_base_error_view();
		$view->content = View::factory('pages/errors/default');
		
		$response->body($view->render());

		return $response;
	}
}