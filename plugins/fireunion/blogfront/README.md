Adds rich editing features to [Rainlab Blog plugin](https://octobercms.com/plugin/rainlab-blog).

Blog Front allows you to authorize front-end users to manage posts in RainLab Blog.  You decide who can posts to which categories and set the forms to allow only drafting or drafting and publishing.  You can allow administrator to approve posts before publishing.

Do you have users that need to create content for your club or group site?  
This is your solution!

### Features
- **Author settings per user**
    - Restrict what categories user can post to
    - Allow publishing or only draft post creation ( backend user will then need to publish post )
    - Set back-end user that user is authoring as.
    - Option to restrict author editing to only posts they create.
    - Option NOT to use "Author Settings" allows you to customize and protect forms as you see fit

- **Email post notifications**
    - Send to one or multiple user groups
    - Included  a link back to post page
    - Sends both HTML and plain text email
    - HTML email will contain post content
    - Set groups that can receive notices in component settings 

Use the provided Author settings to restrict access to the forms or use with [Passage Permissions](https://octobercms.com/plugin/kurtjensen-passage) and check for assigned permissions or just user IDs using twig. ( See Install directions  and example )

My labor union uses Rainlab Blog as a way to show "News" posts on our website. Blog Front gave me a way to delegate the task of creating content without having to train users in HTML. Administrators used to have to take time to copy content from emails and post information for members.  Now I just setup users to write there own posts. They can even paste in pictures from the Web and also paste formated text right from their word processing software. 

After content is created there is even away to send out new post notifications to registeredo users.

The components can be placed on the page and categories assigned to them as one of the property settings.  This has turned out to be the perfect solution for my site.




### Installation

( Installation code : __fireunion.blogfront__ ) Requires ( __RainLab.Blog__) and (__KurtJensen.Passage__ )

This Plugin requires these additional plugins to function:
- [RainLab.Blog](http://octobercms.com/plugin/rainlab-blog)
- [KurtJensen.Passage](http://octobercms.com/plugin/kurtjensen-passage)

### Basic Steps

**Install**
- Download plugin to plugin directory.

**Create Delegated Authors**

In blog section of back-end, add front-end users that you want to delegate to write posts.

**Create Page for Editing Posts**
- Create a "__List__" page and add Blog List Component to the page. 
    - Categories Setting ( Controlled by Author settings or Manually )
        - Preset Authors
            - Set "__Use Preset Authors__" to yes to use back-end author settings.
        - Manually
            - Set "__Categories__" you want to allow posting to.
    - Set "__Restrict Posts to Author__" if you only want users to edit the posts they created answer yes here.
    - Set "__Form Page__" ( After you create it below ).

- Create a "__Form__" page and add Blog Form Component to the page. 
    - Categories,  Backend Author, Allow Publish Settings ( Controlled by Author settings or Manually )
        - Preset Authors
            - Set "__Use Preset Authors__" to yes to use back-end author settings.
        - Manually
            - Set "__Categories__" you want to allow posting to.
            - Set "__Backend Author__" that this post is owned by.
    		- Set "__Restrict Posts to Author__" if you only want users to edit the posts they created answer yes here.
            - Set "__Allow Publish__" to yes if you want user to be able to change post "Published" status.

    - Set "__List Page__" ( from above ).
    - Add "__:slug?__" to the URL for the page .
        - Example URL "__/postform/:slug?__" or "__/blog/form/:slug?__" .

Alternatively you could use the Blog AJAX Form Component on a single page and set it's properties as listed above.

**You should protect these editing pages**
You can do this in a number of different ways but here is one example that allows only user with an id of 5 or 10 to see the form on the form page:

    <div class="contain">
    {% if user.id == 5 or user.id == 10 %}
    {% component 'PostForm' %}
    {% endif %}
    </div>



### You may want to display author ( frontend user ) with post.
This can be done by creating a partial for the post component. The most important thing about replacing default partials is the file name.

If you just drag and dropped the Rainlab.Post component to the page used to display a single post, then your page will look something like this:

- TITLE: Post
- URL: /blog/post/:slug
- File Name: blog/post.htm
- Layout: default
- Markup: 

~~~
    {% component 'blogPost' %}
~~~

Now to create a custom partial for this your partial should look like this:

- FILE NAME: blogPost/default.htm  ( **Notice the filename starts with 'blogPost' which matches the componet "Alias" used in the page markup.**)
- Markup: 

~~~
	 {% set post = __SELF__.post %}

	<div class="content">{{ post.content_html|raw }}</div>

	{% if post.featured_images.count %}
	    <div class="featured-images text-center">
	        {% for image in post.featured_images %}
	            <p>
	                <img
	                    data-src="{{ image.filename }}"
	                    src="{{ image.path }}"
	                    alt="{{ image.description }}"
	                    style="max-width: 100%" />
	            </p>
	        {% endfor %}
	    </div>
	{% endif %}

	<p class="info">
	    Posted
	    {% if post.categories.count %} in
	        {% for category in post.categories %}
	            <a href="{{ category.url }}">{{ category.name }}</a>{% if not loop.last %}, {% endif %}
	        {% endfor %}
	    {% endif %}
	    on {{ post.published_at|date('M d, Y') }} by 
	    {{ post.author.name }} {{ post.author.surname }} as {{ post.user.full_name }}
	</p>
~~~

Notice that there is a new part added to RainLab.Blog original partial:

~~~
	 by 
	    {{ post.author.name }} {{ post.author.surname }} as {{ post.user.full_name }}
~~~

This is an example of what could be changed to display author ( frontend user ) and user ( backend user ) in the posts display.


### Like this plugin?
If you like this plugin or if you use some of my plugins, you can help me by submiting a review in the market. Small donations also help keep me motivated. 

Please do not hesitate to find me in the IRC channel or contact me for assistance.
Sincerely 
Kurt Jensen