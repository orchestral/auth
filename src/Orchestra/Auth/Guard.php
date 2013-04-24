<?php namespace Orchestra\Auth;

class Guard extends \Illuminate\Auth\Guard {
	
	/**
	 * Cached user to roles relationship
	 * 
	 * @var array
	 */
	protected $userRoles = null;

	/**
	 * Get the current user's roles of the application.
	 *
	 * If the user is a guest, empty array should be returned.
	 *
	 * @access  public
	 * @return  array
	 */
	public function roles()
	{
		$user   = $this->user();
		$roles  = array();
		$userId = 0;

		// This is a simple check to detect if the user is actually logged-in,
		// otherwise it's just as the same as setting userId as 0.
		is_null($user) or $userId = $user->id;

		// This operation might be called more than once in a request, by 
		// cached the event result we can avoid duplicate events being fired.
		if ( ! isset($this->userRoles[$userId]) or is_null($this->userRoles[$userId]))
		{
			$this->userRoles[$userId] = $this->events->until('orchestra.auth: roles', array(
				$user, 
				$roles,
			));
		}

		return $this->userRoles[$userId];
	}

	/**
	 * Determine if current user has the given role
	 *
	 * @access public
	 * @param  string   $role
	 * @return boolean
	 */
	public function is($role)
	{
		$roles = $this->roles();

		return in_array($role, $roles);
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		parent::logout();
		$this->userRoles = null;
	}
}