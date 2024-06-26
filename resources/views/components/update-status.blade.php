<dialog id="my_modal_4" class="modal" wire:ignore.self>
    <div class="w-11/12 max-w-5xl modal-box">
        <h3 class="text-lg font-bold">Hello, input update process!</h3>
        <div class="flex flex-col gap-2">
            <form wire:submit='testing'>
                <label class="max-w-xs w-96 form-control">
                    <div class="label">
                        <span class="label-text">Select Status</span>
                        @error('status_id')
                            <span class="label-text-alt">{{ $message }}</span>
                        @enderror
                    </div>
                    <select class="select select-bordered @error('status_id') select-error @enderror" wire:model="status_id">
                        <option selected value="null">Pick one</option>
                        @foreach ($statuses as $status)
                            {{-- <option value="{{ $status->id }}">{{ $status->name }}</option> --}}
                            @if (\App\Helpers\RoleHelper::isAdmin() )
                            <option value="{{ $status->id }}">{{ $status->name }}</option>

                            @elseif ($status->id === 1 || $status->id === 2)

                            @endif
                        @endforeach
                    </select>
                </label>
                
                <label class="max-w-xs w-96 form-control">
                    <div class="label">
                        <span class="label-text">Select Employee</span>
                        @error('employe_id')
                            <span class="label-text-alt">{{ $message }}</span>
                        @enderror
                    </div>
                    <select class="select select-bordered @error('employe_id') select-error @enderror" wire:model="employe_id">
                        <option selected value="null">Pick one</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->username }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control">
                </label>
            </form>
        </div>
        <div class="modal-action">
            <button type="button" class="btn btn-neutral" wire:click='store'>update</button>
            <form method="dialog" class="flex gap-3">
                <!-- if there is a button, it will close the modal -->
                <button class="btn" wire:click="fresh">Close</button>
            </form>
        </div>
    </div>
</dialog>

