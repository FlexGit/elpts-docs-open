<?php

// Main
Breadcrumbs::for('main.index', function ($trail) {
    $trail->push('Главная', route('main.index'));
});

// Countries
Breadcrumbs::for('countries.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Страны', route('countries.index'));
});

Breadcrumbs::for('countries.create', function ($trail) {
    $trail->parent('countries.index');
    $trail->push('Новая запись', route('countries.create'));
});

Breadcrumbs::for('countries.edit', function ($trail, $country) {
    $trail->parent('countries.index');
    $trail->push($country->name, route('countries.edit', $country->id));
});

// Prefixes
Breadcrumbs::for('prefixes.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Префиксы', route('prefixes.index'));
});

Breadcrumbs::for('prefixes.create', function ($trail) {
    $trail->parent('prefixes.index');
    $trail->push('Новая запись', route('prefixes.create'));
});

Breadcrumbs::for('prefixes.edit', function ($trail, $prefix) {
    $trail->parent('prefixes.index');
    $trail->push($prefix->name, route('prefixes.edit', $prefix->id));
});

// Okopfs
Breadcrumbs::for('okopfs.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('ОКОПФ', route('okopfs.index'));
});

Breadcrumbs::for('okopfs.create', function ($trail) {
    $trail->parent('okopfs.index');
    $trail->push('Новая запись', route('okopfs.create'));
});

Breadcrumbs::for('okopfs.edit', function ($trail, $okopf) {
    $trail->parent('okopfs.index');
    $trail->push($okopf->name, route('okopfs.edit', $okopf->id));
});

// Users
Breadcrumbs::for('users.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Операторы', route('users.index'));
});

Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users.index');
    $trail->push('Новая запись', route('users.create'));
});

Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users.index');
    $trail->push($user->name, route('users.edit', $user->id));
});

// Templates
Breadcrumbs::for('templates.index', function ($trail, $doctype) {    $trail->parent('main.index');
    $trail->push('Шаблоны документа "'.$doctype->name.'"', route('templates.index', $doctype));
});

Breadcrumbs::for('templates.edit', function ($trail, $doctype, $template) {
    $trail->parent('templates.index', $doctype);
    $trail->push($template->name, route('templates.edit', [$doctype, $template]));
});

Breadcrumbs::for('templates.create', function ($trail, $doctype) {
    $trail->parent('templates.index', $doctype);
    $trail->push('Новый шаблон', route('templates.create', $doctype));
});

// Log
Breadcrumbs::for('log.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Лог операций', route('log.index'));
});

// Settings
Breadcrumbs::for('settings.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Настройки', route('settings.index'));
});

Breadcrumbs::for('settings.edit', function ($trail, $setting) {
    $trail->parent('settings.index');
    $trail->push($setting->descr, route('settings.edit', $setting));
});

// Docs
Breadcrumbs::for('docs.index', function ($trail, $doctype) {
    $trail->parent('main.index');
    $trail->push('Реестр документов "'.$doctype->name.'"', route('docs.index', $doctype));
});

Breadcrumbs::for('docs.show', function ($trail, $doctype, $docs) {
    $trail->parent('docs.index', $doctype);
    $trail->push('Документ "'.$docs['prefix_number'].'"', route('docs.show', $docs));
});


/*// Home > Blog
Breadcrumbs::register('blog', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Blog', route('blog'));
});

// Home > Blog > [Category]
Breadcrumbs::register('category', function ($breadcrumbs, $category) {
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($category->title, route('category', $category->id));
});

// Home > Blog > [Category] > [Post]
Breadcrumbs::register('post', function ($breadcrumbs, $post) {
    $breadcrumbs->parent('category', $post->category);
    $breadcrumbs->push($post->title, route('post', $post));
});*/