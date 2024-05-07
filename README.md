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
    <li>Run  <code>php artisan migrate</code></li>
    <li>Run  <code>php artisan db:seed</code></li>
</ol>
