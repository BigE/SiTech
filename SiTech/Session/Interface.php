<?php
/**
 *
 */
interface SiTech_Session_Interface
{
	/**
	 * Close the session. In this process, the session wil be saved.
	 */
	public function close();
	
	/**
	 * Destroy the session completely.
	 * 
	 * @return bool
	 */
	public function destroy();
	
	/**
	 * Get a session attribute.
	 *
	 * @param int $attribute
	 * @return mixed
	 */
	public function getAttribute($attribute);
	
	public function isStarted();
	
	/**
	 * Set a session attribute.
	 *
	 * @param int $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value);
	
	/**
	 * Start the session.
	 */
	public function start();
}
?>
