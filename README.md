Run
`php artisan vendor:publish --provider="Boonei\Scaffold\ScaffoldServiceProvider" --tag="assets"`

In app.js after loading Vue, before initialising Vue app:
`require('../boonei/scaffold/js/app');`
