<?php namespace FireUnion\BlogFront\Traits;

use Auth;
use Backend\Models\User;
use Cms\Classes\Page;
use FireUnion\BlogFront\Models\Author;
use Lang;
use RainLab\Blog\Models\Category as BlogCategory;
use RainLab\Blog\Models\Post as BlogPost;
use RainLab\User\Models\UserGroup;
use ValidationException;

trait Loaders {
	/**
	 * A collection of posts to display
	 * @var Collection
	 */
	public $posts;

	/**
	 * A single post to display
	 * @var Collection
	 */
	public $post;
	/**
	 * Allowed CategoryIds for posts
	 * @var Array
	 */
	public $categoryIds = [];
	/**
	 * A collection of categories that have Ids in $categoryIds
	 * @var Collection
	 */
	public $categories;
	/**
	 * tinyInt indicating if user is allowed to publish
	 * @var Integer
	 */
	public $isAjax = false;
	/**
	 * A single Author model with delegation settings
	 * @var Collection
	 */
	public $author = null;
	/**
	 * tinyInt indicating if user is allowed to publish
	 * @var Integer
	 */
	public $allowpublish;
	/**
	 * tinyInt indicating if user is allowed to upload images
	 * @var Integer
	 */
	public $allow_images;
	/**
	 * Slug property
	 * @var string
	 */
	public $slug = '';
	/**
	 * CategoryIds for current post record post
	 * @var Array
	 */
	public $postCategoryIds;
	/**
	 * User group IDs that author can email notices to on saving of post.
	 * @var Array
	 */
	public $user_groups;
	/**
	 * Backend user ID for saving post
	 * @var Integer
	 */
	public $backUser = null;
	/**
	 * Frontend logged in user
	 * @var Integer
	 */
	public $user = null;
	/**
	 * Frontend logged in user
	 * @var Integer
	 */
	public $restrict_owner = 1;

	public function propertiesFor($type) {
		$properties =
			[
			'use_authors' => [
				'title' => 'fireunion.blogfront::lang.general.use_authors_title',
				'description' => 'fireunion.blogfront::lang.general.use_authors_description',
				'type' => 'dropdown',
				'default' => '1',
				'options' => [
					0 => 'fireunion.blogfront::lang.general.opt_no',
					1 => 'fireunion.blogfront::lang.general.opt_yes',
				],
			],
			'restrict_owner' => [
				'title' => 'fireunion.blogfront::lang.general.restrict_owner_title',
				'description' => 'fireunion.blogfront::lang.general.restrict_owner_description',
				'type' => 'dropdown',
				'default' => '1',
				'options' => [
					0 => 'fireunion.blogfront::lang.general.opt_no',
					1 => 'fireunion.blogfront::lang.general.opt_yes',
				],
			],
			'category' => [
				'title' => 'fireunion.blogfront::lang.general.category_title',
				'description' => 'fireunion.blogfront::lang.general.category_description',
				'type' => 'set',
				'items' => $this->getCategoryItems(),
			],
			'backUser' => [
				'title' => 'fireunion.blogfront::lang.general.back_user_title',
				'description' => 'fireunion.blogfront::lang.general.back_user_description',
				'type' => 'dropdown',
			],
		];

		if ($type == 'form' || $type == 'ajax') {
			$properties = array_merge($properties,
				[
					'allowpublish' => [
						'title' => 'fireunion.blogfront::lang.general.allow_pub_title',
						'description' => 'fireunion.blogfront::lang.general.allow_pub_description',
						'type' => 'dropdown',
						'default' => '1',
						'options' => [
							0 => 'fireunion.blogfront::lang.general.opt_no',
							1 => 'fireunion.blogfront::lang.general.opt_yes',
						],
					],
					'non_std_fields' => [
						'title' => 'fireunion.blogfront::lang.general.non_std_fields_title',
						'description' => 'fireunion.blogfront::lang.general.non_std_fields_description',
						'type' => 'text',
						'default' => '',
					],
				]);
		}

		switch ($type) {
		case 'ajax':
			$properties = array_merge($properties,
				[
					'user_groups' => [
						'title' => 'fireunion.blogfront::lang.general.user_groups_title',
						'description' => 'fireunion.blogfront::lang.general.user_groups_description',
						'type' => 'set',
						'items' => $this->getGroupOptions(),
					],
					'postPage' => [
						'title' => 'rainlab.blog::lang.settings.posts_post',
						'description' => 'rainlab.blog::lang.settings.posts_post_description',
						'type' => 'dropdown',
						'options' => $this->getPageOptions(),
						'default' => 'blog/post',
						'group' => 'Links',
					],
				]);
			break;
		case 'form':
			$properties = array_merge($properties,
				[
					'allow_images' => [
						'title' => 'fireunion.blogfront::lang.author.allow_images',
						'description' => 'fireunion.blogfront::lang.author.allow_images_comment',
						'type' => 'dropdown',
						'default' => '1',
						'options' => [
							0 => 'fireunion.blogfront::lang.general.opt_no',
							1 => 'fireunion.blogfront::lang.general.opt_yes',
						],
					],
					'slug' => [
						'title' => 'rainlab.blog::lang.settings.post_slug',
						'description' => 'rainlab.blog::lang.settings.post_slug_description',
						'default' => '{{ :slug }}',
						'type' => 'string',
					],
					'user_groups' => [
						'title' => 'fireunion.blogfront::lang.general.user_groups_title',
						'description' => 'fireunion.blogfront::lang.general.user_groups_description',
						'type' => 'set',
						'items' => $this->getGroupOptions(),
					],
					'listPage' => [
						'title' => 'fireunion.blogfront::lang.general.listpage_title',
						'description' => 'fireunion.blogfront::lang.general.listpage_description',
						'type' => 'dropdown',
						'options' => $this->getPageOptions(),
						'default' => 'blog/list',
						'group' => 'Links',
					],
					'postPage' => [
						'title' => 'rainlab.blog::lang.settings.posts_post',
						'description' => 'rainlab.blog::lang.settings.posts_post_description',
						'type' => 'dropdown',
						'options' => $this->getPageOptions(),
						'default' => 'blog/post',
						'group' => 'Links',
					],
				]);
			break;
		case 'list':
			$properties = array_merge($properties,
				[
					'formPage' => [
						'title' => 'fireunion.blogfront::lang.general.formpage_title',
						'description' => 'fireunion.blogfront::lang.general.formpage_description',
						'type' => 'dropdown',
						'options' => $this->getPageOptions(),
						'default' => 'blog/post',
						'group' => 'Links',
					],

				]);
		}
		return $properties;
	}

