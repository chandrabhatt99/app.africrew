<?php

namespace App\Events;

use App\Models\Proposal;
use App\Models\StaffingRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NegotiationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Proposal $proposal;
    public string $status;
    public ?float $agreedPrice;

    public function __construct(Proposal $proposal, string $status, ?float $agreedPrice = null)
    {
        $this->proposal = $proposal->load(['staffingRequest', 'professional']);
        $this->status = $status;
        $this->agreedPrice = $agreedPrice;
    }

    public function broadcastOn(): array
    {
        $req = $this->proposal->staffingRequest;
        $channels = [];
        if ($req && $req->user_id) {
            $channels[] = new PrivateChannel('chat.' . $req->user_id);
        }
        if ($this->proposal->professional && $this->proposal->professional->email) {
            $profUser = \App\Models\User::where('email', $this->proposal->professional->email)->first();
            if ($profUser) {
                $channels[] = new PrivateChannel('chat.' . $profUser->id);
            }
        }
        return $channels ?: [new Channel('negotiations')];
    }

    public function broadcastAs(): string
    {
        return 'negotiation.updated';
    }
}
