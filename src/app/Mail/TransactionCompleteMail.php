<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Item;
use App\Models\User;

class TransactionCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $buyer;
    public $seller;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Item  $item
     * @param  \App\Models\User  $buyer
     * @param  \App\Models\User  $seller
     * @return void
     */
    public function __construct(Item $item, User $buyer, User $seller)
    {
        $this->item = $item;
        $this->buyer = $buyer;
        $this->seller = $seller;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【COACHTECH】取引完了通知 - ' . $this->item->name)
            ->view('mail.transaction_complete');
    }
}
