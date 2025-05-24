<!-- resources/views/employees/index.blade.php -->

<h2>Employee List</h2>

<form action="{{ route('employees.index') }}" method="GET">
    <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}">
    <button type="submit">Search</button>
</form>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($employees as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No employees found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
