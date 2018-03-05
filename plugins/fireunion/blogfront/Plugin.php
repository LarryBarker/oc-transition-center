<?php namespace FireUnion\BlogFront;

use Backend;
use Event;
use RainLab\Blog\Models\Post as BlogPost;
use RainLab\User\Models\User as UserModel;
use System\Classes\PluginBase;

/**
 * BlogFront Plugin Information File
 */
class Plugin extends PluginBase {

	public $require = ['RainLab.User', 'KurtJensen.Passage', 'RainLab.Blog'];
	/**
	 * Returns information about this plugin.
	 *
	 * @return array
	 */
	public function pluginDetails() {
		return [
			'name' => 'fireunion.blogfront::lang.plugin.name',
			'description' => 'fireunion.blogfront::lang.plugin.description',
			'author' => 'FireUnion Kurt Jensen',
			'icon' => 'icon-pencil',
		];
	}

	/**
	 * Registers any front-end components implemented in this plugin.
	 *
	 * @return array
	 */
	public function registerComponents() {
		return [
			'FireUnion\BlogFront\Components\Ajax' => 'Ajax',
			'FireUnion\BlogFront\Components\PostList' => 'PostList',
			'FireUnion\BlogFront\Components\PostForm' => 'PostForm',
		];
	}

	public function boot() {
		UserModel::extend(function ($model) {
			$model->hasMany['blogposts'] = ['RainLab\Blog\Models\Post',
				'key' => 'author_id'];
		});

		BlogPost::extend(function ($model) {
			$model->belongsTo['author'] = ['RainLab\User\Models\User',
				'key' => 'author_id'];
		});

		Event::listen('backend.menu.extendItems', function ($manager) {
			$manager->addSideMenuItems('RainLab.Blog', 'blog', [
				'author' => [
					'label' => 'fireunion.blogfront::lang.plugin.side_menu_author',
					'icon' => 'icon-pencil-square-o',
					'code' => 'author',
					'owner' => 'RainLab.Blog',
					'url' => Backend::url('fireunion/blogfront/authors'),
				],
			]);
		});
	}

}
