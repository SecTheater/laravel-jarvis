<?php

return [
    'activation' => [
        // Does your application contains of actiavtion process?
        'register' => true,
        // Duration For Expiring the activation token is one day. ( Duration Estmiated in days )
        'expiration' => 1,
    ],
    'posts' => [
        // Does your application contains of posts?
        'register' => true,
        // allow approval for the posts.
        'approve' => true,
    ],
    'comments' => [
        // Does your application contains of comments?

        'register' => true,
        // allow approval for the comments.
        'approve' => true,
    ],
    'replies' => [
        // Does your application contains of replies?

        'register' => true,
        // allow approval for the replies.
        'approve' => true,
    ],
    'reminder' => [
        // Duration For Expiring the reminder token is one day. ( Duration Estmiated in days )
        'expiration' => 1,
    ],
    'tags' => [
        // Does your application contains of tags?

        'register' => true,
    ],
    'likes' => [
        'register' => true,
    ],
    /*
             * here you may specify the session name
    */
    'session_name' => 'sectheater_session',
    /*
             * here you may specify the cookie name
    */
    'cookie_name' => 'sectheater_cookie',
    /*
             * Here you can specify jarvis controller settings
    */
    'controllers' => [
        // the default jarvis controllers namespace, feel free to change it.
        'namespace' => 'SecTheater\\Jarvis\\Http\\Controllers',
    ],
    /*
             * Here is the models that Jarvis uses, Feel free to change them and don't hesitate to change, The package mainly depends on the repoistories, traits and Observers . Everything is separated from the model except for relationships, so you can change the model and keep using the same functionailities but you have to setup the  relationships within your model.
    */
    'models' => [
        'namespace' => '\\App\\',
        // user models
        'user' => [
            // 'user' => 'User' or reference whatever here.
        ],
        'package' => [
            'user'       => 'SecTheater\Jarvis\User\EloquentUser',
            'like'       => 'SecTheater\Jarvis\Like\EloquentLike',
            'post'       => 'SecTheater\Jarvis\Post\EloquentPost',
            'tag'        => 'SecTheater\Jarvis\Tag\EloquentTag',
            'comment'    => 'SecTheater\Jarvis\Comment\EloquentComment',
            'reply'      => 'SecTheater\Jarvis\Reply\EloquentReply',
            'role'       => 'SecTheater\Jarvis\Role\EloquentRole',
            'activation' => 'SecTheater\Jarvis\Activation\EloquentActivation',
            'reminder'   => 'SecTheater\Jarvis\Reminder\EloquentReminder',
        ],
    ],
    /*
            Observers are likely similar to Accessors and Mutators. If you ever want to turn them off just set the option to false.
            They just control the attributes incoming to the database and the outcoming ones, if any of the observers having a conflict with you set the option to false.
    */
    'observers' => [
        'register' => true,
    ],
    'repositories' => [
        'namespace' => '\App\Repositories',
        'user'      => [
            'user',
        ],
        // 'package' => [
        // 	'ActivationRepository' => '\SecTheater\Jarvis\ActivationRepository\ActivationRepository',
        // 	'CommentRepository' => '\SecTheater\Jarvis\Comment\CommentRepository',
        // 	'LikeRepository' => '\SecTheater\Jarvis\Like\LikeRepository',
        // 	'PostRepository' => '\SecTheater\Jarvis\Post\PostRepository',
        // 	'ReminderRepository' => '\SecTheater\Jarvis\Reminder\ReminderRepository',
        // 	'ReplyRepository' => '\SecTheater\Jarvis\Reply\ReplyRepository',
        // 	'RoleRepository' => '\SecTheater\Jarvis\Role\RoleRepository',
        // 	'TagRepository' => '\SecTheater\Jarvis\Tag\TagRepository',
        // 	'UserRepository' => '\SecTheater\Jarvis\User\UserRepository',

        // ]
    ],

];
