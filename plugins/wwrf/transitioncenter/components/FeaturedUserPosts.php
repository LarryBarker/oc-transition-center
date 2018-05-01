<?php namespace Wwrf\TransitionCenter\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Lang;
use RainLab\Blog\Models\Post as BlogPost;
use Redirect;

class FeaturedUserPosts extends ComponentBase
{
    public $posts;

    public $noPostsMessage;

    public $postPage;

    public $sortOrder;

    public static $allowedSorting = [
        'title asc',
        'title desc',
        'created_at asc',
        'created_at desc',
        'updated_at asc',
        'updated_at desc',
        'published_at asc',
        'published_at desc'
    ];

    public function componentDetails()
    {
        return [
            'name'        => 'Featured User Posts',
            'description' => 'List featured user posts on frontend.'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'indikator.news::lang.settings.pagination_title',
                'description' => 'indikator.news::lang.settings.pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}'
            ],
            'postsPerPage' => [
                'title'             => 'indikator.news::lang.settings.per_page_title',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'indikator.news::lang.settings.per_page_validation',
                'default'           => '1'
            ],
            'noPostsMessage' => [
                'title'             => 'indikator.news::lang.settings.no_posts_title',
                'description'       => 'indikator.news::lang.settings.no_posts_description',
                'type'              => 'string',
                'default'           => Lang::get('indikator.news::lang.settings.no_posts_found'),
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'indikator.news::lang.settings.posts_order_title',
                'description' => 'indikator.news::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
                'options'     => [
                    'title asc'         => Lang::get('indikator.news::lang.sorting.title_asc'),
                    'title desc'        => Lang::get('indikator.news::lang.sorting.title_desc'),
                    'created_at asc'    => Lang::get('indikator.news::lang.sorting.created_at_asc'),
                    'created_at desc  ' => Lang::get('indikator.news::lang.sorting.created_at_desc'),
                    'updated_at asc'    => Lang::get('indikator.news::lang.sorting.updated_at_asc'),
                    'updated_at desc'   => Lang::get('indikator.news::lang.sorting.updated_at_desc'),
                    'published_at asc'  => Lang::get('indikator.news::lang.sorting.published_at_asc'),
                    'published_at desc' => Lang::get('indikator.news::lang.sorting.published_at_desc')
                ]
            ],
            'postPage' => [
                'title'       => 'indikator.news::lang.settings.post_title',
                'description' => 'indikator.news::lang.settings.post_description',
                'type' => 'dropdown',
                'default'     => $this->getPostPageOptions()
            ]
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->page['postPage'] = $this->property('postPage');
        $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        $this->posts = $this->page['posts'] = $this->listPosts();

        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1) {
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
            }
        }
    }

    protected function listPosts()
    {
        /*
         * List all the posts, eager load their categories
         */
        $posts = BlogPost::with('categories')->where('is_featured', true)->listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('postsPerPage'),
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });


        return $posts;
    }

}
