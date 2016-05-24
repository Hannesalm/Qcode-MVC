<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'nav navbar-nav',

    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'home' => [
            'text'  =>'Home',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Home',
        ],
        'questions' => [
            'text'  =>'Questions',
            'url'   => $this->di->get('url')->create('questions'),
            'title' => 'questions',
        ],

        'users' => [
            'text'  =>'Users',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'Users',
        ],

        'tags' => [
            'text'  =>'Tags',
            'url'   => $this->di->get('url')->create('tags'),
            'title' => 'Tags',
        ],
        'about' => [
            'text'  =>'About us',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'About',
        ],

    ],



    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



    /**
     * Callback to create the url, if needed, else comment out.
     *
     */
    /*
     'create_url' => function ($url) {
         return $this->di->get('url')->create($url);
     },
     */
];
