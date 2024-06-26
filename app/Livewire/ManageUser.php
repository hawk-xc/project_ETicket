<?php

namespace App\Livewire;

// model definition
use \App\Models\User as UserModel;
use \App\Models\Device as DeviceModel;
use \App\Models\Ticket as TicketModel;
use \App\Models\Proces as ProcesModel;

// component support
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ManageUser extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $search = '';
    public $sortby = 'username';
    public $delete_id;
    public $userpanel = true;
    public $loadCount = 5;
    public $role_id = '', $user_id = '', $name = '', $date = '', $mail = '', $username = '', $phone = '', $user_image = '';

    public function loadMore(int $int): void
    {
        $this->loadCount += $int;
    }

    public function loadLess(int $int): void
    {
        $this->loadCount -= $int;
    }

    public function loadAll(): void
    {
        $this->loadCount = UserModel::where('role_id', '=', 4)->count();
    }

    public function loadAllLess(): void
    {
        $this->loadCount = 5;
    }

    public function userPanel(): void
    {
        $this->userpanel = true;
    }
    public function userPanelClient(): void
    {
        $this->userpanel = false;
    }

    protected $listeners = [
        'confirmDelete' => 'deleteUser',
        'updateToTechnician' => 'userTechnician',
        'updateToHelpdesk' => 'userHelpdesk',
    ];

    public function fresh()
    {
        $this->reset('user_id', 'name', 'date', 'mail', 'username', 'phone', 'user_image');
    }

    public function redirecting()
    {
        return redirect()->to('/register');
    }

    public function detail(int $id): void
    {
        $this->fresh();
        $user = UserModel::find($id);
        $this->user_id = $user->id;
        $this->role_id = $user->role_id;
        $this->name = $user->name;
        $this->date = $user->created_at->diffForHumans();
        $this->mail = $user->email;
        $this->phone = $user->phone;
        $this->user_image = $user->user_image;
        $this->username = $user->username;
        // $this->user_data['name'] = UserModel::find($id)->name;
        // $this->user_data['username'] = UserModel::find($id)->username;
    }

    public function userTechnician()
    {
        $user = UserModel::find($this->user_id);
        if ($user->update(['role_id' => 3])) {
            $this->dispatch('notify', type: 'success', message: 'data successfully updated!');
            event(new \App\Events\UserInteraction(Auth::user(), "User => update user " . $user->name . " with id " . $user->id . " to Technician"));
        }
        $this->fresh();
    }

    public function userHelpdesk()
    {
        $user = UserModel::find($this->user_id);
        if ($user->update(['role_id' => 2])) {
            $this->dispatch('notify', type: 'success', message: 'data successfully updated!');
            event(new \App\Events\UserInteraction(Auth::user(), "User => update user " . $user->name . " with id " . $user->id . " to Helpdesk"));
        }
        $this->fresh();
    }

    public function makeTechnician(): void
    {
        $user = UserModel::find($this->user_id);
        $this->dispatch('closeButton');
        $this->dispatch('confirmation', ['user' => $user, 'type' => 'Technician', 'message' => 'Technician can update process and cancel process', 'response' => 'updateToTechnician']);
    }

    public function makeHelpdesk(): void
    {
        $user = UserModel::find($this->user_id);
        $this->dispatch('closeButton');
        $this->dispatch('confirmation', ['user' => $user, 'type' => 'Helpdesk', 'message' => 'The helpdesk can schedule processes, update processes and reject processes', 'response' => 'updateToHelpdesk']);
    }

    public function sortname()
    {
        $this->sortby = 'name';
    }

    public function sortdate()
    {
        $this->sortby = 'created_at';
    }
    public function deleteConfirmation(int $id): void
    {
        $this->delete_id = $id;
        $this->dispatch('show-delete');
        $this->dispatch('closeButton');
    }


    public function deleteUser(): void
    {
        $user = UserModel::find($this->delete_id);
        // $user = \App\Models\User::find(4);


        $device = DeviceModel::whereIn('user_id', $user->pluck('id'))->get();
        // $device = \App\Models\Device::whereIn('user_id', $user->pluck('id'))->get();


        $ticket = TicketModel::whereIn('device_id', $device->pluck('id'))->get();
        // $ticket = \App\Models\Ticket::whereIn('device_id', $device->pluck('id'))->get();


        $proces = ProcesModel::whereIn('ticket_id', $ticket->pluck('id'))->get();
        // $proces = \App\Models\Proces::whereIn('ticket_id', $ticket->pluck('id'))->get();

        if (!$proces->isEmpty()) {
            foreach ($proces as $data) {
                $data->delete();
            }
        }

        if (!$ticket->isEmpty()) {
            foreach ($ticket as $data) {
                $data->delete();
            }
        }

        if (!$device->isEmpty()) {
            foreach ($device as $data) {
                $data->delete();
            }
        }

        if ($user->delete()) {
            $this->dispatch('notify', type: 'success', message: 'data successfully deleted!');
            event(new \App\Events\UserInteraction(Auth::user(), "User => delete user " . $user->name . " with id " . $user->id));
        }
    }

    public function render()
    {
        $definition = [
            'coreUsers' => UserModel::orderBy('role_id', 'asc')->whereIn('role_id', [1, 2, 3])->get(),
            'clientUsers' => UserModel::orderBy($this->sortby, 'asc')->where('role_id', '=', 4)->where(
                function ($query) {
                    $query->where('username', 'like', '%' . $this->search . '%')->orWhere('name', 'like', '%' . $this->search . '%');
                }
            )->limit($this->loadCount)->get(),
            'testing' => UserModel::where('role_id', '=', '4')->get()
        ];

        return view('livewire.manage-user', $definition);
    }
}
