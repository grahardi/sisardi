@php $u = $user ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $u->name ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $u->email ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Password {{ $u ? '(kosongkan jika tidak ingin mengubah)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ $u ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" id="roleSelect" class="form-select" required>
            <option value="petugas" {{ old('role', $u->role ?? '') == 'petugas' ? 'selected' : '' }}>Petugas</option>
            <option value="superadmin" {{ old('role', $u->role ?? '') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
        </select>
    </div>
    <div class="col-12" id="permissionBox">
        <label class="form-label d-block">Hak Akses Fitur (khusus Petugas)</label>
        @foreach($availablePermissions as $key => $label)
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}"
                    {{ in_array($key, old('permissions', $u->permissions ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>
