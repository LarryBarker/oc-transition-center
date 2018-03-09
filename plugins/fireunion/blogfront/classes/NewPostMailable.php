<?php
namespace FireUnion\BlogFront\Classes;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use RainLab\Blog\Models\Post as BlogPost;

class NewPostMailable extends Mailable {

	use Queueable, SerializesModels;

	/**
	 * The user instance.
	 *
	 * @var user
	 */
	public $user;
	/**
	 * The post instance.
	 *
	 * @var post
	 */
	public $post;
	/**
	 * The post URL
	 *
	 * @var postUrl
	 */
	public $postUrl;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(BlogPost $post, string $postUrl, array $user) {
		$this->post = $post;
		$this->user = $user;
		$this->postUrl = $postUrl;

	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->view('fireunion.blogfront::mail.new_post');
	}
}