@extends('layouts.app')

@section('content')



<div class="container mt-2">

    <div class="row">

        <div class="col-md-12">



            @if (session('status'))

                <div class="alert alert-success">{{ session('status') }}</div>

            @endif



            <div class="card data-card mt-3">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>

                        <h3 class="mb-0">User Management</h3>

                    </div>

                    <div>

                        <a href="{{ route('admin.user.export') }}" class="btn btn-outline-secondary btn-sm">Export Users</a>

                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importUsersModal">

                            <i class="tf-icons ri-upload-line"></i> Import Users

                        </button>

                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add User</button>



                        <!-- Import Users Modal -->

                        <div class="modal fade" id="importUsersModal" tabindex="-1">

                            <div class="modal-dialog">

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error:</strong> {{ $errors->first('csv_file') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('admin.user.import') }}" method="POST" enctype="multipart/form-data">

                                @csrf

                                <div class="modal-content">

                                    <div class="modal-header">

                                    <h5 class="modal-title">Import Users (.xlsx)</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                    </div>

                                    <div class="modal-body">

                                    <input type="file" name="csv_file" accept=".xlsx,.xls" required class="form-control">

                                    <div class="mt-2 text-muted">

                                        Expected columns: <code>name</code>, <code>email</code>, <code>password</code>, <code>role</code><br>
                                        Allowed roles: <code>Peon</code>, <code>Member</code>, <code>Receptionist</code><br>
                                        <a href="{{ asset('sample/user_import_sample.xlsx') }}" class="text-decoration-underline" download>Download sample file</a>

                                    </div>

                                    </div>

                                    <div class="modal-footer">

                                    <button type="submit" class="btn btn-success">Import</button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                                    </div>

                                </div>

                                </form>

                            </div>

                        </div>
                        @if ($errors->has('csv_file'))
                            <script>
                                window.addEventListener('load', function () {
                                    let modal = new bootstrap.Modal(document.getElementById('importUsersModal'));
                                    modal.show();
                                });
                            </script>
                        @endif

                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table" style="table-layout:fixed">

                            <thead>

                                <tr>
                                    <th>Sl. No.</th>

                                    <th>Name</th>

                                    <th>Email</th>

                                    <th>Roles</th>

                                    <th>Status</th>

                                    <th class="action_btn">Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($users as $user)

                                    <tr>

                                        <td>{{ $loop->iteration }}</td>

                                        <td>{{ ucwords($user->name) }}</td>

                                        <td>{{ $user->email }}</td>

                                        <td>{{ $user->role }}</td>

                                        <td>

                                            <div class="form-check form-switch">

                                                <input type="checkbox"

                                                    class="form-check-input status-toggle"

                                                    data-id="{{ $user->id }}"

                                                    {{ $user->status ? 'checked' : '' }}>

                                            </div>

                                        </td>

                                        <td>

                                            <button class="btn btn-sm btn-outline-primary edit-user-btn" 

                                                data-id="{{ $user->id }}"

                                                data-name="{{ $user->name }}"

                                                data-email="{{ $user->email }}"

                                                data-role="{{ $user->role }}"

                                                data-team-id="{{ optional($user->team->first())->id }}"

                                                data-bs-toggle="tooltip" data-bs-title="Edit">

                                                <i class="fa fa-pen"></i></button>

                                            <button class="btn btn-sm btn-outline-danger delete-user-btn" data-id="{{ $user->id }}" data-bs-toggle="tooltip" data-bs-title="Delete"><i class="fa fa-trash"></i></button>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="d-flex justify-content-end">

                            {{ $users->appends($_GET)->links() }}

                        </div>

                    </div>

                </div>

                <div class="modal fade" id="addUserModal" tabindex="-1">

                    <div class="modal-dialog">

                        <form method="POST" action="{{ route('admin.user.store') }}">

                        @csrf

                        <div class="modal-content">

                            <div class="modal-header">

                            <h5 class="modal-title">Add New User</h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                            </div>

                            <div class="modal-body">

                            <div class="mb-3">

                                <label>Name</label>

                                <input type="text" name="name" class="form-control" required>

                            </div>

                            <div class="mb-3">

                                <label>Email</label>

                                <input type="email" name="email" class="form-control" required>

                            </div>

                            <div class="mb-3">

                                <label>Password</label>

                                <input type="password" name="password" class="form-control" required>

                            </div>



                            <div class="mb-3">

                                <label>Role</label>

                                <select name="role" class="form-select" id="userRole" required>

                                    <option value="">Select Role</option>

                                    <option value="Member">Member</option>

                                    <option value="Receptionist">Receptionist</option>

                                    <option value="Peon">Peon</option>

                                </select>

                            </div>



                            <div class="mb-3" id="teamDropdown" style="display: none;">

                                <label>Assign to Team</label>

                                <select name="team_id" class="form-select" id="team_id">

                                    <option value="">Select Team</option>

                                    @foreach($teams as $team)

                                        <option value="{{ $team->id }}">{{ ucwords($team->name) }}</option>

                                    @endforeach

                                </select>

                            </div>

                            </div>

                            <div class="modal-footer">

                            <button type="submit" class="btn btn-dark">Add User</button>

                            </div>

                        </div>

                        </form>

                    </div>

                </div>

                <div class="modal fade" id="editUserModal" tabindex="-1">

                    <div class="modal-dialog">

                        <form id="editUserForm" method="POST">

                            @csrf

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">Edit User</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                                </div>

                                <div class="modal-body">

                                    <input type="hidden" name="user_id" id="editUserId">

                                    <div class="mb-3">

                                        <label>Name</label>

                                        <input type="text" name="name" id="editUserName" class="form-control" required>

                                    </div>

                                    <div class="mb-3">

                                        <label>Email</label>

                                        <input type="email" name="email" id="editUserEmail" class="form-control" required>

                                    </div>

                                    <div class="mb-3">

                                        <label>Role</label>

                                        <select name="role" id="editUserRole" class="form-select" required>

                                            <option value="Member">Member</option>

                                            <option value="Receptionist">Receptionist</option>

                                            <option value="Peon">Peon</option>

                                        </select>

                                    </div>



                                    <div class="mb-3">

                                        <label>Assign to Team</label>

                                        <select name="team_id" id="editTeamId" class="form-select">

                                            <option value="">Select Team</option>

                                            @foreach($teams as $team)

                                                <option value="{{ $team->id }}">{{ ucwords($team->name) }}</option>

                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="submit" class="btn btn-dark">Update User</button>

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

    $(document).ready(function () {

        $('.status-toggle').on('change', function () {

            const userId = $(this).data('id');

            $.post("{{ route('admin.user.toggle', ['id' => '__id__']) }}".replace('__id__', userId), {

                _token: '{{ csrf_token() }}'

            }, function (res) {

                toastFire(res.status === true ? 'success' : 'error', res.message);

            });

        });

        $('.delete-user-btn').on('click', function (e) {

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

                    $.post("{{ route('admin.user.delete', ['id' => '__id__']) }}".replace('__id__', userId), {

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

        $('#addUserModal form').on('submit', function (e) {

            e.preventDefault();

            const form = $(this);



            $.ajax({

                type: 'POST',

                url: form.attr('action'),

                data: form.serialize(),

                success: function (res) {

                    if (res.status) {

                        toastFire('success', res.message);

                        $('#addUserModal').modal('hide');

                        form[0].reset();

                        setTimeout(() => location.reload(), 1000);

                    } else {

                        toastFire('error', res.message);

                    }

                },

                error: function (xhr) {

                    let err = xhr.responseJSON?.message || 'Something went wrong.';

                    toastFire('error', err);

                }

            });

        });



        // $('.edit-user-btn').on('click', function(){

        //     const id = $(this).data('id');



        //     $('#editUserId').val(id);

        //     $('#editUserName').val($(this).data('name'));

        //     $('#editUserEmail').val($(this).data('email'));

        //     $('#editUserRole').val($(this).data('role'));



        //     $('#editUserModal').modal('show');



        // });

        $('.edit-user-btn').on('click', function () {

            const id = $(this).data('id');



            $('#editUserId').val(id);

            $('#editUserName').val($(this).data('name'));

            $('#editUserEmail').val($(this).data('email'));

            $('#editUserRole').val($(this).data('role')).trigger('change');



            // Set team if role is Member

            if ($(this).data('role') === 'Member') {

                $('#editTeamId').val($(this).data('team-id'));

                $('#editTeamId').parent().show();

            } else {

                $('#editTeamId').val('');

                $('#editTeamId').parent().hide();

            }



            $('#editUserModal').modal('show');

        });





        $('#editUserForm').on('submit', function (e) {

            e.preventDefault();



            const id = $('#editUserId').val();

            const formData = $(this).serialize();



            $.ajax({

                type: 'POST',

                url: "{{ route('admin.user.update', ['id' => '__id__']) }}".replace('__id__', id),

                data: formData,

                success: function (res) {

                    if (res.status) {

                        toastFire('success', res.message);

                        $('#editUserModal').modal('hide');

                        setTimeout(() => location.reload(), 1000);

                    } else {

                        toastFire('error', res.message);

                    }

                },

                error: function (xhr) {

                    let err = xhr.responseJSON?.message || 'Something went wrong.';

                    toastFire('error', err);

                }

            });

        });



        $('#userRole').on('change', function () {

            const role = $(this).val();

            if (role === 'Member') {

                $('#teamDropdown').slideDown();

            } else {

                $('#teamDropdown').slideUp();

                $('#teamDropdown select').val('');

            }

        });



        $('#editUserRole').on('change', function () {

            if ($(this).val() === 'Member') {

                $('#editTeamId').parent().show();

            } else {

                $('#editTeamId').parent().hide().find('select').val('');

            }

        });





    });



</script>

@endsection

