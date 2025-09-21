<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .filters {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .text-muted { color: #6c757d; }
        .small { font-size: 10px; }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Users Report</h1>
        <p>Generated on: {{ date('F j, Y \\a\\t g:i A') }}</p>
    </div>

    @if($request->name || $request->role || $request->email)
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->name)
            <p><strong>Name:</strong> {{ $request->name }}</p>
        @endif
        @if($request->role)
            <p><strong>Role:</strong> {{ $request->role }}</p>
        @endif
        @if($request->email)
            <p><strong>Email:</strong> {{ $request->email }}</p>
        @endif
    </div>
    @endif

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Users:</strong> {{ $users->count() }}</p>
        @foreach($systemroles as $role)
            <p><strong>{{ $role->role }}:</strong> {{ $users->where('role', $role->role)->count() }}</p>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Position</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-primary">{{ $user->role }}</span>
                    </td>
                    <td>{{ $user->position ?? 'N/A' }}</td>
                    <td>
                        @if($user->status == 'A')
                            <span class="badge badge-success">Active</span>
                        @elseif($user->status == 'L')
                            <span class="badge badge-warning">Locked</span>
                        @elseif($user->status == 'D')
                            <span class="badge badge-danger">Deleted</span>
                        @else
                            <span class="badge badge-secondary">{{ $user->status }}</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at ? $user->created_at->format('M j, Y') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #6c757d;">
        <p>This report was generated from the Learning Management System</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>