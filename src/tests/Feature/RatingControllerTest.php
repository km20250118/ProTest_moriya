<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompleteMail;

class RatingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $seller;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->buyer = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $this->item = Item::factory()->create([
            'user_id' => $this->seller->id,
            'transaction_status' => 'in_transaction',
        ]);

        SoldItem::create([
            'item_id' => $this->item->id,
            'user_id' => $this->buyer->id,
            'sending_postcode' => $this->buyer->postal_code,
            'sending_address' => $this->buyer->address,
            'sending_building' => '',
        ]);
    }

    /** @test */
    public function 購入者が評価を投稿できる()
    {
        Mail::fake();

        $response = $this->actingAs($this->buyer)
            ->post("/rating/{$this->item->id}/buyer", [
                'rating' => 5,
            ]);

        $response->assertRedirect(route('items.list'));

        $this->assertDatabaseHas('ratings', [
            'item_id' => $this->item->id,
            'from_user_id' => $this->buyer->id,
            'to_user_id' => $this->seller->id,
            'rating' => 5,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'transaction_status' => 'buyer_completed',
        ]);

        Mail::assertSent(TransactionCompleteMail::class);
    }

    /** @test */
    public function 出品者が評価を投稿できる()
    {
        // 購入者が先に評価を完了
        $this->item->update(['transaction_status' => 'buyer_completed']);

        Rating::create([
            'item_id' => $this->item->id,
            'from_user_id' => $this->buyer->id,
            'to_user_id' => $this->seller->id,
            'rating' => 5,
        ]);

        $response = $this->actingAs($this->seller)
            ->post("/rating/{$this->item->id}/seller", [
                'rating' => 4,
            ]);

        $response->assertRedirect(route('items.list'));

        $this->assertDatabaseHas('ratings', [
            'item_id' => $this->item->id,
            'from_user_id' => $this->seller->id,
            'to_user_id' => $this->buyer->id,
            'rating' => 4,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'transaction_status' => 'completed',
        ]);
    }

    /** @test */
    public function ユーザーの平均評価が正しく計算される()
    {
        // 複数の評価を作成
        $item2 = Item::factory()->create(['user_id' => $this->seller->id]);
        $buyer2 = User::factory()->create(['email_verified_at' => now()]);

        Rating::create([
            'item_id' => $this->item->id,
            'from_user_id' => $this->buyer->id,
            'to_user_id' => $this->seller->id,
            'rating' => 5,
        ]);

        Rating::create([
            'item_id' => $item2->id,
            'from_user_id' => $buyer2->id,
            'to_user_id' => $this->seller->id,
            'rating' => 3,
        ]);

        $average = $this->seller->getRatingAverage();

        $this->assertEquals(4, $average); // (5 + 3) / 2 = 4
    }
}
