# WCM Avatar

WordPress plugin that adds an attachment uploader to a users profile
page in the admin UI. Also enables adding a front end attachment uploader
to a themes template.

- [Changelog](#changelog)
- [Installation](#installation)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [Future Versions](#future-versions)


# Changelog

Please see the [Releases](https://github.com/wecodemore/wcm-avatar/releases) for a full list of changes.

# Installation

The preferred way to install is via [Composer](https://getcomposer.org).
Add the following line to your `composer.json`s `require` array:

```json
"wcm/avatar" : "dev-master"
```

If you prefer _stable_ releases, please use a Git tag instead of `dev-master`.
Versions are tagged using [semantic versions](http://semver.org/), meaning
that you can use `1.0.*` safely as this will bring all _patches_, but no
feature changes.

If you prefer to live with on the edge, use the `dev` branch and set
the version in your `composer.json` to `dev-dev`.

# Configuration

To **edit the meta key** that is used to save and retrieve the user ID, use
the following filter. The default is `user_avatar`.

```php
add_filter( 'wcm.avatar.meta_key', function( $key )
{
	return 'user_photo';
} );
```

To **enable the additional custom column** in the admin UI user list table,
use the following filter. Use this filter if you want to see both the users
Gravatar as well as the custom uploaded attachment that can be used instead.

```php
add_filter( 'wcm.avatar.enable_custom_column', '__return_true' );
```

To **disable custom avatars** use the following filter. The output of
the `get_avatar()` function will be overriden per default.

```php
add_filter( 'wcm.avatar.enable_custom_avatar', '__return_false' );
```

To **customize the output** of the `get_avatar()` function, use the
following filter. The filter has _two_ additional arguments on top
of the original arguments that can be retrieved via the
`get_avatar_data()` function.

```php
add_filter( 'wcm.avatar.args', [
	// Default get_avatar_data() args:
	'size'          => 96,
	'height'        => null,
	'width'         => null,
	'alt'           => '',
	'class'         => null,
	'extra_attr'    => '',
	// Additional arguments:
	'size_name'     => 'thumbnail',
	'size_name_2x'  => 'medium',
] );
```

The `class` argument gets appended to the following classes. Those are
the default for WordPress avatars in the admin UI user list table. The
`$size` argument is the `size` argument from above array and of the
type `int`.

 * `photo`
 * `avatar`
 * `avatar-{$size}`

To set a different allowed maximum file size/ dimension than the default
value of `1024`, use the following filter:

```php
add_filter( 'wcm.avatar.size.max', function( $max ) {
	return 2048;
} );
```

To set a different required minimum file size/ dimension than the default
value of `32`, use the following filter:

```php
add_filter( 'wcm.avatar.size.min', function( $min ) {
	return 128;
} );
```

The implementation in your theme should be as following:

```php
function upload_vatar( Array $user )
{
	foreach ( [ 'media', 'file', 'image' ] as $file ) {
		require_once ABSPATH."/wp-admin/includes/{$file}.php";
	}

	$att_id = media_handle_upload( 'user_avatar', $post_id = - 1 );
	if ( is_wp_error( $att_id ) ) {
		avatar_redirect( $att_id->get_error_code() );
	}

	$key = apply_filters( 'wcm.avatar.meta_key', 'user_avatar' );
	// Add attachment-post ID to user as meta single entry to allow querying for it
	$user_meta = update_user_meta( $user['ID'], $key, $att_id );
	if ( FALSE === $user_meta ) {
		avatar_redirect( 'avatar_upload_umeta' );
	}

	// Add user to attachment-post, again as single post meta entry to allow querying it
	$post_meta = add_post_meta( $att_id, 'user_id', $user['ID'], TRUE );
	if ( FALSE === $post_meta ) {
		avatar_redirect( 'avatar_upload_pmeta' );
	}
}

function avatar_redirect() 
{
	if ( headers_sent() ) {
		exit( $reason );
	}
	$key  = $reason === 'saved' ? 'success' : 'error';
	$link = get_permalink( get_queried_object() );
	$url  = $reason === 'login'
		? wp_login_url( $link )
		: add_query_arg( [ $key => $reason ], $link );
	wp_safe_redirect( $url );
	exit();
}
```
# Contributing

We happily accept Pull Requests and reward them with contributor access!

Before adding a new issue or filing a Pull Request, please take a look 
at the [existing issues](https://github.com/wecodemore/wcm-avatar/issues) and [open _Pull Requests](https://github.com/wecodemore/wcm-avatar/pulls).

Please always open a new issue and _fork_ the plugin. Before starting
to work on code changes, please make sure that you check out a new branch
starting with `issue-`:

```sh
git checkout -b issue-9
```

Add as many commits as possible for your changes. When you write a commit
message, please…

 * use `feat`, `refactor`, `style`, `fix` or `docs` for the start of your msg
 * set the _topic_ into braces
 * write a meaningful message (can be multiple lines long)
 * add a hash `#` and the issue number to the message, so GitHub can refer it

Example:

```sh
git commit -m "fix(loader) Fix icon not appearing, see #9"
```

When you push to your remote repo, please push the `issue-` branch to
remote when sending your _Pull Request_. This allows us to check your
branch locally and in isolation before merging it back into `dev` and
finally into `master` and [tagging it for a new release](https://github.com/wecodemore/wcm-avatar/tags).

Thanks a bunch for contributing!

# Future Versions

For a list of changes planned for the future, please look at the [milestones](https://github.com/wecodemore/wcm-avatar/milestones) assigned to issues.
