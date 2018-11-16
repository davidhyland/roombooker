Plugin Update Checker
=====================

This is a custom update checker library for WordPress plugins and themes. It lets you add automatic update notifications and one-click upgrades to your commercial plugins, private themes, and so on. All you need to do is put your plugin/theme details in a JSON file, place the file on your server, and pass the URL to the library. The library periodically checks the URL to see if there's a new version available and displays an update notification to the user if necessary.

From the users' perspective, it works just like with plugins and themes hosted on WordPress.org. The update checker uses the default upgrade UI that is familiar to most WordPress users.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Getting Started](#getting-started)
  - [Self-hosted Plugins and Themes](#self-hosted-plugins-and-themes)
    - [How to Release an Update](#how-to-release-an-update)
    - [Notes](#notes)
  - [GitHub Integration](#github-integration)
    - [How to Release an Update](#how-to-release-an-update-1)
    - [Notes](#notes-1)
  - [BitBucket Integration](#bitbucket-integration)
    - [How to Release an Update](#how-to-release-an-update-2)
  - [GitLab Integration](#gitlab-integration)
    - [How to Release an Update](#how-to-release-an-update-3)
- [Resources](#resources)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

Getting Started
---------------


### GitHub Integration

1. Download [the latest release](https://github.com/YahnisElsts/plugin-update-checker/releases/latest) and copy the `plugin-update-checker` directory to your plugin or theme.
2. Add the following code to the main plugin file or `functions.php`:

	```php
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/user-name/repo-name/',
		__FILE__,
		'unique-plugin-or-theme-slug'
	);

	//Optional: If you're using a private repository, specify the access token like this:
	$myUpdateChecker->setAuthentication('your-token-here');

	//Optional: Set the branch that contains the stable release.
	$myUpdateChecker->setBranch('stable-branch-name');
	```
3. Plugins only: Add a `readme.txt` file formatted according to the [WordPress.org plugin readme standard](https://wordpress.org/plugins/about/readme.txt) to your repository. The contents of this file will be shown when the user clicks the "View version 1.2.3 details" link.

#### How to Release an Update

This library supports a couple of different ways to release updates on GitHub. Pick the one that best fits your workflow.

- **GitHub releases** 
	
	Create a new release using the "Releases" feature on GitHub. The tag name and release title don't matter. The description is optional, but if you do provide one, it will be displayed when the user clicks the "View version x.y.z details" link on the "Plugins" page. Note that PUC ignores releases marked as "This is a pre-release".
	
- **Tags** 
	
	To release version 1.2.3, create a new Git tag named `v1.2.3` or `1.2.3`. That's it.
	
	PUC doesn't require strict adherence to [SemVer](http://semver.org/). These are all valid tag names: `v1.2.3`, `v1.2-foo`, `1.2.3_rc1-ABC`, `1.2.3.4.5`. However, be warned that it's not smart enough to filter out alpha/beta/RC versions. If that's a problem, you might want to use GitHub releases or branches instead.
	
- **Stable branch** 
	
	Point the update checker at a stable, production-ready branch: 
	 ```php
	 $updateChecker->setBranch('branch-name');
	 ```
	 PUC will periodically check the `Version` header in the main plugin file or `style.css` and display a notification if it's greater than the installed version.
	 
	 Caveat: If you set the branch to `master` (the default), the update checker will look for recent releases and tags first. It'll only use the `master` branch if it doesn't find anything else suitable.

#### Notes

The library will pull update details from the following parts of a release/tag/branch:

- Version number
	- The "Version" plugin header.
	- The latest GitHub release or tag name.
- Changelog
	- The "Changelog" section of `readme.txt`.
	- One of the following files:
		CHANGES.md, CHANGELOG.md, changes.md, changelog.md
	- GitHub release notes.
- Required and tested WordPress versions
	- The "Requires at least" and "Tested up to" fields in `readme.txt`.
	- The following plugin headers:
		`Required WP`, `Tested WP`, `Requires at least`, `Tested up to`
- "Last updated" timestamp
	- The creation timestamp of the latest GitHub release.
	- The latest commit in the selected tag or branch.
- Number of downloads
	- The `download_count` statistic of the latest release.
	- If you're not using GitHub releases, there will be no download stats.
- Other plugin details - author, homepage URL, description
	- The "Description" section of `readme.txt`.
	- Remote plugin headers (i.e. the latest version on GitHub).
	- Local plugin headers (i.e. the currently installed version).
- Ratings, banners, screenshots
	- Not supported.
	

Resources
---------

- [This blog post](http://w-shadow.com/blog/2010/09/02/automatic-updates-for-any-plugin/) has more information about the update checker API. *Slightly out of date.*
- [Debug Bar](https://wordpress.org/plugins/debug-bar/) - useful for testing and debugging the update checker.
- [Securing download links](http://w-shadow.com/blog/2013/03/19/plugin-updates-securing-download-links/) - a general overview.
- [A GUI for entering download credentials](http://open-tools.net/documentation/tutorial-automatic-updates.html#wordpress)
- [Theme Update Checker](http://w-shadow.com/blog/2011/06/02/automatic-updates-for-commercial-themes/) - an older, theme-only variant of this update checker.
