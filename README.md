# Task Management

<h3>How to seup project ? </h3>
<ol>
    <li>Clone Repo</li>
    <li>Set database credentails in .env as   
        <br>
<code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=</code>
    </li>
    <li>Run  <code>composer install</code>, if got any error, delete the composer.lock file and rerun <code>composer install</code></li>
    <li>Run  <code>php artisan migrate</code></li>
    <li>Run  <code>php artisan db:seed</code></li>
    <li>Login with login api, using any email in users table and use password as 'password', copy the token from response and paste it in Authorization > Bearer Token.</li>
    <li>Now you can use any api using that bearer token</li>
    <li>Now create, update, delete, assign, unassign, list tasks and many more...</li>
    <li>To invalidate the token, use logout api using that token.</li>
</ol>
