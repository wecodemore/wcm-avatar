# WCM Avatar

WordPress plugin that adds an attachment uploader to a users profile
page in the admin UI.

## How To: Install

The preferred way to install is via [Composer]().
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

## How To: Configuration

To **enable the additional custom column** in the admin UI user list table,
use the following filter. Use this filter if you want to see both the users
Gravatar as well as the custom uploaded attachment that can be used instead.

```php
add_filter( 'wcm.avatar.enable_custom_column', '__return_true' );
```

To **disable custom avatars** use the following filter. The output of
the `get_avatar()` function will be overriden per default.

```php
add_filter( 'wcm.avatar.enable_custom_avatar', '__return_true' );
```

To **customize the output** of the `get_avatar()` function, use the
following filter. The filter has _two_ additional arguments on top
of the original arguments that can be retrieved via the
`get_avatar_data()` function.

```php
add_filter( 'wcm.avatar.size', [
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
add_filter( 'wcm.avatar.size_max', function( $limit ) {
	return 2048;
} );

## How To: Contribute

We happily accept Pull Requests and reward them with contributor access!

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
finally into `master` and tagging it for a new release.

Thanks a bunch for contributing!