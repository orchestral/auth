<?php namespace Orchestra\Auth;

use Illuminate\Support\Facades\Event;

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
		$user    = $this->user;
		$roles   = array();
		$user_id = 0;

		// only search for roles when user is logged
		is_null($user) or $user_id = $user->id;

		if ( ! isset($this->userRoles[$user_id]) or is_null($this->userRoles[$user_id]))
		{
			$this->userRoles[$user_id] = Event::until('orchestra.auth: roles', array(
				$user, 
				$roles,
			));
		}

		return $this->userRoles[$user_id];
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
}