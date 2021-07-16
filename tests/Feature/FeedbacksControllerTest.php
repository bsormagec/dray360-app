<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use Illuminate\Http\Response;
use App\Models\FeedbackComment;
use Tests\Seeds\OrdersTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeedbacksControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(OrdersTableSeeder::class);
        $this->loginCustomerAdmin();
    }

    /** @test */
    public function it_should_list_all_the_comments()
    {
        $this->withoutExceptionHandling();
        $this->loginAdmin();
        factory(FeedbackComment::class, 10)->create();

        $this->getJson(route('feedbacks.index'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_should_allow_to_create_feedbacks_for_orders()
    {
        $order = Order::first();
        $comment = $this->faker->sentence;

        $this->postJson(route('feedbacks.store'), [
                'comment' => $comment,
                'commentable_type' => 'order',
                'commentable_id' => $order->id,
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'comment',
                'user_id',
                'commentable_id',
                'commentable_type',
            ]);

        $this->assertDatabaseHas('t_feedback_comments', [
            'commentable_type' => Order::class,
            'commentable_id' => $order->id,
            'comment' => $comment,
            'user_id' => auth()->id(),
        ]);
    }
}
