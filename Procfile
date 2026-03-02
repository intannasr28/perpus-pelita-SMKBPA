web: vendor/bin/heroku-php-apache2 -i /dev/stdin <<EOF
<Directory "${DOCUMENT_ROOT}">
    Options -MultiViews +FollowSymLinks
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
    AllowOverride All
    Allow from all
</Directory>
EOF
