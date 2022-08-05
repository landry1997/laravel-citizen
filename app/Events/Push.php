<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Push implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $message;
  public $user;

  public function __construct($user, $message)
  {
      $this->message = $message;
      $this->user = $user;
  }

  public function broadcastOn()
  {
      return ['my-channel-push'];
  }

  public function broadcastAs()
  {
      return 'my-event-push';
  }
}
