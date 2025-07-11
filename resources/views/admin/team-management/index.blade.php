@extends('layouts.app')
@section('content')

<div class="container mt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card data-card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Team Management</h3>
                    </div>
                    <div>
                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#teamModal">Add Team</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Team Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $team)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucwords($team->name) }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                            class="form-check-input status-toggle"
                                            data-id="{{ $team->id }}"
                                            {{ $team->status ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary editTeamBtn" data-id="{{ $team->id }}">
                                            <i class="fa fa-pen"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger deleteTeamBtn" data-id="{{ $team->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            
                <div class="modal fade" id="teamModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="teamForm">
                            @csrf
                            <input type="hidden" name="team_id" id="team_id">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Team</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="team_name">Team Name</label>
                                        <input type="text" class="form-control" name="name" id="team_name" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-dark">Save Team</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="editTeamModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form id="editTeamForm">
                            @csrf
                            <input type="hidden" name="team_id" id="edit_team_id">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Team</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Team Name</label>
                                        <input type="text" name="name" id="edit_team_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-dark" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
   
    $(document).ready (function (){
        $('#teamForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("admin.team.store") }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    //toastFire('success', res.message);
                    if (res.status) {
                        toastFire('success', res.message);
                        $('#teamForm').modal('hide');
                        location.reload();
                    } else {
                        toastFire('error', res.message);
                    }
                }
            });
        });  

        
        $('.editTeamBtn').on('click', function () {
            const teamId = $(this).data('id');

            $.get("{{ route('admin.team.edit', ['id' => '__id__']) }}".replace('__id__', teamId), function (res) {
                if (res.status) {
                    $('#edit_team_id').val(res.data.id);
                    $('#edit_team_name').val(res.data.name);
                    $('#editTeamModal').modal('show');
                } else {
                    toastFire('error', res.message || 'Team not found');
                }
            });
        });


     
        $('#editTeamForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.team.update') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        toastFire('success', res.message);
                        $('#editTeamModal').modal('hide');
                        location.reload();
                    } else {
                        toastFire('error', res.message);
                    }
                }
            });
        });


        $('.status-toggle').on('change', function () {
            const userId = $(this).data('id');
            $.post("{{ route('admin.team.toggle', ['id' => '__id__']) }}".replace('__id__', userId), {
                _token: '{{ csrf_token() }}'
            }, function (res) {
                toastFire(res.status === true ? 'success' : 'error', res.message);
            });
        });

        $('.deleteTeamBtn').on('click', function (e) {
            e.preventDefault();
            const btn = $(this);
            const userId = btn.data('id');

            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('admin.team.delete', ['id' => '__id__']) }}".replace('__id__', userId), {
                        _token: '{{ csrf_token() }}'
                    }, function (res) {
                        if (res.status) {
                            toastFire('success', res.message);
                            btn.closest('tr').remove(); 
                        } else {
                            toastFire('error', res.message);
                        }
                    });
                }
            });
        });
    });


</script>
@endsection