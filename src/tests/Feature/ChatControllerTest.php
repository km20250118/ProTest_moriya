<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $seller;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // テストユーザーとアイテムを作成
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
    public function チャット画面が正常に表示される()
    {
        $response = $this->actingAs($this->buyer)
            ->get("/chat/{$this->item->id}");

        $response->assertStatus(200);
        $response->assertViewIs('chat.show');
        $response->assertViewHas('item');
        $response->assertViewHas('messages');
    }

    /** @test */
    public function メッセージを送信できる()
    {
        $response = $this->actingAs($this->buyer)
            ->post("/chat/{$this->item->id}", [
                'body' => 'テストメッセージ',
            ]);

        $response->assertRedirect("/chat/{$this->item->id}");
        $this->assertDatabaseHas('chat_messages', [
            'item_id' => $this->item->id,
            'user_id' => $this->buyer->id,
            'body' => 'テストメッセージ',
        ]);
    }

    /** @test */
    public function 自分のメッセージを編集できる()
    {
        $message = ChatMessage::create([
            'item_id' => $this->item->id,
            'user_id' => $this->buyer->id,
            'body' => '編集前',
            'read_by_buyer' => true,
            'read_by_seller' => false,
        ]);

        $response = $this->actingAs($this->buyer)
            ->put("/chat/{$this->item->id}/message/{$message->id}", [
                'body' => '編集後',
            ]);

        $response->assertRedirect("/chat/{$this->item->id}");
        $this->assertDatabaseHas('chat_messages', [
            'id' => $message->id,
            'body' => '編集後',
        ]);
    }

    /** @test */
    public function 自分のメッセージを削除できる()
    {
        $message = ChatMessage::create([
            'item_id' => $this->item->id,
            'user_id' => $this->buyer->id,
            'body' => 'テスト',
            'read_by_buyer' => true,
            'read_by_seller' => false,
        ]);

        $response = $this->actingAs($this->buyer)
            ->delete("/chat/{$this->item->id}/message/{$message->id}");

        $response->assertRedirect("/chat/{$this->item->id}");
        $this->assertSoftDeleted('chat_messages', [
            'id' => $message->id,
        ]);
    }
}
