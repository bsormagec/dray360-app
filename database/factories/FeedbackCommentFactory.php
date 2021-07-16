<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Order;
use Faker\Generator as Faker;
use App\Models\FeedbackComment;

$factory->define(FeedbackComment::class, function (Faker $faker) {
    return [
        'commentable_type' => Order::class,
        'commentable_id' => factory(Order::class),
        'comment' => $faker->sentence,
        'user_id' => factory(User::class),
    ];
});
