<?php

namespace App\Livewire;

use \App\Models\{
    Device as DeviceModel,
    Proces as ProcesModel,
    Ticket as TicketModel,
    Notification as NotificationModel,
    User as UserModel
};

use Carbon\Carbon;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Main extends Component
{
    public $isVisible = true;
    public $message;
    public $userMessage;
    public $proces_id;

    public function showAllData()
    {
        $this->isVisible = false;
    }

    public function getProces(int $id): void
    {
        $this->proces_id = $id;
    }

    public function hideAllData()
    {
        $this->isVisible = true;
    }

    public function notification(int $id): void
    {
        $data = NotificationModel::find($id);
        $data->update([
            'is_read' => 1
        ]);

        $this->message = $data;
    }

    public function sendMessage()
    {
        NotificationModel::create([
            'user_id' => Auth::user()->id,
            'message' => $this->userMessage,
            'is_user' => true
        ]);
    }

    public function render()
    {
        $devices = DeviceModel::where('user_id', Auth::user()->id);
        $tickets = TicketModel::whereIn('device_id', $devices->pluck('id'))->orderBy('created_at', 'asc')->get();
        $process =  ProcesModel::whereIn('ticket_id', $tickets->pluck('id'))->orderBy('created_at', 'asc')->get();
        $notifications = NotificationModel::where('user_id', Auth::user()->id)->orderBy('created_at', 'asc')->get();
        $timelines = ProcesModel::find($this->proces_id);
        $users = UserModel::all();

        return view('livewire.main', [
            'tickets' => $tickets,
            'devices' => $devices,
            'process' => $process,
            'users' => $users,
            'notifications' => $notifications,
            'userJson' => json_encode($users),
            'procesJson' => json_encode($process),
            'ticketJson' => json_encode($tickets),
            'timelines' => $timelines
        ]);
    }
}
