<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TopBar extends Component
{

    public $notifications = [];


    public function mount()
{
    $user = auth()->user();
    if ($user) {
        $this->notifications = $user->unreadNotifications;
    } else {
        $this->notifications = collect(); // or []
    }
}


    public function render()
    {
        return view('livewire.top-bar');
    }

    public function markAsRead(?string $id = null)
    {
        //the when function check if id passed or not if not it will make all the notification as read
        // if id passed it only make that notificarion as read
        auth()->user()
        ->unreadNotifications
        ->when($id, function($query) use ($id){
            return $query->where('id',$id);
        })
        ->markAsRead();


    }

}