	public function initFor($type) {
		$this->user = Auth::getUser();
		if ($this->property('use_authors')) {
			if ($this->user) {
				$this->author = Author::where('user_id', $this->user->id)->first();

				if (!$this->author && $this->property('use_authors')) {
					return false;
				}
			} else {
				return false;
			}
		}

		$this->restrict_owner = $this->property('restrict_owner');

		if ($type == 'form' || $type == 'ajax') {
			$this->postPage = $this->controller->pageUrl($this->property('postPage'), false);
		}
		if ($type == 'form') {
			$this->slug = $this->property('slug', '');
			$this->allow_images = $this->author ? $this->author->allow_images : $this->property('allow_images');
		}

		$this->backUser = $this->author ? $this->author->admin_id : $this->property('backUser');
		$this->categoryIds = $this->author ? $this->author->categories : $this->property('category');
		$this->allowpublish = $this->author ? $this->author->can_publish : $this->property('allowpublish');
		$this->categories = BlogCategory::whereIn('id', $this->categoryIds)->get();
		if ($user_group_ids = $this->property('user_groups')) {
			$this->user_groups = UserGroup::whereIn('id', $user_group_ids)->orderBy('name')->lists('name', 'id');
		}

	}

	public function runFor($type) {
		if (!$this->backUser) {
			return false;
		}
		if ($type == 'form' || $type == 'ajax') {
			$this->addJs('//cdn.ckeditor.com/4.5.4/standard/ckeditor.js');
			$this->addJs('/plugins/fireunion/blogfront/assets/js/bpost.js');
			$this->addCss('/plugins/fireunion/blogfront/assets/css/style.css');
		}

		switch ($type) {
		case 'form':
			$this->listPage = $this->controller->pageUrl($this->property('listPage'), []);
			break;
		case 'list':
			$this->formPage = $this->controller->pageUrl($this->property('formPage'), []);
		case 'ajax':
			$this->posts = $this->loadPosts();
		}
	}

	protected function loadPost() {
		$post = null;
		if (post('id') or $this->slug) {
			if (post('id')) {
				// Load after form submit
				$post_query = BlogPost::where('id', post('id'));
			} else {
				// Load from slug
				$post_query = BlogPost::where('slug', $this->slug);
			}

			$post_query->whereHas('categories', function ($query) {
				$query->whereIn('id', $this->categoryIds);
			});

			if ($this->restrict_owner) {
				$post_query->where('author_id', $this->user->id);
			}
			$post = $post_query->first();

		}
		// If not found then return a new post
		if (!$post) {
			return new BlogPost();
		}

		$this->postCategoryIds = $post->categories->lists('id');

		return $post;
	}

	protected function loadPosts() {

		$posts_query = BlogPost::with('categories')->
			whereHas('categories', function ($query) {
			$query->whereIn('id', $this->categoryIds);
		}
		);
		if ($this->restrict_owner) {
			$posts_query->where('author_id', $this->user->id);
		}
		$posts = $posts_query->get();
		/*
			         * Add a "url" helper attribute for linking to each post and category
		*/
		$posts->each(function ($post) {
			$post->setUrl($this->property('formPage'), $this->controller);
		});

		return $posts;
	}

