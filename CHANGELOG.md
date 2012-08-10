## CHANGELOG

### Version 0.2.2 (open source)
- Short PHP tags `<?` replaced with full tags `<?php` in the entire application for maximum compatibility with different `php.ini` configurations.

### Version 0.2.1 (open source)
- Fixed a bug in `install.php` which generate a `CRYPTO_SEED` of 62 characters instead of 64. Reduced required length in `app.config.php` to 40 characters.

### Version 0.2.0 (open source)
- Removed the requirement of setting up re-write rules and created a new class `Links`. All links work either with pretty links enabled or disabled. To use pretty links, re-write rules must still be configured on the web-server. See step *#13* in the installation instructions for further details.

### Version 0.1.0 (open source)
- Initial release