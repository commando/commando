## CHANGELOG

### Version 0.2.5 (open source) - *08/10/2012*	
- Short PHP echo tags `<?=` replaced with full definitions `<?php echo` in the entire application for maximum compatibility with different `php.ini` configurations.

### Version 0.2.4 (open source) - *08/09/2012*
- Fixed bugs in `/classes/Links.php` dealing with auto-detecting pretty links. The code is quite nasty, if you know of a more elegant solution please submit a pull request.

### Version 0.2.3 (open source) - *08/09/2012*
- Fixed bug in `/classes/Links.php` where if using pretty links, any request inside of the `/actions` directory would cause pretty links to disable.

### Version 0.2.2 (open source) - *08/09/2012*
- Short PHP tags `<?` replaced with full tags `<?php` in the entire application for maximum compatibility with different `php.ini` configurations.

### Version 0.2.1 (open source) - *08/09/2012*
- Fixed bug in `install.php` which generated a `CRYPTO_SEED` of 62 characters instead of 64. Reduced required length in `app.config.php` of `CRYPTO_SEED` to 40 characters.

### Version 0.2.0 (open source) - *08/09/2012*
- Removed the requirement of setting up re-write rules and created a new class `/classes/Links`. All links work either with pretty links enabled or disabled. To use pretty links, re-write rules must still be configured on the web-server. See step *#13* in the installation instructions for further details.

### Version 0.1.0 (open source) - *08/07/2012*
- Initial release