	/**
	 * Ajax handler to save an event from form
	 * triggers onRun to show list after delete
	 * @return array for a flash like error message if there is a problem with form validation
	 */
	public function save() {
		if (!$this->backUser) {
			return false;
		}

		if (!post('category')) {
			throw new ValidationException(['category[]' => 'You must pick at least one category!']);
			return false; //['#blogfront-flash' => '<div class="alert alert-danger">You must pick at least one category!</div>'];
		}

		if (!$this->post = $this->loadPost()) {
			return null;
		}

		$this->post->user_id = $this->backUser;
		$this->post->author_id = $this->user->id;
		$this->post->title = post('title');
		if (!$this->post->slug) {
			$this->post->slug = $this->getSlug(post('title'));
		}
		$this->post->content = post('content');
		if ($this->allowpublish) {
			$this->post->published_at = post('published') ? (
				$this->post->published_at ?: date('Y-m-d H:i:s'))
			: $this->post->published_at;
			$this->post->published = post('published') ? true : false;
		} else {
			$this->post->published = $this->post->published ? true : false;
			$this->post->published_at = $this->post->published ? (
				$this->post->published_at ?: date('Y-m-d H:i:s'))
			: $this->post->published_at;
		}
		// Check for other fields that may need saving
		$this->updateNonStdFields();

		$id = $this->post->save();

		if ($this->post->categories()->exists()) {
			$this->post->categories()->
				detach(
				$this->categoryIds
			);
		}

		if (post('category')) {
			$this->post->categories()->
				attach(array_intersect(post('category'), $this->categoryIds));
		}

		if (post('ititle') && $this->allow_images) {
			$this->onImageText();
		}

		if (post('notify_groups')) {
			$groups = array_intersect(post('notify_groups'), array_flip($this->user_groups));
			$this->notifyGroups($groups, $this->post, $this->postPage);
		}
		return $id;
	}

	/**
	 * Allow custom fields to be saved from custom form partials
	 * This is used where someone has extended Post model with their own fields
	 * @return null
	 */
	protected function updateNonStdFields() {
		$non_std_fields = $this->property('non_std_fields');
		if ($non_std_fields) {
			foreach (explode(',', $non_std_fields) as $field) {
				if (post($field)) {
					$this->post->$field = post($field);
				}
			}
		}
	}

	/**
	 * Ajax handler to delete a featured_image
	 * triggers onRun to show list after delete
	 * @return null if post does not exist
	 */
	protected function onImageText() {
		if (!$this->post = $this->loadPost()) {
			return null;
		}
		$titles = post('ititle');
		$descriptions = post('idescription');

		foreach ($this->post->featured_images()->get() as $image) {

			$image->title = $titles[$image->id];
			$image->description = $descriptions[$image->id];
			$image->save();
		}
	}

	/**
	 * Ajax handler to delete a post
	 * triggers onRun to show list after delete
	 * @return null if post does not exist
	 */
	protected function onDelete() {
		if (!$this->post = $this->loadPost()) {
			return null;
		}
		$this->post->delete();
		$this->onRun();
	}

	protected function onUnpublish() {
		if (!$this->post = $this->loadPost()) {
			return null;
		}
		$this->post->update(['published' => 0]);
		$this->onRun();
	}

	protected function onPublish() {
		if (!$this->post = $this->loadPost()) {
			return null;
		}
		$this->post->update(['published' => 1]);
		$this->onRun();
	}

	protected function getSlug($title) {
		$slug = str_slug($title, "-");
		$slugCount = count(BlogPost::whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get());

		return ($slugCount > 0) ? "{$slug}-{$slugCount}" : $slug;
	}

	public function getCategoryItems() {
		return BlogCategory::orderBy('name')->lists('name', 'id');
	}

	public function getBackUserOptions() {
		foreach (User::get() as $user) {
			$users[$user->id] = $user->first_name . ' ' . $user->last_name;
		}
		return $users;
	}

	/**
	 * Short function for accessing Lang file strings
	 * @param  string $string string key for lang.php file
	 * @return string Translated string
	 */
	public function trans($string) {
		return Lang::get('fireunion.blogfront::lang.' . $string);
	}

	/**
	 * Create String to show all category names
	 * @param  object $string string key for lang.php file
	 * @return string Translated string
	 */
	public function categoryStr($post) {
		return implode("<br>\n", $post->categories->lists('name'));
	}

	/**
	 * Short function for accessing Lang file strings in blog plugin
	 * @param  string $string string key for lang.php file
	 * @return string Translated string
	 */
	public function blogtrans($string) {
		return Lang::get('rainlab.blog::lang.' . $string);
	}

	public function getPageOptions() {
		return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
	}

	public function getGroupOptions() {
		return UserGroup::orderBy('name')->lists('name', 'id');
	}
}
