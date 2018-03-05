<?php namespace FireUnion\BlogFront\Models;

use Backend\Models\User as Admin;
use Model;
use RainLab\Blog\Models\Category as BlogCategory;
use RainLab\User\Models\User;

/**
 * Author Model
 */
class Author extends Model {
	/**
	 * @var string The database table used by the model.
	 */
	public $table = 'fireunion_blogfront_authors';

	/**
	 * @var array Guarded fields
	 */
	protected $guarded = [];

	/**
	 * @var array Fillable fields
	 */
	protected $fillable = [
		'user_id',
		'admin_id',
		'categories',
		'can_publish',
		'note',
	];

	protected $jsonable = ['categories'];

	/*
		user_id
		admin_id
		categories
		can_publish
		note
	*/

	/**
	 * @var array Relations
	 */
	public $belongsTo = [
		'user' => ['RainLab\User\Models\User',
			'key' => 'user_id'],

		'admin' => ['Backend\Models\User',
			'table' => 'backend_users', 'key' => 'admin_id'],
	];

	public function getCategoriesOptions() {
		return BlogCategory::orderBy('name')->lists('name', 'id');
	}

	public function getUserOptions() {
		foreach (User::get() as $user) {
			$users[$user->id] = $user->name . ' ' . $user->surname;
		}
		return $users;
	}

	public function getAdminOptions() {
		foreach (Admin::get() as $user) {
			$users[$user->id] = $user->first_name . ' ' . $user->last_name;
		}
		return $users;
	}
}
