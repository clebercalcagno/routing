#### apache

```apacheconfig
# turn on mode rewrite
RewriteEngine On

# router url Rewrite
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^([^.]+)$ index.php [NC,L]
```
