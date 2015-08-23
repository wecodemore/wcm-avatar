# WCM Avatar

WordPress plugin that adds an attachment uploader to a users profile
page in the admin UI.

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