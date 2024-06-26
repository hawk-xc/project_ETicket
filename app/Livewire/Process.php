<?php

namespace App\Livewire;

use App\Models\Device as ModelsDevice;
use App\Models\Proces;
use App\Models\User;
use App\Models\Status;
use App\Models\Ticket; // Import model Ticket
use App\View\Components\device;
use Clockwork\Request\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
// use Livewire\WithoutUrlPagination;
// use Livewire\WithPagination;

class Process extends Component
{
    public $notification_message = 'Status perbaikan diperbahrui!';

    // use WithPagination, WithoutUrlPagination;
    public $openModal = false;
    // public $action = '';
    public $status_id;
    public $description;
    public $proces_id;
    public $device_id;
    public $statusId;
    public $employe_id;
    public $sortBy = 'status';
    public $sortDirection = 'asc';
    public $limiter = 5;


    public function redirectToProcessPage()
    {
        return redirect()->route('process');
    }
    public function sortByStatus($status)
    {
        $this->sortBy = 'status';
        $this->statusId = $status;
    }

    public function sortByDate($direction)
    {
        $this->sortBy = 'date';
        $this->sortDirection = $direction;
    }

    public function fresh()
    {
        $this->reset(['status_id', 'employe_id', 'proces_id']);
        $this->reset('status_id');
        // $this->reset('employe_id');
        // $this->reset('proces_id');
        $this->openModal = false;
        // $this->action = '';
    }
    public function addLimit($limiter)
    {
        if (count(Proces::all()) > $this->limiter) {
            $this->limiter += $limiter;
        }
    }
    public function removeLimit($limiter)
    {
        $this->limiter -= $limiter;
    }

    public function render()
    {
        // $statuses = Status::get();
        $statuses = Status::get();
        $user = Auth::user();

        if ($user->role_id === 1) {
            // if ($this->sortBy === 'date') {
            //     $process = Proces::orderBy('created_at', $this->sortDirection)->limit($this->limiter);
            // }
            // if ($this->sortBy === 'status') {
            //     $process = Proces::where('status_id', $this->sortDirection)->latest()->limit($this->limiter);
            //     dd($this->statusId);
            //     // dd($process);
            // } else {
            //     $process = Proces::latest()->limit($this->limiter);
            // }
            if ($this->sortBy === 'date') {
                $process = Proces::orderBy('created_at', $this->sortDirection)->limit($this->limiter);
            } elseif ($this->sortBy === 'status' && $this->statusId !== null) {
                $process = Proces::where('status_id', $this->statusId)->orderBy('id', 'desc')->limit($this->limiter);
            } else {
                $process = Proces::orderBy('id', 'desc')->limit($this->limiter);
            }
        } elseif ($user->role_id === 2) {
            if ($this->sortBy === 'date') {
                $process = Proces::orderBy('created_at', $this->sortDirection)->limit($this->limiter);
            } elseif ($this->sortBy === 'status' && $this->statusId !== null) {
                $process = Proces::where('status_id', $this->statusId)->orderBy('id', 'desc')->limit($this->limiter);
            } else {
                $process = Proces::orderBy('id', 'asc')->orderBy('id', 'desc')->limit($this->limiter);
            }
            $process = Proces::orderBy('id', 'asc')->latest()->limit($this->limiter);
        } else {
            $process = Proces::where('user_id', Auth::user()->id)->orderBy('id', 'asc')->latest()->limit($this->limiter);
        }
        $employees = User::where('role_id', '3')->get();
        $task = Proces::where('user_id', Auth::user()->id)->get();
        // dd($statuses);
        return view('livewire.process', compact('process', 'employees', 'user', 'statuses', 'task'));
    }

    public function create(): void
    {
        $this->validate([
            'status_id' => 'required',
            'employe_id' => 'required'
        ]);
        $process = Process::findOrFail($this->id);
        $employee = User::findOrFail($this->id);
    }

    public function edit(int $id)
    {
        // $this->action = 'edit';
        $this->openModal = true;
        $process = Proces::find($id);
        $this->proces_id = $process->id;
        $this->status_id = $process->status_id;
        $this->employe_id = $process->user_id;
    }

    public function processed($id)
    {

        Proces::find($id)->update(['status_id' => 3]);
        // find($this->device_id);
        $proces = Proces::find($id);
        $this->dispatch('notify', type: 'success', message: $this->notification_message);
        event(new \App\Events\UserInteraction(Auth::user(), "Proces => update proces {$proces->ticket->device->device_name} with id " . $proces->id));
        $this->fresh();
        $this->dispatch('closeButton');
    }



    public function done($id)
    {
        if (Proces::find($id)->update(['status_id' => 4])) {
            $this->dispatch('notify', type: 'success', message: $this->notification_message);

            $this->fresh();
        }

        // \App\Jobs\MailerJob::dispatch('wahyutricahyono777@gmail.com', 'wahyu', 'done', \App\Models\Ticket::first());

        $this->dispatch('closeButton');
    }

    public function store()
    {

        $data = $this->validate([
            'status_id' => 'required',
        ]);
        $data['user_id'] = $this->employe_id;
        $proces = Proces::find($this->proces_id);

        $ticket = Proces::find($this->proces_id)->ticket;

        $blueprint_message = "<p>Hallo User</p><br><p>Terima kasih atas kesabaran dan pengertian Anda selama kami menangani permintaan perbaikan Anda.</p><br><p>Kami ingin menginformasikan bahwa untuk saat ini, proses perbaikan telah <b>selesai</b></p><br><p>Mohon untuk saat ini perangkat diambil ditoko.</p><br><p>Apabila Anda memiliki pertanyaan lebih lanjut atau memerlukan bantuan lainnya, jangan ragu untuk menghubungi kami melalui virtual chat ini.</p><br><p>Terima kasih atas pengertian dan kerjasamanya.</p><br><p>Salam hormat, Fitri</p><br><p>Helpdesk E-Service</p>";

        \App\Models\Notification::create(
            [
                'user_id' => $ticket->device->user->id,
                'message' => $blueprint_message,
                'is_read' => 0,
                'is_user' => 0
            ]
        );

        // run with php artisan queue:work to operate job
        \App\Jobs\MailerJob::dispatch($ticket->device->user->email, $ticket->device->user->name, 'done', $ticket);

        if ($proces->update($data)) {
            $this->fresh();
            $this->dispatch('closeButton');
            $this->dispatch('notify', type: 'success', message: $this->notification_message);
            event(new \App\Events\UserInteraction(Auth::user(), "Process => update proces from customer {$proces->ticket->device->user->name} with id " . $proces->id));
            // redirect('/process')->dispatch('notify', type: 'success', message: 'Data successfully updated!'));
            // session()->flash('message', 'Data successfully updated broww!');
            redirect('/process');
            // redirect('/process')->with('message', 'Data successfully updated broww!');
            // redirect('/process');
            // $this->dispatch('notify', type: 'success', message: 'Data successfully updated!');
            // $this->dispatch('closeButton');
        }
    }
}
